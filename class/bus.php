<?php
class Bus {
    private $conn;
    private $table = 'Bus';
    public function __construct($db) {
        $this->conn = $db;
    }

   
    public function getAllBusDetails() {
        try {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $buses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $buses[] = $row;
            }
            
            return $buses;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
    
    
    public function getBusById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    
    public function getFilteredBuses($filters) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
            $params = [];
            
            if (isset($filters['location'])) {
                $query .= " AND location = ?";
                $params[] = $filters['location'];
            }
            
            if (isset($filters['destination'])) {
                $query .= " AND destination = ?";
                $params[] = $filters['destination'];
            }
            
            if (isset($filters['bus_type'])) {
                $query .= " AND bus_type = ?";
                $params[] = $filters['bus_type'];
            }
            
            $stmt = $this->conn->prepare($query);
            
            for ($i = 0; $i < count($params); $i++) {
                $stmt->bindValue($i + 1, $params[$i]);
            }
            
            $stmt->execute();
            
            $buses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $buses[] = $row;
            }
            
            return $buses;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

  
    public function createNewBus($id, $location, $destination, $departure_time, $arrival_time, $bus_type, $price, $available_seats, $status) {
        $query = "INSERT INTO Bus (id, location, destination, departure_time, arrival_time, bus_type, price, available_seats, status)
                  VALUES (:id, :location, :destination, :departure_time, :arrival_time, :bus_type, :price, :available_seats, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':destination', $destination);
        $stmt->bindParam(':departure_time', $departure_time);
        $stmt->bindParam(':arrival_time', $arrival_time);
        $stmt->bindParam(':bus_type', $bus_type);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':available_seats', $available_seats);
        return $stmt->execute();
    }
  
    public function updateBus($id, $location, $destination, $departure_time, $arrival_time, $bus_type, $price, $available_seats, $status) {
        try {
            $query = "UPDATE " . $this->table . " 
                    SET location = ?, destination = ?, departure_time = ?, arrival_time = ?, 
                        bus_type = ?, price = ?, available_seats = ?, status = ? 
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            
           
            $stmt->bindParam(1, $location);
            $stmt->bindParam(2, $destination);
            $stmt->bindParam(3, $departure_time);
            $stmt->bindParam(4, $arrival_time);
            $stmt->bindParam(5, $bus_type);
            $stmt->bindParam(6, $price);
            $stmt->bindParam(7, $available_seats);
            $stmt->bindParam(8, $status);
            $stmt->bindParam(9, $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
   
    public function deleteBus($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
}
?>