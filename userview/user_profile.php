<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HopStop Dashboard</title>
    <link rel="stylesheet" href="../style/user_prof.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
         th {
        color: black;
        text-align: left;
        padding-right: 14px;
        text-transform: uppercase;
        font-size: 14px;
    }

    td {
        padding-right: 12px;
        border-bottom: 1px solid #eee;
        font-size: 15px;
    }
    </style>

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
                     <li><a href="Contact.php">Contact</a></li>
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
                       <img src="../images/profile.png" alt="Profile Photo" id="imageDisplay2">
                   </div>
                   <div class="details">
                       <p><strong id="nameDisplay2">Name</strong></p>
                       <p id="userEmail">Email</p>
                   </div>
               </div>
               <div class="profile-actions">
                   <button class="edit-btn" id="EditProfile">Edit Profile</button>
               </div>
               <!-- EDIT PROFILE MODAL -->
               <div id="editProfileModal" class="modal">
                   <div class="modal-content">
                       <span class="close">×</span>
                       <h3>Update Profile</h3>
                       <form id="profileUpdateForm" enctype="multipart/form-data">
                           <div class="form-grid">
                               <div class="form-group">
                                   <label for="editfirstName">First Name</label>
                                   <input type="text" id="editfirstName" name="firstName" placeholder="First Name" required>
                                   <label for="editlastName">Last Name</label>
                                   <input type="text" id="editlastName" name="lastName" placeholder="Last Name" required>
                                   <div class="file-input">
                                       <label for="edit-prof">Profile Picture</label>
                                       <input type="file" id="edit-prof" name="picture" accept="image/*">
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
                     <div class="container">
                        <div class="booking-card" id="booking-details">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Bus No.</th>
                                        <th>Location</th>
                                        <th>Destination</th>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                        <th>Seat No.</th>
                                    </tr>
                                </thead>
                                <tbody id="content">
                                    <!--js-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            <div class="booking-buttons">
                <button class="cancel-btn">Cancel Booking</button>
            </div>

            <!-- <div class="booking-header">
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
                </div> -->

       </section>
   </div>
   
   <script src="../js/user_script.js"></script>

    
</body>

</html>