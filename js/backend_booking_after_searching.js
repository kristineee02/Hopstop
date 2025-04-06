/**
 * Search function for handling bus/travel routes between specific destinations
 * Supports: Zamboanga, Manila, Cebu, and Davao
 * @param {string} from - Origin location
 * @param {string} to - Destination location
 * @param {number} passengers - Number of passengers (optional)
 * @returns {Array} - Array of matching routes
 */
function searchDestinations(from, to, passengers = 1) {
    // Normalize input to handle case insensitivity and trimming
    from = from.trim().toLowerCase();
    to = to.trim().toLowerCase();
    
    // Available destinations
    const validDestinations = ['zamboanga', 'manila', 'cebu', 'davao'];
    
    // Check if destinations are valid
    if (!validDestinations.includes(from)) {
        return { error: `Invalid origin: ${from}. Please choose from Zamboanga, Manila, Cebu, or Davao.` };
    }
    
    if (!validDestinations.includes(to)) {
        return { error: `Invalid destination: ${to}. Please choose from Zamboanga, Manila, Cebu, or Davao.` };
    }
    
    if (from === to) {
        return { error: "Origin and destination cannot be the same." };
    }
    
    // Sample routes database (in a real app, this would come from a backend service)
    const routes = [
        {
            id: "012-123",
            from: "zamboanga",
            to: "manila",
            departure: "6AM",
            arrival: "10PM",
            price: 1200,
            image: "https://example.com/bus1.jpg",
            availableSeats: 45
        },
        {
            id: "012-124",
            from: "zamboanga",
            to: "manila",
            departure: "6AM",
            arrival: "10PM",
            price: 1300,
            image: "https://example.com/bus2.jpg",
            availableSeats: 32
        },
        {
            id: "012-098",
            from: "zamboanga",
            to: "manila",
            departure: "6AM",
            arrival: "10PM",
            price: 1250,
            image: "https://example.com/bus3.jpg",
            availableSeats: 28
        },
        {
            id: "011-456",
            from: "zamboanga",
            to: "manila",
            departure: "6AM",
            arrival: "10PM",
            price: 1150,
            image: "https://example.com/bus4.jpg",
            availableSeats: 15
        },
        {
            id: "023-789",
            from: "manila",
            to: "zamboanga",
            departure: "7AM",
            arrival: "11PM",
            price: 1200,
            image: "https://example.com/bus5.jpg",
            availableSeats: 40
        },
        {
            id: "034-567",
            from: "cebu",
            to: "davao",
            departure: "8AM",
            arrival: "3PM",
            price: 800,
            image: "https://example.com/bus6.jpg",
            availableSeats: 52
        },
        {
            id: "034-568",
            from: "davao",
            to: "cebu",
            departure: "7AM",
            arrival: "2PM",
            price: 850,
            image: "https://example.com/bus7.jpg",
            availableSeats: 48
        },
        {
            id: "045-123",
            from: "manila",
            to: "cebu",
            departure: "5AM",
            arrival: "4PM",
            price: 1500,
            image: "https://example.com/bus8.jpg",
            availableSeats: 38
        },
        {
            id: "045-124",
            from: "cebu",
            to: "manila",
            departure: "6AM",
            arrival: "5PM",
            price: 1450,
            image: "https://example.com/bus9.jpg",
            availableSeats: 42
        },
        {
            id: "056-234",
            from: "manila",
            to: "davao",
            departure: "7AM",
            arrival: "8PM",
            price: 1600,
            image: "https://example.com/bus10.jpg",
            availableSeats: 35
        },
        {
            id: "056-235",
            from: "davao",
            to: "manila",
            departure: "7AM",
            arrival: "8PM",
            price: 1650,
            image: "https://example.com/bus11.jpg",
            availableSeats: 30
        },
        {
            id: "067-345",
            from: "zamboanga",
            to: "cebu",
            departure: "5AM",
            arrival: "4PM",
            price: 950,
            image: "https://example.com/bus12.jpg",
            availableSeats: 46
        },
        {
            id: "067-346",
            from: "cebu",
            to: "zamboanga",
            departure: "6AM",
            arrival: "5PM",
            price: 900,
            image: "https://example.com/bus13.jpg",
            availableSeats: 50
        },
        {
            id: "078-456",
            from: "zamboanga",
            to: "davao",
            departure: "6AM",
            arrival: "6PM",
            price: 1100,
            image: "https://example.com/bus14.jpg",
            availableSeats: 44
        },
        {
            id: "078-457",
            from: "davao",
            to: "zamboanga",
            departure: "7AM",
            arrival: "7PM",
            price: 1150,
            image: "https://example.com/bus15.jpg",
            availableSeats: 38
        }
    ];
    
    // Filter routes based on search criteria
    const filteredRoutes = routes.filter(route => 
        route.from === from && 
        route.to === to && 
        route.availableSeats >= passengers
    );
    
    if (filteredRoutes.length === 0) {
        if (routes.some(route => route.from === from && route.to === to)) {
            return { 
                error: `No available routes from ${from} to ${to} for ${passengers} passengers.`,
                message: "Try reducing the number of passengers or choosing a different date."
            };
        } else {
            return { 
                error: `No routes available from ${from} to ${to}.`,
                message: "Please try different locations."
            };
        }
    }
    
    return {
        success: true,
        routes: filteredRoutes,
        passengerCount: passengers,
        message: `Found ${filteredRoutes.length} routes from ${from} to ${to}`
    };
}

