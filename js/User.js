// Search functionality
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

            // Redirect to booking page with search parameters instead of making API call
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

function bookBus(busId) {
    console.log("Booking bus:", busId);

    window.location.href = `User_account_booking.php?bus_id=${busId}`;
}