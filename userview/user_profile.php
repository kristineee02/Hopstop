<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HopStop Dashboard</title>
    <link rel="stylesheet" href="../style/user_prof.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
   <div id="main-container">
       <header>
         <div class="container nav-container">
             <div class="logo">
                 <div class="logo-circle"></div>
                 <span><b>HopStop</b></span>
             </div>
             <nav>
                 <ul class="nav-links">
                     <li><a href="User.php">Home</a></li>
                     <li><a href="About.php">About</a></li>
                     <li><a href="contact.php">Contact</a></li>
                 </ul>
             </nav>
            
             <div class="user-profile" id="profileButton">
                 <i class="fas fa-user-circle"></i>
             </div>
            
             <div class="profile-dropdown" id="profileDropdown">
                 <div class="dropdown-item">Profile</div>
                 <div class="dropdown-item" onclick="logOut()">Logout</div>
             </div>
         </div>
       </header>   

       <section class="user-profile-section">
           <section class="profile-card" id="profileHeaderDocumet">
               <div class="profile-info">
                   <div class="avatar">
                       <img src="../images/profile.png" alt="Profile Photo" id="profile-image">
                   </div>
                   <div class="details">
                       <p><strong id="userName">Name</strong></p>
                       <p id="userEmail">Email</p>
                   </div>
               </div>
               <div class="profile-actions">
                   <button class="edit-btn" id="EditProfile">Edit Profile</button>
               </div>

               <!-- EDIT PROFILE MODAL -->
               <div id="editProfileModal" class="modal">
                   <div class="modal-content">
                       <span class="close">&times;</span>
                       <h3>Update Profile</h3>
                       <form id="profileUpdateForm" enctype="multipart/form-data">
                           <div class="form-grid">
                               <div class="form-group">
                                   <label for="editfname">First Name</label>
                                   <input type="text" id="editfname" name="editfname" placeholder="First Name" required>
                                  
                                   <label for="editlname">Last Name</label>
                                   <input type="text" id="editlname" name="editlname" placeholder="Last Name" required>
                                  
                                   <div class="file-input">
                                       <label for="editProfile">Profile Picture</label>
                                       <input type="file" id="editProfile" name="editProfile" accept="image/*">
                                   </div>                
                               </div>
                           </div>
                           <button type="submit" class="button-edit">Save Changes</button>
                       </form>
                   </div>
               </div>
           </section>

            <h2>Active Booking</h2>
            <section class="booking-section">
                <div class="booking-scroll">
                    <div class="booking-card">
                        <nav>
                            <a href="#">Bus No. </a>
                            <a href="#">Location </a>
                            <a href="#">Destination</a>
                            <a href="#">Departure</a>
                            <a href="#">Arrival</a>
                            <a href="#">Seat No.</a>
                        </nav>
                    </div>
                </div>
            </section>
            <div class="booking-buttons">
                <button class="cancel-btn" onclick="window.location.href='cancel.html';">Cancel Booking</button>
                <button class="view-btn">View Ticket</button>
            </div>
            <div class="booking-header">
                <h2>Booking History</h2>

                <div class="button">
                    <button class="arrow-button" onclick="toggleDropdown1()">
                            <img src="../images/free-arrow-down-icon-3101-thumb.png" alt="arrow">
                        </button>
                </div>
                <div class="dropdown-menu1" id="dropdownMenu1">
                    <section class="booking-section-history">
                        <div class="booking-scroll">
                            <div class="booking-card-history">
                                <nav>
                                    <a href="#">Bus No. </a>
                                    <a href="#">Location </a>
                                    <a href="#">Destination</a>
                                    <a href="#">Departure</a>
                                    <a href="#">Arrival</a>
                                    <a href="#">Seat No.</a>
                                </nav>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="booking-buttons1">
                    <button class="view-btn1">View Ticket</button>
                </div>
            </div>
       </section>
   </div>
   
   <!-- Add script tags at the end of the body -->
   <script src="../js/user_script.js"></script>
</body>

</html>