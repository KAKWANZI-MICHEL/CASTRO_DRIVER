<?php
// Error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database configuration
class Database {
    public $host = "localhost";
    public $db_name = "castro";
    public $username = "root";
    public $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $e) {
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ]));
        }
        return $this->conn;
    }
}

class DriverAPI {
    public $conn;
    public $earth_radius = 6371;
    
    // Encryption key and method - in production, store this securely
    public $encryption_key = 'castrotransporters!@#$%^&*()';
    public $cipher_method = 'AES-256-CBC';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Encrypt data using AES-256
     */
    public function encrypt($data) {
        $key = hash('sha256', $this->encryption_key, true);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, $this->cipher_method, $key, OPENSSL_RAW_DATA, $iv);
        
        $result = base64_encode($encrypted) . ':' . base64_encode($iv);
        return $result;
    }

    /**
     * Decrypt data using AES-256
     */
    public function decrypt($data) {
        $key = hash('sha256', $this->encryption_key, true);
        
        $parts = explode(':', $data);
        if (count($parts) !== 2) {
            return false;
        }
        
        $encrypted = base64_decode($parts[0]);
        $iv = base64_decode($parts[1]);
        
        if (strlen($iv) !== 16) {
            return false;
        }
        
        return openssl_decrypt($encrypted, $this->cipher_method, $key, OPENSSL_RAW_DATA, $iv);
    }

    // ==================== DRIVER AUTHENTICATION ====================
    
    /**
     * Register a new driver
     */
    public function registerDriver($data) {
        try {
            $required = ['name', 'email', 'password', 'DOB', 'vehicle_type', 'vehicle_model', 'plate_number'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return $this->response(false, "Missing required field: $field");
                }
            }
            
            $this->conn->beginTransaction();
            
            // Check if driver already exists
            $check_stmt = $this->conn->prepare("SELECT id FROM drivers WHERE email = ?");
            $check_stmt->execute([$data['email']]);
            
            if ($check_stmt->rowCount() > 0) {
                $this->conn->rollBack();
                return $this->response(false, "Driver with this email already exists");
            }
            
            // Encrypt the password before storing
            $encrypted_password = $this->encrypt($data['password']);
            
            // Create driver account
            $driver_stmt = $this->conn->prepare("
                INSERT INTO drivers (name, email, password, DOB) 
                VALUES (?, ?, ?, ?)
            ");
            
            $driver_stmt->execute([
                $data['name'],
                $data['email'],
                $encrypted_password,
                $data['DOB']
            ]);
            
            $driver_id = $this->conn->lastInsertId();
            
            // Add vehicle information
            $vehicle_stmt = $this->conn->prepare("
                INSERT INTO driver_vehicles 
                (driver_id, vehicle_type, vehicle_model, vehicle_color, plate_number, is_verified)
                VALUES (?, ?, ?, ?, ?, 0)
            ");
            
            $vehicle_stmt->execute([
                $driver_id,
                $data['vehicle_type'],
                $data['vehicle_model'],
                $data['vehicle_color'] ?? null,
                $data['plate_number']
            ]);
            
            // Initialize driver status
            $status_stmt = $this->conn->prepare("
                INSERT INTO driver_status (driver_id, is_online, is_available, current_latitude, current_longitude)
                VALUES (?, 0, 0, NULL, NULL)
            ");
            $status_stmt->execute([$driver_id]);
            
            // Initialize driver stats
            $stats_stmt = $this->conn->prepare("
                INSERT INTO driver_stats (driver_id, total_rides, total_earnings, rating, total_ratings)
                VALUES (?, 0, 0, 0, 0)
            ");
            $stats_stmt->execute([$driver_id]);
            
            $this->conn->commit();
            
            return $this->response(true, "Driver registered successfully", [
                'id' => $driver_id,
                'name' => $data['name'],
                'email' => $data['email']
            ]);
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return $this->response(false, "Registration failed: " . $e->getMessage());
        }
    }
    
    /**
     * Driver login
     */
    public function driverLogin($data) {
        try {
            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->response(false, "Email and password required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT d.*, 
                       dv.vehicle_type, dv.vehicle_model, dv.plate_number, dv.vehicle_color,
                       ds.is_online, ds.is_available, ds.current_latitude, ds.current_longitude,
                       dst.rating, dst.total_rides, dst.total_earnings, dst.acceptance_rate
                FROM drivers d
                LEFT JOIN driver_vehicles dv ON d.id = dv.driver_id
                LEFT JOIN driver_status ds ON d.id = ds.driver_id
                LEFT JOIN driver_stats dst ON d.id = dst.driver_id
                WHERE d.email = ?
            ");
            
            $stmt->execute([$data['email']]);
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($driver) {
                // Decrypt the stored password and compare
                $decrypted_password = $this->decrypt($driver['password']);
                
                if ($decrypted_password === $data['password']) {
                    unset($driver['password']);
                    
                    // Update online status
                    $updateStmt = $this->conn->prepare("
                        UPDATE driver_status SET is_online = 1 WHERE driver_id = ?
                    ");
                    $updateStmt->execute([$driver['id']]);
                    
                    $driver['is_online'] = 1;
                    
                    return $this->response(true, "Login successful", ['user' => $driver]);
                }
            }
            
            return $this->response(false, "Invalid email or password");
            
        } catch (Exception $e) {
            return $this->response(false, "Login failed: " . $e->getMessage());
        }
    }
    
    /**
     * Driver logout
     */
    public function driverLogout($data) {
        try {
            if (!isset($data['driver_id'])) {
                return $this->response(false, "Driver ID required");
            }
            
            $stmt = $this->conn->prepare("
                UPDATE driver_status 
                SET is_online = 0, is_available = 0 
                WHERE driver_id = ?
            ");
            
            $stmt->execute([$data['driver_id']]);
            
            return $this->response(true, "Logged out successfully");
            
        } catch (Exception $e) {
            return $this->response(false, "Logout failed: " . $e->getMessage());
        }
    }

    // ==================== DRIVER STATUS & LOCATION ====================

    /**
     * Update driver location
     */
    public function updateLocation($data) {
        try {
            if (!isset($data['driver_id']) || !isset($data['latitude']) || !isset($data['longitude'])) {
                return $this->response(false, "Driver ID and location required");
            }
            
            $this->conn->beginTransaction();
            
            // Update driver's current location
            $stmt = $this->conn->prepare("
                UPDATE driver_status 
                SET current_latitude = ?, current_longitude = ?, 
                    last_location_update = NOW()
                WHERE driver_id = ?
            ");
            
            $stmt->execute([
                $data['latitude'],
                $data['longitude'],
                $data['driver_id']
            ]);
            
            // Save to location history
            $history_stmt = $this->conn->prepare("
                INSERT INTO driver_location_history (driver_id, latitude, longitude)
                VALUES (?, ?, ?)
            ");
            
            $history_stmt->execute([
                $data['driver_id'],
                $data['latitude'],
                $data['longitude']
            ]);
            
            $this->conn->commit();
            
            return $this->response(true, "Location updated successfully");
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return $this->response(false, "Location update failed: " . $e->getMessage());
        }
    }
    
    /**
     * Toggle driver availability
     */
    public function toggleAvailability($data) {
        try {
            if (!isset($data['driver_id']) || !isset($data['is_available'])) {
                return $this->response(false, "Driver ID and availability status required");
            }
            
            $stmt = $this->conn->prepare("
                UPDATE driver_status 
                SET is_available = ? 
                WHERE driver_id = ?
            ");
            
            $stmt->execute([
                $data['is_available'],
                $data['driver_id']
            ]);
            
            $status = $data['is_available'] ? 'available' : 'unavailable';
            return $this->response(true, "You are now $status for new trips");
            
        } catch (Exception $e) {
            return $this->response(false, "Update failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get driver stats
     */
    public function getDriverStats($driver_id) {
        try {
            if (!$driver_id) {
                return $this->response(false, "Driver ID required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT ds.*, 
                       (SELECT COUNT(*) FROM rides WHERE driver_id = ? AND status = 'completed' AND DATE(completed_at) = CURDATE()) as today_rides,
                       (SELECT COALESCE(SUM(total_fare), 0) FROM rides WHERE driver_id = ? AND status = 'completed' AND DATE(completed_at) = CURDATE()) as today_earnings,
                       (SELECT COUNT(*) FROM rides WHERE driver_id = ? AND status = 'completed') as total_completed_rides,
                       (SELECT COALESCE(SUM(total_fare), 0) FROM rides WHERE driver_id = ? AND status = 'completed') as total_earnings_all_time
                FROM driver_stats ds
                WHERE ds.driver_id = ?
            ");
            
            $stmt->execute([$driver_id, $driver_id, $driver_id, $driver_id, $driver_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stats) {
                return $this->response(true, "Stats retrieved", ['stats' => $stats]);
            } else {
                return $this->response(false, "Driver stats not found");
            }
            
        } catch (Exception $e) {
            return $this->response(false, "Error: " . $e->getMessage());
        }
    }

    // ==================== RIDE MANAGEMENT ====================

/**
 * Get available trips for driver (FIXED — NO HY093 ERROR)
 */
public function getAvailableTrips($data) {
    try {
        if (
            !isset($data['driver_id']) ||
            !isset($data['vehicle_type']) ||
            !isset($data['latitude']) ||
            !isset($data['longitude'])
        ) {
            return $this->response(false, "Missing required parameters");
        }

        $driver_id   = (int)$data['driver_id'];
        $vehicle     = $data['vehicle_type'];
        $driver_lat  = (float)$data['latitude'];
        $driver_lng  = (float)$data['longitude'];
        $radius      = isset($data['radius']) ? (float)$data['radius'] : 50;

        $sql = "
            SELECT
                r.id,
                r.passenger_id,
                p.name AS passenger_name,
                r.pickup_latitude,
                r.pickup_longitude,
                r.pickup_address,
                r.dropoff_latitude,
                r.dropoff_longitude,
                r.dropoff_address,
                r.distance_km,
                r.total_fare,
                r.vehicle_type,
                r.estimated_duration_minutes,
                r.created_at,

                (
                    6371 * acos(
                        cos(radians(?))
                        * cos(radians(r.pickup_latitude))
                        * cos(radians(r.pickup_longitude) - radians(?))
                        + sin(radians(?))
                        * sin(radians(r.pickup_latitude))
                    )
                ) AS distance_to_pickup_km

            FROM rides r
            JOIN passengers p ON p.id = r.passenger_id

            WHERE
                r.status = 'searching'
                AND r.driver_id IS NULL
                AND r.vehicle_type = ?

            HAVING
                (
                    6371 * acos(
                        cos(radians(?))
                        * cos(radians(r.pickup_latitude))
                        * cos(radians(r.pickup_longitude) - radians(?))
                        + sin(radians(?))
                        * sin(radians(r.pickup_latitude))
                    )
                ) <= ?

            ORDER BY distance_to_pickup_km ASC
            LIMIT 20
        ";

        $stmt = $this->conn->prepare($sql);

        // 🔥 EXACT PARAMETER ORDER — VERY IMPORTANT
        $stmt->execute([
            $driver_lat, // SELECT cos
            $driver_lng, // SELECT lng
            $driver_lat, // SELECT sin
            $vehicle,    // WHERE vehicle_type

            $driver_lat, // HAVING cos
            $driver_lng, // HAVING lng
            $driver_lat, // HAVING sin
            $radius      // HAVING radius
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $trips = [];
        foreach ($rows as $row) {
            $eta = round(($row['distance_to_pickup_km'] / 30) * 60);

            $trips[] = [
                'id' => (int)$row['id'],
                'passengerName' => $row['passenger_name'],
                'pickupLat' => (float)$row['pickup_latitude'],
                'pickupLng' => (float)$row['pickup_longitude'],
                'pickupAddress' => $row['pickup_address'],
                'dropoffLat' => (float)$row['dropoff_latitude'],
                'dropoffLng' => (float)$row['dropoff_longitude'],
                'dropoffAddress' => $row['dropoff_address'],
                'distance' => (float)$row['distance_km'],
                'fare' => (float)$row['total_fare'],
                'vehicleType' => $row['vehicle_type'],
                'estimatedDuration' => (int)$row['estimated_duration_minutes'],
                'etaToPickup' => $eta,
                'distanceFromDriver' => round($row['distance_to_pickup_km'], 2)
            ];
        }

        return $this->response(true, "Available trips found", [
            'trips' => $trips,
            'count' => count($trips)
        ]);

    } catch (Throwable $e) {
        return $this->response(false, "Error finding trips: " . $e->getMessage());
    }
}

    /**
     * Accept a ride
     */
    public function acceptRide($data) {
        try {
            if (!isset($data['ride_id']) || !isset($data['driver_id'])) {
                return $this->response(false, "Ride ID and Driver ID required");
            }
            
            $ride_id = $data['ride_id'];
            $driver_id = $data['driver_id'];
            
            // Check if ride is still available
            $check_stmt = $this->conn->prepare("
                SELECT id FROM rides 
                WHERE id = ? AND status = 'searching' AND driver_id IS NULL
                FOR UPDATE
            ");
            $check_stmt->execute([$ride_id]);
            
            if ($check_stmt->rowCount() == 0) {
                return $this->response(false, "This ride is no longer available");
            }
            
            // Update the ride
            $stmt = $this->conn->prepare("
                UPDATE rides 
                SET driver_id = ?, status = 'driver_assigned', assigned_at = NOW() 
                WHERE id = ? AND status = 'searching'
            ");
            
            $stmt->execute([$driver_id, $ride_id]);
            
            if ($stmt->rowCount() > 0) {
                // Update driver status to unavailable
                $this->conn->prepare("
                    UPDATE driver_status SET is_available = 0 WHERE driver_id = ?
                ")->execute([$driver_id]);
                
                return $this->response(true, "Ride accepted successfully", [
                    'ride_id' => $ride_id,
                    'status' => 'driver_assigned'
                ]);
            } else {
                return $this->response(false, "Failed to accept ride");
            }
            
        } catch (Exception $e) {
            return $this->response(false, "Accept ride failed: " . $e->getMessage());
        }
    }

    /**
     * Update ride status
     */
    public function updateRideStatus($data) {
        try {
            if (!isset($data['ride_id']) || !isset($data['driver_id']) || !isset($data['status'])) {
                return $this->response(false, "Ride ID, Driver ID, and status required");
            }
            
            $ride_id = $data['ride_id'];
            $driver_id = $data['driver_id'];
            $status = $data['status'];
            
            // Valid status transitions
            $valid_statuses = ['driver_assigned', 'driver_arrived', 'in_progress', 'completed', 'cancelled_by_driver'];
            
            if (!in_array($status, $valid_statuses)) {
                return $this->response(false, "Invalid status");
            }
            
            // Set the appropriate timestamp field
            $timestamp_field = '';
            switch($status) {
                case 'driver_arrived':
                    $timestamp_field = 'driver_arrived_at = NOW()';
                    break;
                case 'in_progress':
                    $timestamp_field = 'started_at = NOW()';
                    break;
                case 'completed':
                    $timestamp_field = 'completed_at = NOW()';
                    break;
                case 'cancelled_by_driver':
                    $timestamp_field = 'cancelled_at = NOW()';
                    break;
                default:
                    $timestamp_field = '';
            }
            
            // Update the ride
            if (!empty($timestamp_field)) {
                $stmt = $this->conn->prepare("
                    UPDATE rides 
                    SET status = ?, $timestamp_field 
                    WHERE id = ? AND driver_id = ?
                ");
                $stmt->execute([$status, $ride_id, $driver_id]);
            } else {
                $stmt = $this->conn->prepare("
                    UPDATE rides 
                    SET status = ? 
                    WHERE id = ? AND driver_id = ?
                ");
                $stmt->execute([$status, $ride_id, $driver_id]);
            }
            
            // If ride is completed, update driver stats
            if ($status === 'completed' && $stmt->rowCount() > 0) {
                // Get ride fare
                $fare_stmt = $this->conn->prepare("SELECT total_fare FROM rides WHERE id = ?");
                $fare_stmt->execute([$ride_id]);
                $ride = $fare_stmt->fetch();
                
                if ($ride) {
                    // Update driver stats
                    $this->conn->prepare("
                        UPDATE driver_stats 
                        SET total_rides = total_rides + 1,
                            total_earnings = total_earnings + ?
                        WHERE driver_id = ?
                    ")->execute([$ride['total_fare'], $driver_id]);
                }
                
                // Make driver available again
                $this->conn->prepare("
                    UPDATE driver_status SET is_available = 1 WHERE driver_id = ?
                ")->execute([$driver_id]);
            }
            
            // If ride is cancelled, make driver available again
            if ($status === 'cancelled_by_driver' && $stmt->rowCount() > 0) {
                $this->conn->prepare("
                    UPDATE driver_status SET is_available = 1 WHERE driver_id = ?
                ")->execute([$driver_id]);
            }
            
            if ($stmt->rowCount() > 0) {
                return $this->response(true, "Ride status updated to $status");
            } else {
                return $this->response(false, "Failed to update ride status");
            }
            
        } catch (Exception $e) {
            return $this->response(false, "Update failed: " . $e->getMessage());
        }
    }

    /**
     * Get ride details
     */
    public function getRide($ride_id) {
        try {
            if (!$ride_id) {
                return $this->response(false, "Ride ID required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                       p.name as passenger_name,
                       d.name as driver_name,
                       dv.vehicle_type, dv.vehicle_model, dv.plate_number,
                       dst.rating as driver_rating
                FROM rides r
                LEFT JOIN passengers p ON r.passenger_id = p.id
                LEFT JOIN drivers d ON r.driver_id = d.id
                LEFT JOIN driver_vehicles dv ON r.driver_id = dv.driver_id
                LEFT JOIN driver_stats dst ON r.driver_id = dst.driver_id
                WHERE r.id = ?
            ");
            
            $stmt->execute([$ride_id]);
            $ride = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($ride) {
                return $this->response(true, "Ride details found", ['ride' => $ride]);
            }
            
            return $this->response(false, "Ride not found");
            
        } catch (Exception $e) {
            return $this->response(false, "Error: " . $e->getMessage());
        }
    }

    /**
     * Get driver ride history (FIXED - removed p.phone)
     */
    public function getDriverHistory($driver_id) {
        try {
            if (!$driver_id) {
                return $this->response(false, "Driver ID required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                       p.name as passenger_name,
                       rr.rating,
                       rr.comment as rating_comment
                FROM rides r
                LEFT JOIN passengers p ON r.passenger_id = p.id
                LEFT JOIN ride_ratings rr ON r.id = rr.ride_id AND rr.passenger_id = r.passenger_id
                WHERE r.driver_id = ?
                ORDER BY r.created_at DESC
                LIMIT 50
            ");
            
            $stmt->execute([$driver_id]);
            $rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->response(true, "Ride history found", ['rides' => $rides]);
            
        } catch (Exception $e) {
            return $this->response(false, "Error: " . $e->getMessage());
        }
    }

    // ==================== HELPER FUNCTIONS ====================
    
    public function response($success, $message, $data = null) {
        return json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

// ==================== ROUTER ====================

if (!isset($_SERVER['REQUEST_METHOD'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$api = new DriverAPI();
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    switch ($action) {
        // Driver authentication
        case 'register_driver':
            echo $api->registerDriver($data);
            break;
            
        case 'driver_login':
            echo $api->driverLogin($data);
            break;
            
        case 'driver_logout':
            echo $api->driverLogout($data);
            break;
            
        // Driver status & location
        case 'update_location':
            echo $api->updateLocation($data);
            break;
            
        case 'toggle_availability':
            echo $api->toggleAvailability($data);
            break;
            
        // Ride management - THESE ARE THE ONES YOU NEED
        case 'get_available_trips':
            echo $api->getAvailableTrips($data);
            break;
            
        case 'accept_ride':
            echo $api->acceptRide($data);
            break;
            
        case 'update_ride_status':
            echo $api->updateRideStatus($data);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
    }
    
} elseif ($method === 'GET') {
    switch ($action) {
        case 'driver_stats':
            $driver_id = isset($_GET['driver_id']) ? $_GET['driver_id'] : null;
            echo $api->getDriverStats($driver_id);
            break;
            
        case 'get_ride':
            $ride_id = isset($_GET['ride_id']) ? $_GET['ride_id'] : null;
            echo $api->getRide($ride_id);
            break;
            
        case 'driver_history':
            $driver_id = isset($_GET['driver_id']) ? $_GET['driver_id'] : null;
            echo $api->getDriverHistory($driver_id);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed: ' . $method]);
}
?>