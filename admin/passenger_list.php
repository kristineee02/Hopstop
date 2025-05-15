<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview of Passenger List - Purple Theme</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* HEADER STYLES */
    .main-header {
        background-color: #a682c1; /* Restored original purple color */
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

    /* Page Title */
    .passenger-list-header {
        margin: 20px 30px;
    }

    .passenger-list-header h1 {
        color: #5d4777;
        font-size: 26px;
    }

    /* Main Container */
    .container {
        width: 92%;
        max-width: 1400px;
        margin: 25px auto;
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.08);
        flex: 1;
    }

    /* Table Styling */
    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
    }

    thead {
        background-color: #8e69a5;
    }

    th {
        color: white;
        text-align: left;
        padding: 14px;
        text-transform: uppercase;
        font-size: 14px;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 15px;
    }

    tbody tr:hover {
        background-color: #f9f5fd;
    }

    tr:nth-child(even) {
        background-color: #faf7fc;
    }

    /* Delete Button */
    .delete-btn {
        background-color: #a682c1;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .delete-btn:hover {
        background-color: #8e69a5;
    }

    /* Footer */
    footer {
        background-color: #5d4777;
        color: white;
        text-align: center;
        padding: 15px 0;
        font-size: 14px;
        margin-top: auto;
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

    <div class="passenger-list-header">
        <h1>Passenger List</h1>
    </div>

    <div class="container">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="content">
                    <!--js-->
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 HopStop Passenger Management System</p>
    </footer>

    <script src="../js/passenger_list.js"></script>

</body>
</html>