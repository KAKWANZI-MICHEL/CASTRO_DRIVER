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

class RideAPI {
    public $conn;
    public $earth_radius = 6371;
    
    // Encryption key and method - in production, store this securely (not in code)
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
    $iv = openssl_random_pseudo_bytes(16); // Force 16 bytes explicitly
    $encrypted = openssl_encrypt($data, $this->cipher_method, $key, OPENSSL_RAW_DATA, $iv);
    
    // Store IV as raw bytes in base64
    $result = base64_encode($encrypted) . ':' . base64_encode($iv);
    return $result;
}

/**
 * Decrypt data using AES-256
 */
public function decrypt($data) {
    $key = hash('sha256', $this->encryption_key, true);
    
    // Split the encrypted data and IV
    $parts = explode(':', $data);
    if (count($parts) !== 2) {
        return false;
    }
    
    $encrypted = base64_decode($parts[0]);
    $iv = base64_decode($parts[1]);
    
    // Verify IV is exactly 16 bytes
    if (strlen($iv) !== 16) {
        return false;
    }
    
    return openssl_decrypt($encrypted, $this->cipher_method, $key, OPENSSL_RAW_DATA, $iv);
}
    // ==================== PASSENGER FUNCTIONS ====================
    
    /**
     * Register a new passenger
     * Endpoint: POST /ride_api.php?action=register_passenger
     */
    public function registerPassenger($data) {
        try {
            $required = ['name', 'email', 'password', 'DOB'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return $this->response(false, "Missing required field: $field");
                }
            }
            
            // Check if passenger already exists
            $check_stmt = $this->conn->prepare("SELECT id FROM passengers WHERE email = ?");
            $check_stmt->execute([$data['email']]);
            
            if ($check_stmt->rowCount() > 0) {
                return $this->response(false, "Passenger with this email already exists");
            }
            
            // 🔐 ENCRYPT the password before storing
            $encrypted_password = $this->encrypt($data['password']);
            
            $stmt = $this->conn->prepare("
                INSERT INTO passengers (name, email, password, DOB) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['name'],
                $data['email'],
                $encrypted_password,  // Store encrypted password
                $data['DOB']
            ]);
            
            $passenger_id = $this->conn->lastInsertId();
            
            return $this->response(true, "Passenger registered successfully", [
                'id' => $passenger_id,
                'name' => $data['name'],
                'email' => $data['email'],
                'DOB' => $data['DOB']
            ]);
            
        } catch (Exception $e) {
            return $this->response(false, "Registration failed: " . $e->getMessage());
        }
    }
    
    /**
     * Passenger login
     * Endpoint: POST /ride_api.php?action=passenger_login
     */
    public function passengerLogin($data) {
        try {
            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->response(false, "Email and password required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT * FROM passengers WHERE email = ?
            ");
            
            $stmt->execute([$data['email']]);
            $passenger = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($passenger) {
                // 🔓 DECRYPT the stored password and compare
                $decrypted_password = $this->decrypt($passenger['password']);
                
                if ($decrypted_password === $data['password']) {
                    unset($passenger['password']);
                    return $this->response(true, "Login successful", ['user' => $passenger]);
                }
            }
            
            return $this->response(false, "Invalid email or password");
            
        } catch (Exception $e) {
            return $this->response(false, "Login failed: " . $e->getMessage());
        }
    }
    
    /**
     * Save passenger address
     * Endpoint: POST /ride_api.php?action=save_address
     */
    public function saveAddress($data) {
        try {
            $required = ['passenger_id', 'address_name', 'address_text', 'latitude', 'longitude'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return $this->response(false, "Missing required field: $field");
                }
            }
            
            $stmt = $this->conn->prepare("
                INSERT INTO passenger_addresses 
                (passenger_id, address_name, address_text, latitude, longitude, is_favorite)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['passenger_id'],
                $data['address_name'],
                $data['address_text'],
                $data['latitude'],
                $data['longitude'],
                isset($data['is_favorite']) ? $data['is_favorite'] : false
            ]);
            
            return $this->response(true, "Address saved successfully", [
                'address_id' => $this->conn->lastInsertId()
            ]);
            
        } catch (Exception $e) {
            return $this->response(false, "Failed to save address: " . $e->getMessage());
        }
    }
    
    /**
     * Get passenger addresses
     * Endpoint: GET /ride_api.php?action=get_addresses&passenger_id=123
     */
    public function getAddresses($passenger_id) {
        try {
            if (!$passenger_id) {
                return $this->response(false, "Passenger ID required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT * FROM passenger_addresses 
                WHERE passenger_id = ?
                ORDER BY is_favorite DESC, created_at DESC
            ");
            
            $stmt->execute([$passenger_id]);
            $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->response(true, "Addresses retrieved", ['addresses' => $addresses]);
            
        } catch (Exception $e) {
            return $this->response(false, "Error: " . $e->getMessage());
        }
    }
    
    // ==================== DRIVER FUNCTIONS ====================
    
    /**
     * Register a new driver
     * Endpoint: POST /ride_api.php?action=register_driver
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
            
            // 🔐 ENCRYPT the password before storing
            $encrypted_password = $this->encrypt($data['password']);
            
            // Create driver account
            $driver_stmt = $this->conn->prepare("
                INSERT INTO drivers (name, email, password, DOB) 
                VALUES (?, ?, ?, ?)
            ");
            
            $driver_stmt->execute([
                $data['name'],
                $data['email'],
                $encrypted_password,  // Store encrypted password
                $data['DOB']
            ]);
            
            $driver_id = $this->conn->lastInsertId();
            
            // Add vehicle information
            $vehicle_stmt = $this->conn->prepare("
                INSERT INTO driver_vehicles 
                (driver_id, vehicle_type, vehicle_model, vehicle_color, plate_number, is_verified)
                VALUES (?, ?, ?, ?, ?, 1)
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
                VALUES (?, 0, 1, NULL, NULL)
            ");
            $status_stmt->execute([$driver_id]);
            
            // Initialize driver stats
            $stats_stmt = $this->conn->prepare("
                INSERT INTO driver_stats (driver_id) VALUES (?)
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
     * Endpoint: POST /ride_api.php?action=driver_login
     */
    public function driverLogin($data) {
        try {
            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->response(false, "Email and password required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT d.*, dv.vehicle_type, dv.vehicle_model, dv.plate_number,
                       ds.is_online, ds.is_available, ds.current_latitude, ds.current_longitude,
                       dst.rating, dst.total_rides
                FROM drivers d
                LEFT JOIN driver_vehicles dv ON d.id = dv.driver_id
                LEFT JOIN driver_status ds ON d.id = ds.driver_id
                LEFT JOIN driver_stats dst ON d.id = dst.driver_id
                WHERE d.email = ?
            ");
            
            $stmt->execute([$data['email']]);
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($driver) {
                // 🔓 DECRYPT the stored password and compare
                $decrypted_password = $this->decrypt($driver['password']);
                
                if ($decrypted_password === $data['password']) {
                    unset($driver['password']);
                    return $this->response(true, "Login successful", ['user' => $driver]);
                }
            }
            
            return $this->response(false, "Invalid email or password");
            
        } catch (Exception $e) {
            return $this->response(false, "Login failed: " . $e->getMessage());
        }
    }
    
    /**
     * Update driver location
     * Endpoint: POST /ride_api.php?action=update_location
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
                    is_online = 1,
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
     * Endpoint: POST /ride_api.php?action=toggle_availability
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
            
            return $this->response(true, "Availability updated successfully");
            
        } catch (Exception $e) {
            return $this->response(false, "Update failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get driver stats
     * Endpoint: GET /ride_api.php?action=driver_stats&driver_id=123
     */
    public function getDriverStats($driver_id) {
        try {
            if (!$driver_id) {
                return $this->response(false, "Driver ID required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT ds.*, 
                       (SELECT COUNT(*) FROM rides WHERE driver_id = ? AND status = 'completed' AND DATE(completed_at) = CURDATE()) as today_rides,
                       (SELECT COALESCE(SUM(total_fare), 0) FROM rides WHERE driver_id = ? AND status = 'completed' AND DATE(completed_at) = CURDATE()) as today_earnings
                FROM driver_stats ds
                WHERE ds.driver_id = ?
            ");
            
            $stmt->execute([$driver_id, $driver_id, $driver_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $this->response(true, "Stats retrieved", ['stats' => $stats]);
            
        } catch (Exception $e) {
            return $this->response(false, "Error: " . $e->getMessage());
        }
    }
    
    // ==================== RIDE FUNCTIONS ====================
    
    /**
     * Find nearby drivers by vehicle type
     * Endpoint: POST /ride_api.php?action=find_drivers
     */
    public function findNearbyDrivers($data) {
        try {
            $required = ['pickup_lat', 'pickup_lng', 'vehicle_type'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return $this->response(false, "Missing required parameter: $field");
                }
            }
            
            $pickup_lat = floatval($data['pickup_lat']);
            $pickup_lng = floatval($data['pickup_lng']);
            $vehicle_type = $data['vehicle_type'];
            $radius = isset($data['radius']) ? floatval($data['radius']) : 10;
            
            // Haversine formula to find nearby drivers
            $query = "
                SELECT 
                    d.id,
                    d.name,
                    dv.vehicle_type,
                    dv.vehicle_model,
                    dv.plate_number,
                    dst.rating,
                    ds.current_latitude,
                    ds.current_longitude,
                    ds.is_available,
                    ( ? * acos( cos( radians(?) ) * cos( radians( ds.current_latitude ) ) 
                    * cos( radians( ds.current_longitude ) - radians(?) ) + sin( radians(?) ) 
                    * sin( radians( ds.current_latitude ) ) ) ) AS distance
                FROM drivers d
                INNER JOIN driver_vehicles dv ON d.id = dv.driver_id
                INNER JOIN driver_status ds ON d.id = ds.driver_id
                LEFT JOIN driver_stats dst ON d.id = dst.driver_id
                WHERE dv.vehicle_type = ?
                    AND ds.is_available = 1
                    AND ds.is_online = 1
                    AND ds.current_latitude IS NOT NULL
                HAVING distance < ?
                ORDER BY distance
                LIMIT 20
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $this->earth_radius,
                $pickup_lat,
                $pickup_lng,
                $pickup_lat,
                $vehicle_type,
                $radius
            ]);
            
            $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format the response
            $formatted_drivers = [];
            foreach ($drivers as $driver) {
                $eta_minutes = round(($driver['distance'] / 30) * 60);
                $fare = $this->calculateFare($vehicle_type, $driver['distance']);
                
                $formatted_drivers[] = [
                    'id' => $driver['id'],
                    'name' => $driver['name'],
                    'vehicle_type' => $driver['vehicle_type'],
                    'vehicle_model' => $driver['vehicle_model'],
                    'plate_number' => $driver['plate_number'],
                    'rating' => floatval($driver['rating']),
                    'distance' => round($driver['distance'], 2),
                    'eta' => $eta_minutes . ' min',
                    'fare' => $fare,
                    'location' => [
                        'lat' => floatval($driver['current_latitude']),
                        'lng' => floatval($driver['current_longitude'])
                    ]
                ];
            }
            
            return $this->response(true, "Drivers found", [
                'drivers' => $formatted_drivers,
                'count' => count($formatted_drivers)
            ]);
            
        } catch (Exception $e) {
            return $this->response(false, "Error finding drivers: " . $e->getMessage());
        }
    }
    
    /**
     * Request a ride
     * Endpoint: POST /ride_api.php?action=request_ride
     */
    public function requestRide($data) {
        try {
            $required = [
                'passenger_id', 'pickup_lat', 'pickup_lng', 'pickup_address',
                'dropoff_lat', 'dropoff_lng', 'dropoff_address', 'vehicle_type'
            ];
            
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return $this->response(false, "Missing required parameter: $field");
                }
            }
            
            // Calculate distance between pickup and dropoff
            $distance = $this->calculateDistance(
                $data['pickup_lat'], $data['pickup_lng'],
                $data['dropoff_lat'], $data['dropoff_lng']
            );
            
            // Calculate fare
            $base_fare = $this->getBaseFare($data['vehicle_type']);
            $distance_fare = $this->getPerKmRate($data['vehicle_type']) * $distance;
            $total_fare = $base_fare + $distance_fare;
            
            // Create ride record
            $ride_stmt = $this->conn->prepare("
                INSERT INTO rides (
                    passenger_id, vehicle_type,
                    pickup_latitude, pickup_longitude, pickup_address,
                    dropoff_latitude, dropoff_longitude, dropoff_address,
                    distance_km, estimated_duration_minutes,
                    base_fare, distance_fare, total_fare,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'searching')
            ");
            
            $estimated_duration = round(($distance / 30) * 60);
            
            $ride_stmt->execute([
                $data['passenger_id'],
                $data['vehicle_type'],
                $data['pickup_lat'],
                $data['pickup_lng'],
                $data['pickup_address'],
                $data['dropoff_lat'],
                $data['dropoff_lng'],
                $data['dropoff_address'],
                $distance,
                $estimated_duration,
                $base_fare,
                $distance_fare,
                $total_fare
            ]);
            
            $ride_id = $this->conn->lastInsertId();
            
            return $this->response(true, "Ride request submitted", [
                'ride_id' => $ride_id,
                'distance' => round($distance, 2),
                'estimated_duration' => $estimated_duration,
                'estimated_fare' => round($total_fare),
                'status' => 'searching'
            ]);
            
        } catch (Exception $e) {
            return $this->response(false, "Ride request failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get ride details
     * Endpoint: GET /ride_api.php?action=get_ride&ride_id=123
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
     * Get passenger ride history
     * Endpoint: GET /ride_api.php?action=passenger_history&passenger_id=123
     */
    public function getPassengerHistory($passenger_id) {
        try {
            if (!$passenger_id) {
                return $this->response(false, "Passenger ID required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                       d.name as driver_name,
                       dv.vehicle_model, dv.plate_number,
                       rr.rating
                FROM rides r
                LEFT JOIN drivers d ON r.driver_id = d.id
                LEFT JOIN driver_vehicles dv ON r.driver_id = dv.driver_id
                LEFT JOIN ride_ratings rr ON r.id = rr.ride_id
                WHERE r.passenger_id = ?
                ORDER BY r.created_at DESC
                LIMIT 50
            ");
            
            $stmt->execute([$passenger_id]);
            $rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->response(true, "Ride history found", ['rides' => $rides]);
            
        } catch (Exception $e) {
            return $this->response(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Get driver ride history
     * Endpoint: GET /ride_api.php?action=driver_history&driver_id=123
     */
    public function getDriverHistory($driver_id) {
        try {
            if (!$driver_id) {
                return $this->response(false, "Driver ID required");
            }
            
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                       p.name as passenger_name,
                       rr.rating
                FROM rides r
                LEFT JOIN passengers p ON r.passenger_id = p.id
                LEFT JOIN ride_ratings rr ON r.id = rr.ride_id
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
    
    public function calculateFare($vehicle_type, $distance) {
        $base_fare = $this->getBaseFare($vehicle_type);
        $per_km_rate = $this->getPerKmRate($vehicle_type);
        return round($base_fare + ($per_km_rate * $distance));
    }
    
    public function getBaseFare($vehicle_type) {
        $fares = [
            'car' => 3000,
            'motorcycle' => 2000,
            'bike' => 1000
        ];
        return $fares[$vehicle_type] ?? 3000;
    }
    
    public function getPerKmRate($vehicle_type) {
        $rates = [
            'car' => 2000,
            'motorcycle' => 1500,
            'bike' => 800
        ];
        return $rates[$vehicle_type] ?? 2000;
    }
    
    public function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $lat_delta = deg2rad($lat2 - $lat1);
        $lon_delta = deg2rad($lon2 - $lon1);
        
        $a = sin($lat_delta/2) * sin($lat_delta/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lon_delta/2) * sin($lon_delta/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $this->earth_radius * $c;
    }
    
    public function calculateETA($driver_lat, $driver_lng, $pickup_lat, $pickup_lng) {
        $distance = $this->calculateDistance($driver_lat, $driver_lng, $pickup_lat, $pickup_lng);
        return round(($distance / 30) * 60);
    }
    
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

$api = new RideAPI();
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    switch ($action) {
        // Passenger endpoints
        case 'register_passenger':
            echo $api->registerPassenger($data);
            break;
        case 'passenger_login':
            echo $api->passengerLogin($data);
            break;
        case 'save_address':
            echo $api->saveAddress($data);
            break;
            
        // Driver endpoints
        case 'register_driver':
            echo $api->registerDriver($data);
            break;
        case 'driver_login':
            echo $api->driverLogin($data);
            break;
        case 'update_location':
            echo $api->updateLocation($data);
            break;
        case 'toggle_availability':
            echo $api->toggleAvailability($data);
            break;
            
        // Ride endpoints
        case 'find_drivers':
            echo $api->findNearbyDrivers($data);
            break;
        case 'request_ride':
            echo $api->requestRide($data);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
    }
    
} elseif ($method === 'GET') {
    switch ($action) {
        case 'get_addresses':
            $passenger_id = isset($_GET['passenger_id']) ? $_GET['passenger_id'] : null;
            echo $api->getAddresses($passenger_id);
            break;
        case 'driver_stats':
            $driver_id = isset($_GET['driver_id']) ? $_GET['driver_id'] : null;
            echo $api->getDriverStats($driver_id);
            break;
        case 'get_ride':
            $ride_id = isset($_GET['ride_id']) ? $_GET['ride_id'] : null;
            echo $api->getRide($ride_id);
            break;
        case 'passenger_history':
            $passenger_id = isset($_GET['passenger_id']) ? $_GET['passenger_id'] : null;
            echo $api->getPassengerHistory($passenger_id);
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