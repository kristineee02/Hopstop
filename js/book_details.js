document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM fully loaded, fetching bus details");
    getBusDetails();
});

function getBusDetails() {
    const url = new URL(window.location.href);
    const busId = url.searchParams.get("id");

    if (!busId) {
        console.error("No bus ID found in URL");
        const busContainer = document.getElementById("bus");
        if (busContainer) {
            busContainer.innerHTML = `<p class="error-message">No bus ID provided.</p>`;
        }
        return;
    }

    console.log(`Fetching details for bus ID: ${busId}`);
    fetch(`../api/bus_api.php?id=${busId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("API Response data:", data);
            if (data.status === "success" && data.bus) {
                console.log("Bus received:", data.bus);
                displayBusDetails(data.bus);
            } else {
                console.error("Error fetching bus:", data.message || "Unknown error");
                const busContainer = document.getElementById("bus");
                if (busContainer) {
                    busContainer.innerHTML = `<p class="error-message">Failed to load bus data: ${data.message || "Unknown error"}</p>`;
                }
            }
        })
        .catch(error => {
            console.error("Failed to fetch bus data:", error);
            const busContainer = document.getElementById("bus");
            if (busContainer) {
                busContainer.innerHTML = `<p class="error-message">Failed to load bus data. Please try again later.</p>`;
            }
        });
}

function displayBusDetails(bus) {
    console.log("Displaying bus details:", bus);
    const fieldMappings = {
        busDisplay: bus.bus_number,
        fromDisplay: bus.location,
        toDisplay: bus.destination,
        departureDisplay: bus.departure_time,
        arrivalDisplay: bus.arrival_time,
        typeDisplay: bus.bus_type,
        priceDisplay: `PHP ${parseFloat(bus.price).toFixed(2)}`,
        seatsDisplay: bus.available_seats
    };

    Object.keys(fieldMappings).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = fieldMappings[id] || "N/A";
        } else {
            console.warn(`Element with ID ${id} not found`);
        }
    });

    const bookButton = document.querySelector(".book-button");
    if (bookButton) {
        bookButton.addEventListener("click", function () {
            window.location.href = `User_account_ticket_form.php?bus_id=${bus.bus_id}`;
        });
    } else {
        console.warn("Book button not found");
    }
}