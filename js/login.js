document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("formId").addEventListener("submit", function(event) {
        event.preventDefault();
        login();
    });

});

function login() {
    const formData = {
        email: document.getElementById("email").value.trim(),
        password: document.getElementById("password").value
    };
    
    console.log("Attempting login with email:", formData.email);
    
    fetch("../api/login_api.php", {
        method: "POST",
        body: JSON.stringify(formData),
        headers: { "Content-Type": "application/json" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            if (data.accountType === "passenger") {
                const passengerData = data.passengers;
                alert("Passenger Login Successful!");
                storeSession(passengerData, "passenger");
                window.location.href = "../userview/User.php";
            } else if (data.accountType === "admin") {
                const adminData = data.admins;
                alert("Admin Login Successful!");
                storeSession(adminData, "admin");
                window.location.href = "../admin/admin.php";
            } else {
                alert(data.message);
            }
        } else {
            alert(data.message || "Login failed. Please check your credentials.");
        }
    })
    .catch(error => {
        console.error("Login error:", error);
        alert("An error occurred during login. Please try again later.");
    });
}

function storeSession(data, userType) {
    // Add the user type to session data
    const sessionData = {
        email: data.email,
        user_type: userType
    };
    
    if (userType === "passenger") {
        sessionData.passenger_id = data.passenger_id;
    } else if (userType === "admin") {
        sessionData.admin_id = data.admin_id;
    }
    
    fetch("../api/store_session.php", {
        method: "POST",
        body: JSON.stringify(sessionData),
        headers: { "Content-Type": "application/json" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            console.log("Session stored successfully");
        } else {
            alert("Failed to store session: " + data.message);
        }
    })
    .catch(error => {
        console.error("Session storage error:", error);
        alert("An error occurred while storing the session.");
    });
}