// Example of how to display results in UI
function displaySearchResults(results) {
    const container = document.getElementById("trips-container");
    container.innerHTML = ''; // Clear previous results
    
    if (results.error) {
        const errorDiv = document.createElement("div");
        errorDiv.className = "error-message";
        errorDiv.textContent = results.error;
        
        if (results.message) {
            const suggestionDiv = document.createElement("div");
            suggestionDiv.className = "suggestion";
            suggestionDiv.textContent = results.message;
            errorDiv.appendChild(suggestionDiv);
        }
        
        container.appendChild(errorDiv);
        return;
    }
    
    results.routes.forEach(route => {
        const tripCard = document.createElement("div");
        tripCard.className = "trip-card";
        
        // Format route display with proper capitalization
        const fromCapitalized = route.from.charAt(0).toUpperCase() + route.from.slice(1);
        const toCapitalized = route.to.charAt(0).toUpperCase() + route.to.slice(1);
        
        tripCard.innerHTML = `
            <div class="trip-image">
                <img src="/api/placeholder/200/120" alt="Bus Image">
            </div>
            <div class="trip-details">
                <div class="trip-route">${fromCapitalized} - ${toCapitalized}</div>
                <div class="trip-time">Departure: ${route.departure}</div>
                <div class="trip-time">Arrival: ${route.arrival}</div>
                <div class="trip-price">Price: ₱${route.price}</div>
                <div class="trip-id">NO. ${route.id}</div>
            </div>
            <button class="book-button" data-id="${route.id}">Book now</button>
        `;
        
        container.appendChild(tripCard);
    });
    
    // Add event listeners to book buttons
    document.querySelectorAll('.book-button').forEach(button => {
        button.addEventListener('click', function() {
            const routeId = this.getAttribute('data-id');
            bookTicket(routeId, results.passengerCount);
        });
    });
}

// Function to handle the booking process
function bookTicket(routeId, passengers) {
    alert(`Booking process initiated for route #${routeId} with ${passengers} passenger(s)!`);
    // In a real app, this would connect to a backend service
}

// Search form handler
function handleSearchForm(event) {
    event.preventDefault();
    
    const fromInput = document.getElementById("from-location").value;
    const toInput = document.getElementById("to-location").value;
    const passengersInput = document.getElementById("passengers").value || 1;
    
    const results = searchDestinations(fromInput, toInput, parseInt(passengersInput));
    displaySearchResults(results);
}

// Initialize when the DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    const searchForm = document.getElementById("search-form");
    if (searchForm) {
        searchForm.addEventListener("submit", handleSearchForm);
    }
    
    // Example of direct search function usage
    // const results = searchDestinations("zamboanga", "manila", 2);
    // displaySearchResults(results);
});