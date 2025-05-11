<?php
class Seat {
    private $conn;
    private $table = "bus_seats";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // FETCH ALL SEATS
    public function getAllSeats() {
        $query = "SELECT id, seat_number, user_id, seat_status FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // FETCH SEAT BY ID
    public function getSeatById($id) {
        $query = "SELECT id, seat_number, user_id, seat_status FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // FETCH SEAT BY NUMBER
    public function getSeatByNumber($seat_number) {
        $query = "SELECT id, seat_number, user_id, seat_status FROM " . $this->table . " WHERE seat_number = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$seat_number]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // FETCH SEATS BY STATUS
    public function getSeatsByStatus($status) {
        $query = "SELECT id, seat_number, user_id, seat_status FROM " . $this->table . " WHERE seat_status = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ADD A NEW SEAT
    public function addSeat($seat_number, $user_id, $seat_status) {
        $query = "INSERT INTO " . $this->table . " (seat_number, user_id, seat_status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$seat_number, $user_id, $seat_status]);
    }
    
    // UPDATE SEAT STATUS
    public function updateSeatStatus($seat_number, $status) {
        $query = "UPDATE " . $this->table . " SET seat_status = ? WHERE seat_number = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $seat_number]);
    }
    
    // BOOK MULTIPLE SEATS
    public function bookSeats($seat_numbers, $user_id) {
        $success = true;
        $this->conn->beginTransaction();
        
        try {
            foreach ($seat_numbers as $seat_number) {
                $query = "UPDATE " . $this->table . " 
                          SET user_id = ?, seat_status = 'unavailable' 
                          WHERE seat_number = ? AND seat_status = 'available'";
                $stmt = $this->conn->prepare($query);
                $result = $stmt->execute([$user_id, $seat_number]);
                
                if (!$result) {
                    $success = false;
                    break;
                }
            }
            
            if ($success) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // INITIALIZE SEATS
    public function initializeSeats() {
        // First, check if seats already exist
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            return false; // Seats already exist
        }
        
        // Create seats based on your HTML structure (12 rows, 5 seats per row with position 3 empty)
        $this->conn->beginTransaction();
        
        try {
            $seatCount = 1;
            for ($row = 1; $row <= 12; $row++) {
                for ($col = 1; $col <= 5; $col++) {
                    // Skip the middle position (3) in each row
                    if ($col == 3) continue;
                    
                    $query = "INSERT INTO " . $this->table . " (seat_number, seat_status) VALUES (?, 'available')";
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute([(string)$seatCount]);
                    $seatCount++;
                }
            }
            
            // Set some seats as unavailable based on your HTML
            $unavailableSeats = [3, 5, 7, 13, 15, 23, 24, 35, 45, 48];
            foreach ($unavailableSeats as $seatNumber) {
                $query = "UPDATE " . $this->table . " SET seat_status = 'unavailable' WHERE seat_number = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([(string)$seatNumber]);
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // GET SEAT MAP
    public function getSeatMap() {
        $query = "SELECT seat_number, seat_status FROM " . $this->table . " ORDER BY CAST(seat_number AS UNSIGNED)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format as needed for your frontend
        $seatMap = [];
        $unavailableSeats = [];
        
        foreach ($seats as $seat) {
            if ($seat['seat_status'] == 'unavailable') {
                $unavailableSeats[] = (int)$seat['seat_number'];
            }
        }
        
        return [
            'seatMap' => [
                [1, 2, 0, 3, 4],
                [5, 6, 0, 7, 8],
                [9, 10, 0, 11, 12],
                [13, 14, 0, 15, 16],
                [17, 18, 0, 19, 20],
                [21, 22, 0, 23, 24],
                [25, 26, 0, 27, 28],
                [29, 30, 0, 31, 32],
                [33, 34, 0, 35, 36],
                [37, 38, 0, 39, 40],
                [41, 42, 0, 43, 44],
                [45, 46, 0, 47, 48]
            ],
            'unavailableSeats' => $unavailableSeats
        ];
    }
}
?>