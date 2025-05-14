document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("formId").addEventListener("submit", function(event){
        event.preventDefault();
        addAdmin();
    });
});

function addAdmin(){
    
    const formData = {
        firstName: document.getElementById("firstName").value,
        lastName: document.getElementById("lastName").value,
        email: document.getElementById("email").value,
        password: document.getElementById("password").value,
    }

    fetch("../api/admin_signup_api.php", {
        method: "POST",
        body: JSON.stringify(formData),
        headers: {"Content-Type": "application/json"}
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success"){
            alert("Added Successfully");
            location.href = "../login/UserLogIn.php";
        }
    })
    .catch(error => console.error(error));
}