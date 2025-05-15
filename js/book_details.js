document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded, calling getAllBusDetails()");
    getAllBusDetails();

    function getAllBusDetails() {
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
                    displayBusDetails(data.buses);
                } else {
                    console.error("Error fetching buses:", data.message);
                    const busContainer = document.getElementById("book-details");
                    if (busContainer) {
                        busContainer.innerHTML = `<p class="error-message">Failed to load bus data: ${data.message || "Unknown error"}</p>`;
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching buses:", error);
                const busContainer = document.getElementById("book-details");
                if (busContainer) {
                    busContainer.innerHTML = `<p class="error-message">Failed to load bus data. Please try again later.</p>`;
                }
            });
    }

    function displayBusDetails(buses) {
        console.log("Displaying buses:", buses);
        const busContainer = document.getElementById("book-details");
        
        if (!busContainer) {
            console.error("Error: book-details element not found in the DOM");
            return;
        }
        
        busContainer.innerHTML = '';
        
        if (!buses || buses.length === 0) {
            console.log("No buses to display");
            busContainer.innerHTML = '<p class="no-trips">No buses available at the moment.</p>';
            return;
        }
        
        console.log(`Creating ${buses.length} bus cards...`);
        
        buses.forEach((bus) => {
            const busCard = document.createElement("div");
            busCard.className = "detail-row";
            busCard.dataset.id = bus.bus_id;
            
            // Create the bus details container
            const busDetails = document.createElement("div");
            busDetails.className = "bus-details";
            
            const formattedPrice = parseFloat(bus.price).toFixed(2);
            
            busDetails.innerHTML = `
                <p class="bus-id">Bus: ${bus.bus_number}</p>
                <p class="bus-route">Route: ${bus.location} → ${bus.destination}</p>
                <p class="bus-time">Departure: ${bus.departure_time}</p>
                <p class="bus-time">Arrival: ${bus.arrival_time}</p>
                <p class="bus-price">Price: PHP ${formattedPrice}</p>
                <p class="bus-status">Status: ${bus.status}</p>
                <p class="bus-seats">Available Seats: ${bus.available_seats}</p>
                <p class="bus-type">Bus Type: ${bus.bus_type}</p>
            `;
            
            // Add the bus details to the bus card
            busCard.appendChild(busDetails);
            
            const bookButton = document.createElement("button");
            bookButton.className = "book-button";
            bookButton.textContent = "Book now";
            bookButton.onclick = function() {
                window.location.href = `User_account_ticket_form.php?id=${bus.bus_id}`;
            };
            
            busCard.appendChild(bookButton);
            
            busContainer.appendChild(busCard);
        });
        
        console.log("Bus display complete");
    }
});