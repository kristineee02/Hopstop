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
});

function logOut() {
    let confirmLogout = confirm("Are you sure you want to log out?");
    
    if (confirmLogout) {
        window.location.href = "../homepage/Homepage.php";
        sessionStorage.clear();
    }
}
