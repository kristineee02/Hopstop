document.addEventListener("DOMContentLoaded", function() {
    const searchForm = document.getElementById("search-form");
    const searchResultsContainer = document.getElementById("search-results");

    if (searchForm && searchResultsContainer) {
        console.log("Search form and results container found");
        
        searchForm.addEventListener("submit", function(event) {
            event.preventDefault();
            console.log("Form submitted");

            const fromInput = searchForm.querySelector("input[name='from']").value.trim();
            const toInput = searchForm.querySelector("input[name='to']").value.trim();

            console.log("Search values:", fromInput, toInput);

            if (!fromInput || !toInput) {
                alert("Please fill in both from and to locations");
                return;
            }
            // Redirect to booking page with search parameters
            window.location.href = `User_account_booking.php?location=${encodeURIComponent(fromInput)}&destination=${encodeURIComponent(toInput)}`;
        });
    } else {
        console.error("Search form or results container not found", {
            searchForm: !!searchForm,
            searchResultsContainer: !!searchResultsContainer
        });
    }
});

function logOut() {
    let confirmLogout = confirm("Are you sure you want to log out?");
    
    if (confirmLogout) {
        window.location.href = "../homepage/Homepage.php";
        sessionStorage.clear();
    }
}

function profile() {
    window.location.href = "user_profile.php";
}

// Profile dropdown functionality
document.addEventListener("DOMContentLoaded", function() {
    const profileButton = document.getElementById("profileButton");
    const profileDropdown = document.getElementById("profileDropdown");
    
    if (profileButton && profileDropdown) {
        profileButton.addEventListener("click", function() {
            profileDropdown.classList.toggle("show");
        });
        
        // Close the dropdown when clicking outside
        window.addEventListener("click", function(event) {
            if (!event.target.matches("profileButton") && 
                !event.target.closest("profileButton") && 
                !event.target.closest("profileDropdown")) {
                if (profileDropdown.classList.contains("show")) {
                    profileDropdown.classList.remove("show");
                }
            }
        });
    }
});