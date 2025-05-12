document.addEventListener("DOMContentLoaded", function() {
    loadBus();
    
    // Set up modal close buttons
    const closeButtons = document.querySelectorAll(".close");
    closeButtons.forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("createBusModal").style.display = "none";
            document.getElementById("editBusModal").style.display = "none";
        });
    });
});

function loadBus() {
    fetch("../api/bus_api.php")
    .then(response => response.json())
    .then(data => {
        let BusTableBody = document.getElementById("BusTableBody");
        BusTableBody.innerHTML = "";

        if (data.status === "success" && data.buses) {
            data.buses.forEach(bus => {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td>${bus.bus_id}</td>
                    <td>${bus.location}</td>
                    <td>${bus.destination}</td>
                    <td>${bus.departure_time}</td>
                    <td>${bus.arrival_time}</td>
                    <td>${bus.available_seats}</td>
                    <td>${bus.bus_type}</td>
                    <td>${bus.price}</td>
                    <td>${bus.bus_number}</td>
                    <td>${bus.status}</td>
                `;
                
                row.style.cursor = "pointer";
                row.onclick = function() {
                    openUpdateModal(
                        bus.bus_id, 
                        bus.location, 
                        bus.destination, 
                        bus.departure_time,
                        bus.arrival_time, 
                        bus.available_seats,
                        bus.bus_type, 
                        bus.price, 
                        bus.bus_number,
                        bus.status
                    );
                };
                
                BusTableBody.appendChild(row);
            });
        } else {
            console.error("Error loading buses:", data.message || "Unknown error");
        }
    })
    .catch(error => {
        console.error("Error fetching data:", error);
    });
}

function openCreateBusModal() {
    document.getElementById("createBusModal").style.display = "block";
}

function closeCreateModal() {
    document.getElementById("createBusModal").style.display = "none";
}

function openUpdateModal(id, bus_number, location, destination, departure_time, arrival_time, bus_type, price, available_seats, status) {
    // Set form values
    document.getElementById("editBusId").value = id;
    document.getElementById("editNumber").value = bus_number;
    document.getElementById("editlocation-select").value = location;
    document.getElementById("editdestination-select").value = destination;
    
    try {
        // Format departure time for datetime-local input
        document.getElementById("editdepartureTime").value = formatDateTimeForInput(departure_time);
        document.getElementById("editarrivalTime").value = formatDateTimeForInput(arrival_time);
    } catch (e) {
        console.error("Error formatting dates:", e);
        document.getElementById("editdepartureTime").value = departure_time;
        document.getElementById("editarrivalTime").value = arrival_time;
    }
    
    document.getElementById("editbusType").value = bus_type;
    document.getElementById("editprice").value = price;
    document.getElementById("editavailableSeats").value = available_seats;
    document.getElementById("editStatus").value = status;

    // Show the modal
    document.getElementById("editBusModal").style.display = "block";
}

// Helper function to format time for input fields
function formatDateTimeForInput(timeStr) {
    // Assuming current date since we only have time in the database
    const today = new Date().toISOString().split('T')[0];
    return `${today}T${timeStr}`;
}

function closeEditModal() {
    document.getElementById("editBusModal").style.display = "none";
}

function createNewBus() {
    try {
        const departure = document.getElementById("departureTime").value;
        const arrival = document.getElementById("arrivalTime").value;
        const price = document.getElementById("price").value;
        const availableSeats = document.getElementById("availableSeats").value;
        const busNumber = document.getElementById("busNum").value;

        // Extract time portion from datetime inputs
        const departureTime = departure ? departure.split('T')[1] : '';
        const arrivalTime = arrival ? arrival.split('T')[1] : '';

        const formData = {
            bus_number: busNumber,
            location: document.getElementById("location-select").value,
            destination: document.getElementById("destination-select").value,
            departure_time: departureTime,
            arrival_time: arrivalTime,
            bus_type: document.getElementById("busType").value,
            price: price,
            available_seats: availableSeats,
            status: document.getElementById("status").value
        };

        // Validate data
        for (const key in formData) {
            if (formData[key] === "" || formData[key] == null) {
                alert(`${key.replace(/_/g, ' ')} is required!`);
                return;
            }
        }

        if (isNaN(price) || price <= 0) {
            alert("Price must be a positive number!");
            return;
        }
        if (isNaN(availableSeats) || availableSeats <= 0) {
            alert("Available seats must be a positive number!");
            return;
        }

        fetch("../api/bus_api.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeCreateModal();
                loadBus();
                // Reset form fields
                document.getElementById("location-select").value = "";
                document.getElementById("destination-select").value = "";
                document.getElementById("departureTime").value = "";
                document.getElementById("arrivalTime").value = "";
                document.getElementById("busType").value = "";
                document.getElementById("price").value = "";
                document.getElementById("availableSeats").value = "";
                document.getElementById("busNum").value = "";
                document.getElementById("status").value = "Available";
            }
        })
        .catch(error => {
            console.error("Error adding details:", error);
            alert("Failed to create new bus. Please try again.");
        });
    } catch (err) {
        console.error("Unexpected error:", err);
        alert("An unexpected error occurred. Check the console for details.");
    }
}

function updateBus() {
    try {
        const departure = document.getElementById("editdepartureTime").value;
        const arrival = document.getElementById("editarrivalTime").value;
        
        // Extract time portion from datetime inputs
        const departureTime = departure ? departure.split('T')[1] : '';
        const arrivalTime = arrival ? arrival.split('T')[1] : '';
        
        const formData = {
            id: document.getElementById("editBusId").value,
            location: document.getElementById("editlocation-select").value,
            destination: document.getElementById("editdestination-select").value,
            departure_time: departureTime,
            arrival_time: arrivalTime,
            available_seats: document.getElementById("editavailableSeats").value,
            bus_type: document.getElementById("editbusType").value,
            price: document.getElementById("editprice").value,
            bus_number: document.getElementById("editNumber").value,
            status: document.getElementById("editStatus").value
        };

        // Validate data
        for (const key in formData) {
            if (!formData[key]) {
                alert(`${key.replace('_', ' ')} is required!`);
                return;
            }
        }

        // Validate numeric fields
        if (isNaN(formData.price) || formData.price <= 0) {
            alert("Price must be a positive number!");
            return;
        }
        if (isNaN(formData.available_seats) || formData.available_seats <= 0) {
            alert("Available seats must be a positive number!");
            return;
        }

        fetch("../api/bus_api.php", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeEditModal();
                loadBus();
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to update bus. Please try again.");
        });
    } catch (err) {
        console.error("Unexpected error in updateBus:", err);
        alert("An unexpected error occurred. Check the console for details.");
    }
}

function filterBus() {
    const destination = document.getElementById("filterDestination").value;
    const location = document.getElementById("filterLocation").value;
    const busType = document.getElementById("filterBusType").value;
    
    let url = "../api/bus_api.php";
    const params = [];
    
    if (destination) params.push(`destination=${encodeURIComponent(destination)}`);
    if (location) params.push(`location=${encodeURIComponent(location)}`);
    if (busType) params.push(`bus_type=${encodeURIComponent(busType)}`);
    
    if (params.length > 0) {
        url += "?" + params.join("&");
    }
    
    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            let BusTableBody = document.getElementById("BusTableBody");
            BusTableBody.innerHTML = "";
            
            data.buses.forEach(bus => {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td>${bus.bus_id}</td>
                    <td>${bus.location}</td>
                    <td>${bus.destination}</td>
                    <td>${bus.departure_time}</td>
                    <td>${bus.arrival_time}</td>
                    <td>${bus.available_seats}</td>
                    <td>${bus.bus_type}</td>
                    <td>${bus.price}</td>
                    <td>${bus.bus_number}</td>
                    <td>${bus.status}</td>
                `;
                
                row.style.cursor = "pointer";
                row.onclick = function() {
                    openUpdateModal(
                        bus.bus_id,
                        bus.location, 
                        bus.destination, 
                        bus.departure_time,
                        bus.arrival_time, 
                        bus.available_seats,
                        bus.bus_type, 
                        bus.price, 
                        bus.bus_number,
                        bus.status
                    );
                };
                
                BusTableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
}

function deleteBus() {
    const id = document.getElementById("editBusId").value;
    
    if (!id) {
        alert("Bus ID is missing. Please open the edit modal for a valid bus.");
        return;
    }

    if (confirm("Are you sure you want to delete this bus?")) {
        fetch(`../api/bus_api.php?id=${encodeURIComponent(id)}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeEditModal();
                loadBus();
            } else {
                console.error("Delete failed:", data.message);
            }
        })
        .catch(error => {
            console.error("Error deleting bus:", error);
            alert("Failed to delete bus. Check the console for details.");
        });
    }
}