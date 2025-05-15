document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("formId").addEventListener("submit", function(event){
        event.preventDefault();
        login();
    });
});

function login(){
    const formData = {
        email: document.getElementById("email").value,
        password: document.getElementById("password").value
    }
    
    fetch("../api/login_api.php", {
        method: "POST",
        body: JSON.stringify(formData),
        headers: {"Content-Type": "application/json"}
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success"){
            if(data.accountType === "admin"){
                const adminData = data.admins;
                alert("Log In Successfully!");
                storeSession(adminData);
            }else if(data.accountType === "passenger"){
                const passengerData = data.passengers;
                alert("Log In Successfully!");
                storeSession(passengerData);
            }else{
                alert(data.message);
            }
        }
        else{
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Login error:", error);
        alert("Login failed. Please try again.");
    });
}

function storeSession(data){
    fetch("../api/store_session.php", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {"Content-Type": "application/json"}
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success"){
            console.log("Session stored successfully");
            // Redirect based on user type
            if (data.passengerId) {
                window.location.href = "../userview/User.php";
            } else if (data.adminId) {
                window.location.href = "../admin/admin.php";
            }
        } else {
            console.error("Session storage failed:", data.message);
            alert("Session storage failed. Please try again.");
        }
    })
    .catch(error => {
        console.error("Session storage error:", error);
        alert("Session storage failed. Please try again.");
    });
}