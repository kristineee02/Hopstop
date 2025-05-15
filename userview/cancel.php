<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/cancel.css">
    <script src="cancel.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">HopStop</div>
            <nav>
                <a href="#">Home</a>
                <a href="#">About</a>
                <a href="#">Contact</a>
            </nav>
            <div class="profile-container">
                <button class="profile-button" onclick="toggleDropdown()">
                    <img src="images/profile.png" alt="Profile Photo">    
                </button> 
                
            </div>
        </header>
       
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="#">View Profile</a>
                <a href="#">Logout</a>
            </div>
        
                
                    
                        <h2> Cancel Booking </h2>
                       
                        <div class="box">
                            <strong>BUS ID:</strong> <br>
                            <strong>Date:</strong> <br>
                            <strong>From:</strong> <br>
                            <strong>To:</strong>  <br>
                            <strong>Departure:</strong> <br>
                            <strong>Arrival:</strong> <br>
                            <strong>Price:</strong> 
                        </div>
                        <div class="refund-box">
                            <strong>Refund Policy:</strong>
                            <ul>
                                <li>Full refund available if canceled 72 hours before departure</li>
                                <li>50% refund if canceled 24-72 hours before departure</li>
                                <li>No refund for cancellations less than 24 hours before departure</li>
                            </ul>
                            <strong>Your Estimated Refund:</strong>
                        </div>
                        <h2> Reason for Cancellation</h2>
                        <select id="reason">
                            <option>Change of plans</option>
                            <option>Medical emergency</option>
                            <option>Bad weather</option>
                        </select>
                        <h2>Refund Method</h2>
                        <select id="refund-method">
                            <option>Bank Transfer</option>
                            <option>Over the counter</option>
                        </select>
                        <div class="btn-container">
                            <button class="back-btn">Back</button>
                            <button class="confirm-btn">Confirm Cancellation</button>
                            
                        </div>
               
               
                </div>
                
           
</body>
</html>