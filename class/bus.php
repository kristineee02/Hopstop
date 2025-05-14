<?php
class Bus {
    private $conn;
    private $table = 'bus'; 

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBusDetails() {
        $query = "SELECT * FROM bus";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBusById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE bus_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getBusById: " . $e->getMessage());
            throw new Exception("Failed to fetch bus: " . $e->getMessage());
        }
    }

    public function getFilteredBuses($filters) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
            $params = [];

            if (!empty($filters['location'])) {
                $query .= " AND location = :location";
                $params[':location'] = $filters['location'];
            }
            if (!empty($filters['destination'])) {
                $query .= " AND destination = :destination";
                $params[':destination'] = $filters['destination'];
            }
            if (!empty($filters['bus_type'])) {
                $query .= " AND bus_type = :bus_type";
                $params[':bus_type'] = $filters['bus_type'];
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getFilteredBuses: " . $e->getMessage());
            throw new Exception("Failed to fetch filtered buses: " . $e->getMessage());
        }
    }

    public function createNewBus( $location, $destination, $departure_time, 
                               $arrival_time, $available_seats, $bus_type, $price, $bus_number, $status) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (location, destination, departure_time, arrival_time,  available_seats,
                      bus_type, price, bus_number,  status)
                     VALUES ( :location, :destination, :departure_time, 
                            :arrival_time, :available_seats, :bus_type, :price, :bus_number,  :status)";

            $stmt = $this->conn->prepare($query);
            $params = [
                ':location' => $location,
                ':destination' => $destination,
                ':departure_time' => $departure_time,
                ':arrival_time' => $arrival_time,
                ':available_seats' => $available_seats,
                ':bus_type' => $bus_type,
                ':price' => $price,
                ':bus_number' => $bus_number,
                ':status' => $status
            ];
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database error in createNewBus: " . $e->getMessage());
            throw new Exception("Failed to create bus: " . $e->getMessage());
        }
    }

    public function updateBus($id, $location, $destination, $departure_time, 
                            $arrival_time,  $available_seats, $bus_type, $price,  $bus_number, $status) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET location = :location, 
                         destination = :destination, departure_time = :departure_time,
                         arrival_time = :arrival_time, available_seats = :available_seats, 
                         bus_type = :bus_type, price = :price, bus_number = :bus_number, 
                         status = :status WHERE bus_id = :id";

            $stmt = $this->conn->prepare($query);
            $params = [
                ':location' => $location,
                ':destination' => $destination,
                ':departure_time' => $departure_time,
                ':arrival_time' => $arrival_time,
                ':available_seats' => $available_seats,
                ':bus_type' => $bus_type,
                ':price' => $price,
                ':bus_number' => $bus_number,
                ':status' => $status,
                ':id' => $id
            ];
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database error in updateBus: " . $e->getMessage());
            throw new Exception("Failed to update bus: " . $e->getMessage());
        }
    }

    public function deleteBus($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE bus_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in deleteBus: " . $e->getMessage());
            throw new Exception("Failed to delete bus: " . $e->getMessage());
        }
    }
}
?>