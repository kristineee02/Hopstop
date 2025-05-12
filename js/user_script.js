function scrollLeft() {
    document.getElementById('slider').scrollBy({ left: -250, behavior: 'smooth' });
}

function scrollRight() {
    document.getElementById('slider').scrollBy({ left: 250, behavior: 'smooth' });
}

function toggleDropdown1() {
    let dropdown = document.getElementById("dropdownMenu1");
    dropdown.classList.toggle("show");
}

document.addEventListener('DOMContentLoaded', function() {
   
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
  
    profileButton.addEventListener('click', function() {
        profileDropdown.classList.toggle('show');
    });
  
    // Close dropdown when clicking outside
    window.addEventListener('click', function(event) {
        if (!event.target.matches('.user-profile') && !event.target.matches('.fa-user-circle')) {
            if (profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        }
    });
    
    // Initialize modal functionality
    initModal();
     
    // Load user data on page load
    getPassenger();
     
    // Handle form submission
    const profileForm = document.getElementById("profileUpdateForm");
    if (profileForm) {
        profileForm.addEventListener("submit", function(event) {
            event.preventDefault();
            updateProfile();
        });
    }
});
  
function initModal() {
    const modal = document.getElementById("editProfileModal");
    const editProfileBtn = document.getElementById("EditProfile");
    
    if (!modal || !editProfileBtn) {
        console.error("Modal elements not found");
        return;
    }
    
    const closeBtn = modal.querySelector(".close");
     
    editProfileBtn.addEventListener("click", function() {
        modal.classList.add("show");
    });
     
    closeBtn.addEventListener("click", function() {
        modal.classList.remove("show");
    });
     
    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.classList.remove("show");
        }
    });
}

function getPassenger() {
    fetch("../api/passenger_api.php")
    .then(response => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then(data => {
        console.log("Session data:", data);
        if (data.status === "success" && data.userId) {
            const passengerId = data.userId;
            return fetch(`../api/passenger_api.php?userId=${passengerId}`);
        } else {
            throw new Error("No active user session found");
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then(data => {
        console.log("User data:", data);
        if (data.status === "success" && data.passengerData) {
            const userNameElement = document.getElementById("userName");
            const userEmailElement = document.getElementById("userEmail");
            const profileImageElement = document.getElementById("profile-image");
            
            if (userNameElement) {
                userNameElement.textContent = 
                    `${data.passengerData.first_name} ${data.passengerData.last_name}`;
            }
            
            if (userEmailElement) {
                userEmailElement.textContent = data.passengerData.email;
            }
            
            if (profileImageElement && data.passengerData.picture) {
                profileImageElement.src = `../Uploads/${data.passengerData.picture}`;
            }
            
            // Also update form fields for editing
            const firstNameInput = document.getElementById("editfname");
            const lastNameInput = document.getElementById("editlname");
            
            if (firstNameInput) {
                firstNameInput.value = data.passengerData.first_name;
            }
            
            if (lastNameInput) {
                lastNameInput.value = data.passengerData.last_name;
            }
        } else {
            console.error("Failed to get user data:", data.message);
        }
    })
    .catch(error => {
        console.error("Error in user data fetching:", error);
        // Optionally redirect to login page if session is invalid
        // window.location.href = "login.php";
    });
}

function updateProfile() {
    const formData = new FormData();
    const firstNameInput = document.getElementById("editfname");
    const lastNameInput = document.getElementById("editlname");
    const profilePicInput = document.getElementById("editProfile");
    
    if (firstNameInput) {
        formData.append('firstName', firstNameInput.value);
    }
    
    if (lastNameInput) {
        formData.append('lastName', lastNameInput.value);
    }

    if (profilePicInput && profilePicInput.files && profilePicInput.files[0]) {
        formData.append('picture', profilePicInput.files[0]);
    }

    fetch("../api/passenger_api.php", {
        method: "PUT",
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            alert("Profile updated successfully!");
            // Reload the page to show updated information
            window.location.reload();
        } else {
            throw new Error(data.message || "Update failed");
        }
    })
    .catch(error => {
        console.error("Update error:", error);
        alert("Error updating profile: " + error.message);
    });
}