document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("formId").addEventListener("submit", function(event){
        event.preventDefault();
        addUser();
    });
});

function addUser(){
    const formData = {
        firstName: document.getElementById("firstName").value.trim(),
        lastName: document.getElementById("lastName").value.trim(),
        email: document.getElementById("email").value.trim(),
        password: document.getElementById("password").value,
    }

    // Basic validation
    if (!formData.firstName || !formData.lastName || !formData.email || !formData.password) {
        alert("Please fill in all fields");
        return;
    }

    // Email validation
    if (!isValidEmail(formData.email)) {
        alert("Please enter a valid email address");
        return;
    }

    fetch("../api/user_signup_api.php", {
        method: "POST",
        body: JSON.stringify(formData),
        headers: {"Content-Type": "application/json"}
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success"){
            alert("Account created successfully! Please login.");
            location.href = "../Login/Login.php";
        } else {
            alert(data.message || "Failed to create account");
        }
    })
    .catch(error => {
        console.error(error);
        alert("An error occurred. Please try again later.");
    });
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}