<?php
class Booking {
    private $conn;
    private $table = "bookings";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBookingDetails() {
        try {
            $query = "SELECT b.booking_id, b.passenger_id, b.bus_id, b.passenger_type, 
                             b.seat_number,b.reference, b.status,
                             bs.price
                      FROM " . $this->table . " b
                      LEFT JOIN bus bs ON b.bus_id = bs.bus_id
                      WHERE b.status IN ('pending', 'confirmed')";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getAllBookingDetails: " . $e->getMessage());
            throw new Exception("Failed to fetch booking details: " . $e->getMessage());
        }
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

    public function getBookingsByPassengerType($passenger_type) {
        try {
            $query = "SELECT b.*, 
                      p.name as passenger_name, 
                      bs.location, bs.destination, bs.departure_time, bs.arrival_time, bs.bus_type, bs.price 
                      FROM " . $this->table . " b
                      LEFT JOIN passenger p ON b.passenger_id = p.passenger_id
                      LEFT JOIN bus bs ON b.bus_id = bs.bus_id
                      WHERE b.passenger_type = :passenger_type";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':passenger_type', $passenger_type);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getBookingsByPassengerType: " . $e->getMessage());
            throw new Exception("Failed to fetch bookings by passenger type: " . $e->getMessage());
        }
    }

    public function getOccupiedSeats($bus_id) {
        try {
            $query = "SELECT seat_number FROM " . $this->table . " WHERE bus_id = :bus_id AND status != 'cancelled'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
            $stmt->execute();
            $seats = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            return array_map('strval', $seats);
        } catch (PDOException $e) {
            error_log("Database error in getOccupiedSeats: " . $e->getMessage());
            throw new Exception("Failed to fetch occupied seats: " . $e->getMessage());
        }
    }

    public function createNewBooking($passenger_id, $bus_id, $reserve_name, $passenger_type, 
                                  $seat_number, $id_upload_path, $reference, $remarks, $status = 'pending') {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE bus_id = :bus_id AND seat_number = :seat_number AND status != 'cancelled'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
            $stmt->bindParam(':seat_number', $seat_number);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Seat is already taken");
            }

            $this->conn->beginTransaction();

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
            $result = $stmt->execute($params);
            $booking_id = $this->conn->lastInsertId();

            $updateQuery = "UPDATE bus SET available_seats = available_seats - 1 WHERE bus_id = :bus_id AND available_seats > 0";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
            $updateStmt->execute();

            if ($updateStmt->rowCount() === 0) {
                $this->conn->rollBack();
                throw new Exception("No available seats or bus not found");
            }

            $this->conn->commit();
            return $booking_id;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Database error in createNewBooking: " . $e->getMessage());
            throw new Exception("Failed to create booking: " . $e->getMessage());
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function updateBookingStatusByReference($reference, $status) {
        try {
            $query = "UPDATE " . $this->table . " SET status = :status WHERE reference = :reference";
            $stmt = $this->conn->prepare($query);
            $params = [
                ':status' => $status,
                ':reference' => $reference
            ];
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database error in updateBookingStatusByReference: " . $e->getMessage());
            throw new Exception("Failed to update booking status: " . $e->getMessage());
        }
    }

    public function updateBookingDetails($reference, $reserve_name, $passenger_type, $bus_id, $seat_number, $price, $status, $remarks) {
        try {
            // Verify bus exists
            $busQuery = "SELECT available_seats FROM bus WHERE bus_id = :bus_id";
            $busStmt = $this->conn->prepare($busQuery);
            $busStmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
            $busStmt->execute();
            $bus = $busStmt->fetch(PDO::FETCH_ASSOC);
            if (!$bus) {
                throw new Exception("Bus not found");
            }

            // Check if seat is taken by another booking
            $seatQuery = "SELECT booking_id FROM " . $this->table . " 
                         WHERE bus_id = :bus_id AND seat_number = :seat_number 
                         AND reference != :reference AND status != 'cancelled'";
            $seatStmt = $this->conn->prepare($seatQuery);
            $seatStmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
            $seatStmt->bindParam(':seat_number', $seat_number);
            $seatStmt->bindParam(':reference', $reference);
            $seatStmt->execute();
            if ($seatStmt->fetchColumn() > 0) {
                throw new Exception("Seat is already taken");
            }

            $this->conn->beginTransaction();

            // Get current booking to adjust available_seats if status changes
            $currentQuery = "SELECT status, bus_id, seat_number FROM " . $this->table . " WHERE reference = :reference";
            $currentStmt = $this->conn->prepare($currentQuery);
            $currentStmt->bindParam(':reference', $reference);
            $currentStmt->execute();
            $currentBooking = $currentStmt->fetch(PDO::FETCH_ASSOC);

            $query = "UPDATE " . $this->table . " 
                     SET reserve_name = :reserve_name, 
                         passenger_type = :passenger_type, 
                         bus_id = :bus_id, 
                         seat_number = :seat_number, 
                         status = :status, 
                         remarks = :remarks 
                     WHERE reference = :reference";
            $stmt = $this->conn->prepare($query);
            $params = [
                ':reserve_name' => $reserve_name,
                ':passenger_type' => $passenger_type,
                ':bus_id' => $bus_id,
                ':seat_number' => $seat_number,
                ':status' => $status,
                ':remarks' => $remarks,
                ':reference' => $reference
            ];
            $result = $stmt->execute($params);

            // Update bus available_seats if status or seat changes
            if ($result && $currentBooking) {
                if ($status === 'cancelled' && $currentBooking['status'] !== 'cancelled') {
                    $updateQuery = "UPDATE bus SET available_seats = available_seats + 1 WHERE bus_id = :bus_id";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->bindParam(':bus_id', $currentBooking['bus_id'], PDO::PARAM_INT);
                    $updateStmt->execute();
                } elseif ($status !== 'cancelled' && $currentBooking['status'] === 'cancelled') {
                    $updateQuery = "UPDATE bus SET available_seats = available_seats - 1 WHERE bus_id = :bus_id AND available_seats > 0";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
                    $updateStmt->execute();
                    if ($updateStmt->rowCount() === 0) {
                        $this->conn->rollBack();
                        throw new Exception("No available seats on this bus");
                    }
                } elseif ($seat_number !== $currentBooking['seat_number'] || $bus_id !== $currentBooking['bus_id']) {
                    // Adjust seats if seat or bus changes
                    if ($currentBooking['status'] !== 'cancelled') {
                        $updateQuery = "UPDATE bus SET available_seats = available_seats + 1 WHERE bus_id = :bus_id";
                        $updateStmt = $this->conn->prepare($updateQuery);
                        $updateStmt->bindParam(':bus_id', $currentBooking['bus_id'], PDO::PARAM_INT);
                        $updateStmt->execute();
                    }
                    if ($status !== 'cancelled') {
                        $updateQuery = "UPDATE bus SET available_seats = available_seats - 1 WHERE bus_id = :bus_id AND available_seats > 0";
                        $updateStmt = $this->conn->prepare($updateQuery);
                        $updateStmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
                        $updateStmt->execute();
                        if ($updateStmt->rowCount() === 0) {
                            $this->conn->rollBack();
                            throw new Exception("No available seats on this bus");
                        }
                    }
                }
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Database error in updateBookingDetails: " . $e->getMessage());
            throw new Exception("Failed to update booking: " . $e->getMessage());
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
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
            $getBookingQuery = "SELECT bus_id, status FROM " . $this->table . " WHERE booking_id = :id";
            $getStmt = $this->conn->prepare($getBookingQuery);
            $getStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $getStmt->execute();
            $booking = $getStmt->fetch(PDO::FETCH_ASSOC);
            if (!$booking) {
                return false;
            }
            $this->conn->beginTransaction();
            $query = "DELETE FROM " . $this->table . " WHERE booking_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
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
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $reference = '';
        for ($i = 0; $i < 10; $i++) {
            $reference .= $characters[rand(0, strlen($characters) - 1)];
        }
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE reference = :reference";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return $this->generateReference();
        }
        return $reference;
    }
}
?>