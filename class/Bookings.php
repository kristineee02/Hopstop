<?php
class Booking {
    private $conn;
    private $table = "bookings";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBookingDetails() {
        $query = "SELECT b.*, 
                  p.name as passenger_name, 
                  bs.location, bs.destination, bs.departure_time, bs.arrival_time, bs.bus_type, bs.price 
                  FROM " . $this->table . " b
                  LEFT JOIN passenger p ON b.passenger_id = p.passenger_id
                  LEFT JOIN bus bs ON b.bus_id = bs.bus_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingById($id) {
        try {
            $query = "SELECT b.*, 
                      p.name as passenger_name, 
                      bs.location, bs.destination, bs.departure_time, bs.arrival_time, bs.bus_type, bs.price 
                      FROM " . $this->table . " b
                      LEFT JOIN passenger p ON b.passenger_id = p.passenger_id
                      LEFT JOIN bus bs ON b.bus_id = bs.bus_id
                      WHERE b.booking_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getBookingById: " . $e->getMessage());
            throw new Exception("Failed to fetch booking details: " . $e->getMessage());
        }
    }

    public function getBookingsByPassengerId($passenger_id) {
        try {
            $query = "SELECT b.*, 
                      bs.location, bs.destination, bs.departure_time, bs.arrival_time, bs.bus_type, bs.price 
                      FROM " . $this->table . " b
                      LEFT JOIN bus bs ON b.bus_id = bs.bus_id
                      WHERE b.passenger_id = :passenger_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':passenger_id', $passenger_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getBookingsByPassengerId: " . $e->getMessage());
            throw new Exception("Failed to fetch passenger's bookings: " . $e->getMessage());
        }
    }

    public function getBookingsByBusId($bus_id) {
        try {
            $query = "SELECT b.*, 
                      p.name as passenger_name 
                      FROM " . $this->table . " b
                      LEFT JOIN passenger p ON b.passenger_id = p.passenger_id
                      WHERE b.bus_id = :bus_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getBookingsByBusId: " . $e->getMessage());
            throw new Exception("Failed to fetch bus bookings: " . $e->getMessage());
        }
    }

    public function getOccupiedSeats($bus_id) {
        try {
            $query = "SELECT seat_number FROM " . $this->table . " WHERE bus_id = :bus_id AND status != 'cancelled'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (PDOException $e) {
            error_log("Database error in getOccupiedSeats: " . $e->getMessage());
            throw new Exception("Failed to fetch occupied seats: " . $e->getMessage());
        }
    }

    public function createNewBooking($passenger_id, $bus_id, $reserve_name, $passenger_type, 
                                  $seat_number, $id_upload_path, $reference, $remarks, $status = 'pending') {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (passenger_id, bus_id, reserve_name, passenger_type, seat_number,
                      id_upload_path, reference, remarks, status)
                     VALUES (:passenger_id, :bus_id, :reserve_name, :passenger_type, :seat_number,
                            :id_upload_path, :reference, :remarks, :status)";

            $stmt = $this->conn->prepare($query);
            $params = [
                ':passenger_id' => $passenger_id,
                ':bus_id' => $bus_id,
                ':reserve_name' => $reserve_name,
                ':passenger_type' => $passenger_type,
                ':seat_number' => $seat_number,
                ':id_upload_path' => $id_upload_path,
                ':reference' => $reference,
                ':remarks' => $remarks,
                ':status' => $status
            ];
            
            // Begin transaction
            $this->conn->beginTransaction();
            
            // Create booking
            $result = $stmt->execute($params);
            
            if ($result) {
                // Update available seats in bus table
                $updateQuery = "UPDATE bus SET available_seats = available_seats - 1 WHERE bus_id = :bus_id AND available_seats > 0";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
                $updateResult = $updateStmt->execute();
                
                if ($updateResult) {
                    $this->conn->commit();
                    return $this->conn->lastInsertId();
                } else {
                    $this->conn->rollBack();
                    return false;
                }
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Database error in createNewBooking: " . $e->getMessage());
            throw new Exception("Failed to create booking: " . $e->getMessage());
        }
    }

    public function updateBookingStatus($id, $status) {
        try {
            $query = "UPDATE " . $this->table . " SET status = :status WHERE booking_id = :id";
            $stmt = $this->conn->prepare($query);
            $params = [
                ':status' => $status,
                ':id' => $id
            ];
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database error in updateBookingStatus: " . $e->getMessage());
            throw new Exception("Failed to update booking status: " . $e->getMessage());
        }
    }

    public function deleteBooking($id) {
        try {
            // First, get the booking details to update bus available seats
            $getBookingQuery = "SELECT bus_id, status FROM " . $this->table . " WHERE booking_id = :id";
            $getStmt = $this->conn->prepare($getBookingQuery);
            $getStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $getStmt->execute();
            $booking = $getStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                return false;
            }
            
            // Begin transaction
            $this->conn->beginTransaction();
            
            // Delete the booking
            $query = "DELETE FROM " . $this->table . " WHERE booking_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            // If booking was not cancelled, update the available seats
            if ($result && $booking['status'] !== 'cancelled') {
                $updateQuery = "UPDATE bus SET available_seats = available_seats + 1 WHERE bus_id = :bus_id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':bus_id', $booking['bus_id'], PDO::PARAM_INT);
                $updateResult = $updateStmt->execute();
                
                if ($updateResult) {
                    $this->conn->commit();
                    return true;
                } else {
                    $this->conn->rollBack();
                    return false;
                }
            } else {
                $this->conn->commit();
                return $result;
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Database error in deleteBooking: " . $e->getMessage());
            throw new Exception("Failed to delete booking: " . $e->getMessage());
        }
    }
    
    public function generateReference() {
        // Generate a unique reference number (alphanumeric, 10 characters)
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $reference = '';
        for ($i = 0; $i < 10; $i++) {
            $reference .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Check if the reference already exists
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE reference = :reference";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            // If reference already exists, generate a new one recursively
            return $this->generateReference();
        }
        
        return $reference;
    }
}
?>