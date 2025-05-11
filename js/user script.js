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
  
   
    window.addEventListener('click', function(event) {
        if (!event.target.matches('.user-profile') && !event.target.matches('.fa-user-circle')) {
            if (profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        }
    });
    
    // Initialize modal functionality
    initModal();
     
    // Load user data
    getPassenger();
     
    // Handle form submission
    document.getElementById("profileUpdateForm").addEventListener("submit", function(event) {
        event.preventDefault();
        updateProfile();
    });
});
  
function logOut() {
    let confirmLogout = confirm("Are you sure you want to log out?");
    
    if (confirmLogout) {
        window.location.href = "Homepage.php";
        sessionStorage.clear();
    }
}
  
function initModal() {
    const modal = document.getElementById("editProfileModal");
    const editProfileBtn = document.getElementById("EditProfile");
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
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            const passengerId = data.userId;
            fetch(`../api/passenger_api.php?userId=${passengerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("userName").textContent = 
                        `${data.passengerData.first_name} ${data.passengerData.last_name}`;
                    document.getElementById("userEmail").textContent = 
                        data.passengerData.email;
                    if (data.passengerData.picture) {
                        document.getElementById("profile-image").src = 
                            `../Uploads/${data.passengerData.picture}`;
                    }
                    document.getElementById("editfname").value = data.passengerData.first_name;
                    document.getElementById("editlname").value = data.passengerData.last_name;
                }
            })
            .catch(error => console.error("Error fetching passenger details:", error));
        }
    })
    .catch(error => console.error("Error fetching session:", error));
}

function updateProfile() {
    const formData = new FormData();
    formData.append('firstName', document.getElementById("editfname").value);
    formData.append('lastName', document.getElementById("editlname").value);

    const profilePicInput = document.getElementById("editProfile");
    if (profilePicInput.files && profilePicInput.files[0]) {
        formData.append('picture', profilePicInput.files[0]);
    }

    fetch("../api/passenger_api.php", {
        method: "PUT",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Profile updated successfully!");
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