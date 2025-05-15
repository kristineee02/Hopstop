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

function logOut() {
    // Clear session and redirect to login page
    fetch("../api/logout.php", {
        method: "POST"
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success") {
            window.location.href = "../login/login.php";
        }
    })
    .catch(error => {
        console.error('Error logging out:', error);
        window.location.href = "../login/login.php"; // Redirect anyway if there's an error
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown toggle
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function(event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        window.addEventListener('click', function(event) {
            if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }

    // Initialize modal and fetch user data
    initModal();
    getPassenger();

    // Set up profile update form submission
    const profileForm = document.getElementById("profileUpdateForm");
    if (profileForm) {
        profileForm.addEventListener("submit", function(event) {
            event.preventDefault();
            updateProfile();
        });
    }
});

function initModal() {
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'EditProfile') {
            e.preventDefault();
            document.getElementById("editProfileModal").classList.add("show");
        }
        if (e.target && e.target.classList.contains('close')) {
            document.getElementById("editProfileModal").classList.remove("show");
        }
    });

    window.addEventListener('click', function(e) {
        const modal = document.getElementById("editProfileModal");
        if (e.target === modal) {
            modal.classList.remove("show");
        }
    });
}

function getPassenger() {
    // First, get the session data to retrieve the passenger ID
    fetch("../api/store_session.php")
        .then(response => {
            if (!response.ok) throw new Error('Session fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Session response:', data);
            if (data.status === "success" && data.passengerId) {
                // Now fetch passenger details using the ID
                return fetch(`../api/passenger_api.php?passengerId=${data.passengerId}`);
            } else {
                throw new Error(data.message || "User not authenticated");
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Passenger fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Passenger response:', data);
            if (data.status === "success" && data.passengerData) {
                // Update the UI with passenger data
                const passenger = data.passengerData;
                
                // Set profile image, using default if none exists
                const imgElement = document.getElementById("imageDisplay2");
                if (imgElement) {
                    imgElement.src = passenger.picture ? `../Uploads/${passenger.picture}` : '../images/profile.png';
                }
                
                // Set name display
                const nameElement = document.getElementById("nameDisplay2");
                if (nameElement) {
                    nameElement.textContent = `${passenger.first_name} ${passenger.last_name}`;
                }
                
                // Set email display
                const emailElement = document.getElementById("userEmail");
                if (emailElement) {
                    emailElement.textContent = passenger.email;
                }
                
                // Populate edit form fields
                const firstNameField = document.getElementById("editfirstName");
                if (firstNameField) {
                    firstNameField.value = passenger.first_name;
                }
                
                const lastNameField = document.getElementById("editlastName");
                if (lastNameField) {
                    lastNameField.value = passenger.last_name;
                }
            } else {
                throw new Error(data.message || "Failed to load passenger data");
            }
        })
        .catch(error => {
            console.error('Error fetching passenger:', error);
            alert("Error loading profile: " + error.message);
        });
}

function updateProfile() {
    const formData = new FormData();
    formData.append('firstName', document.getElementById("editfirstName").value);
    formData.append('lastName', document.getElementById("editlastName").value);
    
    // Handle profile picture
    const profilePicInput = document.getElementById("edit-prof");
    if (profilePicInput.files[0]) {
        // Validate file
        const file = profilePicInput.files[0];
        if (file.size > 5 * 1024 * 1024 || !file.type.startsWith('image/')) {
            alert('Profile picture must be an image under 5MB.');
            return;
        }
        formData.append('picture', file);
    }

    // Get session data to confirm user is logged in
    fetch("../api/store_session.php")
        .then(response => {
            if (!response.ok) throw new Error('Session fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Session response for update:', data);
            if (data.status === "success" && data.passengerId) {
                // Send update request with passenger ID
                return fetch("../api/passenger_api.php", {
                    method: "POST",
                    body: formData
                });
            } else {
                throw new Error(data.message || "User not authenticated");
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Update failed');
            return response.json();
        })
        .then(data => {
            console.log('Update response:', data);
            if (data.status === "success") {
                alert("Profile updated successfully!");
                document.getElementById("editProfileModal").classList.remove("show");
                // Refresh profile data
                getPassenger();
            } else {
                throw new Error(data.message || "Update failed");
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            alert("Error updating profile: " + error.message);
        });
}