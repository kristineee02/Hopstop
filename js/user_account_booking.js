document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded, checking for search parameters");
    
    // Get search parameters from URL
    const urlParams = new URLSearchParams(window.location.search);
    const location = urlParams.get('location');
    const destination = urlParams.get('destination');
    const busId = urlParams.get('id');
    
    if (location && destination) {
        // If we have search parameters, display buses based on search
        searchBuses(location, destination);
    } else if (busId) {
        // If we have a bus ID, fetch and display only that bus
        getBusById(busId);
    } else {
        // Otherwise, get all buses
        getAllBuses();
    }
    
    function searchBuses(location, destination) {
        console.log(`Searching for buses from ${location} to ${destination}`);
        
        // Update search results heading
        const tripHeading = document.querySelector(".trip");
        if (tripHeading) {
            tripHeading.textContent = `Available Tickets: ${location} to ${destination}`;
        }
        
        fetch(`../api/bus_api.php?action=search&location=${encodeURIComponent(location)}&destination=${encodeURIComponent(destination)}`)
            .then(response => {
                console.log("API Response status:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("Search API Response data:", data);
                if (data.status === "success") {
                    console.log("Buses received:", data.buses);
                    displayBuses(data.buses);
                } else {
                    console.error("Error searching buses:", data.message);
                    const tripsContainer = document.getElementById("trips-container");
                    if (tripsContainer) {
                        tripsContainer.innerHTML = `<p class="error-message">No buses available for this route. Please try a different route.</p>`;
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching buses:", error);
                const tripsContainer = document.getElementById("trips-container");
                if (tripsContainer) {
                    tripsContainer.innerHTML = `<p class="error-message">Failed to search buses. Please try again later.</p>`;
                }
            });
    }
    
    function getBusById(id) {
        console.log(`Fetching bus with ID: ${id}`);
        fetch(`../api/bus_api.php?id=${id}`)
            .then(response => {
                console.log("API Response status:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("API Response data:", data);
                if (data.status === "success" && data.bus) {
                    console.log("Bus received:", data.bus);
                    displayBuses([data.bus]);
                } else {
                    console.error("Error fetching bus:", data.message);
                    const tripsContainer = document.getElementById("trips-container");
                    if (tripsContainer) {
                        tripsContainer.innerHTML = `<p class="error-message">Failed to load bus data: ${data.message || "Bus not found"}</p>`;
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching bus:", error);
                const tripsContainer = document.getElementById("trips-container");
                if (tripsContainer) {
                    tripsContainer.innerHTML = `<p class="error-message">Failed to load bus data. Please try again later.</p>`;
                }
            });
    }

    function getAllBuses() {
        console.log("Fetching all buses...");
        fetch("../api/bus_api.php")
            .then(response => {
                console.log("API Response status:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("API Response data:", data);
                if (data.status === "success") {
                    console.log("Buses received:", data.buses);
                    displayBuses(data.buses);
                } else {
                    console.error("Error fetching buses:", data.message);
                    const tripsContainer = document.getElementById("trips-container");
                    if (tripsContainer) {
                        tripsContainer.innerHTML = `<p class="error-message">Failed to load bus data: ${data.message || "Unknown error"}</p>`;
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching buses:", error);
                const tripsContainer = document.getElementById("trips-container");
                if (tripsContainer) {
                    tripsContainer.innerHTML = `<p class="error-message">Failed to load bus data. Please try again later.</p>`;
                }
            });
    }

    function displayBuses(buses) {
        console.log("Displaying buses:", buses);
        const tripsContainer = document.getElementById("trips-container");
        
        if (!tripsContainer) {
            console.error("Error: trips-container element not found in the DOM");
            return;
        }
        
        tripsContainer.innerHTML = '';
        
        if (!buses || buses.length === 0) {
            console.log("No buses to display");
            tripsContainer.innerHTML = '<p class="no-trips">No buses available matching your criteria.</p>';
            return;
        }
        
        console.log(`Creating ${buses.length} bus cards...`);
        
        buses.forEach((bus) => {
            const tripCard = document.createElement("div");
            tripCard.className = "trip-card";
            tripCard.dataset.id = bus.bus_id;
            
            // Create the trip details container
            const tripDetails = document.createElement("div");
            tripDetails.className = "trip-details";
            
            const formattedPrice = parseFloat(bus.price).toFixed(2);
            
            // Add trip details
            tripDetails.innerHTML = `
                <p class="trip-id">Bus: ${bus.bus_number}</p>
                <p class="trip-route">Route: ${bus.location} → ${bus.destination}</p>
                <p class="trip-time">Departure: ${bus.departure_time}</p>
                <p class="trip-time">Arrival: ${bus.arrival_time}</p>
                <p class="trip-price">Price: PHP ${formattedPrice}</p>
                <p class="trip-status">Status: ${bus.status}</p>
                <p class="trip-seats">Available Seats: ${bus.available_seats}</p>
                <p class="trip-type">Bus Type: ${bus.bus_type}</p>
            `;
            
            // Add the trip details to the trip card
            tripCard.appendChild(tripDetails);
            
            // Create the "Book now" button
            const bookButton = document.createElement("button");
            bookButton.className = "book-button";
            bookButton.textContent = "Book now";
            bookButton.onclick = function() {
                window.location.href = `User_account_book_details.php?id=${bus.bus_id}`;
            };
            
            // Add the button to the trip card
            tripCard.appendChild(bookButton);
            
            // Add the trip card to the trips container
            tripsContainer.appendChild(tripCard);
        });
        
        console.log("Bus display complete");
    }
});