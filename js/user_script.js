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

    initModal();
    getPassenger();

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
    fetch("../api/store_session.php")
        .then(response => {
            if (!response.ok) throw new Error('Session fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Session response:', data);
            if (data.status === "success" && data.userId) {
                return fetch(`../api/passenger_api.php?userId=${data.userId}`);
            } else {
                throw new Error("User not authenticated");
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Passenger fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Passenger response:', data);
            if (data.status === "success" && data.passengerData) {
                const passenger = data.passengerData;
                document.getElementById("imageDisplay2").src = passenger.picture ? `../Uploads/${passenger.picture}` : '../images/profile.png';
                document.getElementById("nameDisplay2").textContent = `${passenger.first_name} ${passenger.last_name}`;
                document.getElementById("userEmail").textContent = passenger.email;
                document.getElementById("editfirstName").value = passenger.first_name;
                document.getElementById("editlastName").value = passenger.last_name;
                document.getElementById("edit-prof").setAttribute('data-current', passenger.picture || '');
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
    const profilePicInput = document.getElementById("edit-prof");
    if (profilePicInput.files[0]) {
        formData.append('picture', profilePicInput.files[0]);
    }

    fetch("../api/store_session.php")
        .then(response => {
            if (!response.ok) throw new Error('Session fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Session response for update:', data);
            if (data.status === "success" && data.userId) {
                formData.append('passengerId', data.userId);
                return fetch("../api/passenger_api.php", {
                    method: "POST",
                    body: formData
                });
            } else {
                throw new Error("User not authenticated");
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
                window.location.reload();
            } else {
                throw new Error(data.message || "Update failed");
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            alert("Error updating profile: " + error.message);
        });
}