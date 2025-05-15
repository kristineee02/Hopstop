document.addEventListener("DOMContentLoaded", function() {
    console.log("Book details page loaded");
    
    // Get bus ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const busId = urlParams.get('id');
    
    if (busId) {
        console.log(`Fetching details for bus ID: ${busId}`);
        getBusDetails(busId);
    } else {
        console.error("No bus ID provided in URL");
        const busDetailsContainer = document.getElementById("book-details");
        if (busDetailsContainer) {
            busDetailsContainer.innerHTML = '<p class="error-message">No bus selected. Please go back and select a bus.</p>';
        }
    }

    function getBusDetails(id) {
        fetch(`../api/bus_api.php?id=${id}`)
            .then(response => {
                console.log("API Response status:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("API Response data:", data);
                if (data.status === "success" && data.bus) {
                    console.log("Bus details received:", data.bus);
                    displayBusDetails(data.bus);
                } else {
                    console.error("Error fetching bus details:", data.message);
                    const busDetailsContainer = document.getElementById("book-details");
                    if (busDetailsContainer) {
                        busDetailsContainer.innerHTML = `<p class="error-message">Failed to load bus details: ${data.message || "Bus not found"}</p>`;
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching bus details:", error);
                const busDetailsContainer = document.getElementById("book-details");
                if (busDetailsContainer) {
                    busDetailsContainer.innerHTML = `<p class="error-message">Failed to load bus details. Please try again later.</p>`;
                }
            });
    }

    function displayBusDetails(bus) {
        console.log("Displaying bus details:", bus);
        const busDetailsContainer = document.getElementById("book-details");
        
        if (!busDetailsContainer) {
            console.error("Error: book-details element not found in the DOM");
            return;
        }
        
        busDetailsContainer.innerHTML = '';
        
        if (!bus) {
            console.log("No bus details to display");
            busDetailsContainer.innerHTML = '<p class="error-message">Bus information not available.</p>';
            return;
        }
        
        const formattedPrice = parseFloat(bus.price).toFixed(2);
        
        // Create the bus details display
        const detailsContainer = document.createElement("div");
        detailsContainer.className = "detail-row";
        
        detailsContainer.innerHTML = `
            <div class="detail-item">
                <div class="detail-label">BUS ID:</div>
                <div class="detail-value">${bus.bus_id}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Bus Number:</div>
                <div class="detail-value">${bus.bus_number}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">From:</div>
                <div class="detail-value">${bus.location}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">To:</div>
                <div class="detail-value">${bus.destination}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Departure:</div>
                <div class="detail-value">${bus.departure_time}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Arrival:</div>
                <div class="detail-value">${bus.arrival_time}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Bus Type:</div>
                <div class="detail-value">${bus.bus_type}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Price:</div>
                <div class="detail-value">PHP ${formattedPrice}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Available Seats:</div>
                <div class="detail-value">${bus.available_seats}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status:</div>
                <div class="detail-value">${bus.status}</div>
            </div>
        `;
        
        busDetailsContainer.appendChild(detailsContainer);
        
        // Add the book button
        const bookButton = document.createElement("button");
        bookButton.className = "book-button";
        bookButton.textContent = "Book now";
        bookButton.onclick = function() {
            window.location.href = `User_account_ticket_form.php?id=${bus.bus_id}`;
        };
        
        busDetailsContainer.appendChild(bookButton);
        
        console.log("Bus details display complete");
    }
});