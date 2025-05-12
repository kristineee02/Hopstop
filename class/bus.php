<?php
class Bus {
    private $conn;
    private $table = 'buses';
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
            $query = "SELECT * FROM " . $this->table . " WHERE bus_id = ?";
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

  
    public function createNewBus($id, $location, $destination, $date, $time, $bus_type, $price, $available_seats, $bus_number) {
        $query = "INSERT INTO Bus (bus_id, location, destination, date, time, bus_type, price, available_seats, bus_number)
                  VALUES (:bus_id, :location, :destination, :date, :time, :bus_type, :price, :available_seats, :bus_number)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_number', $bus_number);
        $stmt->bindParam(':bus_id', $id);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':destination', $destination);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':bus_type', $bus_type);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':available_seats', $available_seats);
        return $stmt->execute();
    }
  
    public function updateBus($id, $location, $destination, $date, $time, $bus_type, $price, $available_seats, $bus_number) {
        try {
            $query = "UPDATE " . $this->table . " 
                    SET location = ?, destination = ?, date = ?, time = ?, 
                        bus_type = ?, price = ?, available_seats = ?, bus_number = ? 
                    WHERE bus_id = ?";
            
            $stmt = $this->conn->prepare($query);
            
           
            $stmt->bindParam(1, $location);
            $stmt->bindParam(2, $destination);
            $stmt->bindParam(3, $date);
            $stmt->bindParam(4, $time);
            $stmt->bindParam(5, $bus_type);
            $stmt->bindParam(6, $price);
            $stmt->bindParam(7, $available_seats);
            $stmt->bindParam(8, $bus_number);
            $stmt->bindParam(9, $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
   
    public function deleteBus($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE bus_id = ?";
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