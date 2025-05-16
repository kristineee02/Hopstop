<?php
include '../api/database.php';
include '../class/DbTest.php';

$database = new Database();
$conn = $database->getConnection();
$test = new DbTest($conn);
$connectionStatus = $test->checkConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HopStop Admin</title>
    <link rel="stylesheet" href="../style/styles2.css">
    <style>
        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .clickable-row:hover {
            background-color: #f5f5f5;
        }
        .status-btn {
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-weight: bold;
        }
        .status-pending {
            background-color: #ffaa00;
            color: white;
        }
        .status-confirmed {
            background-color: #4CAF50;
            color: white;
        }
        .search-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-filter select, 
        .search-filter input {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .form-group input,
        .form-group select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 100%;
        }
        .modal button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .modal button:hover {
            background-color: #45a049;
        }
        .main-header {
            background-color: #a682c1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .nav-menu {
            display: flex;
            gap: 25px;
        }
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            padding: 5px 10px;
        }
        .nav-menu a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header class="main-header">
    <div class="logo">Admin HopStop</div>
    <nav class="nav-menu">
        <a href="admin.php">Bus Management</a>
        <a href="booking_management.php">Booking Management</a>
        <a href="passenger_list.php">Passenger Records</a>
        <a href="#">Logout</a>
    </nav>
</header>
<div class="container">
    <div class="table-container">
        <div class="page-header">
            <div class="page-title">Booking Management</div>
        </div>
        <div class="search-filter">
            <select id="filterPassType" onchange="filterPassengerType()">
                <option value="">Filter By Type</option>
                <option value="Regular">Regular</option>
                <option value="PWD/Senior Citizen">PWD/Senior Citizen</option>
                <option value="Student">Student</option>
            </select>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Passenger Type</th>
                    <th>Seat Number</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="BookingTableBody"></tbody>
        </table>
        <div id="booking-details"></div>
    </div>
</div>

<script src="../js/booking.js"></script>
</body>
</html>