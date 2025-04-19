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
                    <td>${bus.id}</td>
                    <td>${bus.location}</td>
                    <td>${bus.destination}</td>
                    <td>${bus.departure_time}</td>
                    <td>${bus.arrival_time}</td>
                    <td>${bus.bus_type}</td>
                    <td>${bus.price}</td>
                    <td>${bus.available_seats}</td>
                    <td>${bus.status}</td>
                
                `;
                
                // Make the row clickable
                row.style.cursor = "pointer";
                row.onclick = function() {
                    openUpdateModal(
                        bus.id, 
                        bus.location, 
                        bus.destination, 
                        bus.departure_time,
                        bus.arrival_time, 
                        bus.bus_type, 
                        bus.price, 
                        bus.available_seats,
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

function openUpdateModal(id, location, destination, departure_time, arrival_time, bus_type, price, available_seats, status) {
    // Set form values
    document.getElementById("editBusId").value = id;
    document.getElementById("editStatus").value = status;
    document.getElementById("editlocation-select").value = location;
    document.getElementById("editdestination-select").value = destination;
    
    // Handle datetime inputs - convert to format expected by datetime-local inputs
    try {
        const departureDate = new Date(departure_time);
        const arrivalDate = new Date(arrival_time);
        
        // Format date for datetime-local (YYYY-MM-DDThh:mm)
        document.getElementById("editdepartureTime").value = 
            departureDate.toISOString().slice(0, 16);
        document.getElementById("editarrivalTime").value = 
            arrivalDate.toISOString().slice(0, 16);
    } catch (e) {
        console.error("Error formatting dates:", e);
        // If date conversion fails, set as-is
        document.getElementById("editdepartureTime").value = departure_time;
        document.getElementById("editarrivalTime").value = arrival_time;
    }
    
    document.getElementById("editbusType").value = bus_type;
    document.getElementById("editprice").value = price;
    document.getElementById("editavailableSeats").value = available_seats;

    // Show the modal
    document.getElementById("editBusModal").style.display = "block";
}

function closeEditModal() {
    document.getElementById("editBusModal").style.display = "none";
}

function createNewBus() {
    try {
        const formData = {
            id: document.getElementById("busId")?.value,
            location: document.getElementById("location-select")?.value,
            destination: document.getElementById("destination-select")?.value,
            departure_time: document.getElementById("departureTime")?.value,
            arrival_time: document.getElementById("arrivalTime")?.value,
            bus_type: document.getElementById("busType")?.value,
            price: document.getElementById("price")?.value,
            available_seats: document.getElementById("availableSeats")?.value,
            status: document.getElementById("status")?.value
        };

        // Validate data
        for (const key in formData) {
            if (formData[key] === "" || formData[key] == null) {
                alert(`${key.replace(/_/g, ' ')} is required!`);
                return;
            }
        }

        fetch("../api/bus_api.php", {
            method: "POST",
            body: JSON.stringify(formData)
        })
        
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeCreateModal();
                loadBus();
                // Reset form fields
                document.getElementById("busId").value = "";
                document.getElementById("location-select").value = "";
                document.getElementById("destination-select").value = "";
                document.getElementById("departureTime").value = "";
                document.getElementById("arrivalTime").value = "";
                document.getElementById("busType").value = "";
                document.getElementById("price").value = "";
                document.getElementById("availableSeats").value = "";
                document.getElementById("status").value = "";
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
    const formData = {
        id: document.getElementById("editBusId").value,
        status: document.getElementById("editStatus").value,
        location: document.getElementById("editlocation-select").value,
        destination: document.getElementById("editdestination-select").value,
        departure_time: document.getElementById("editdepartureTime").value,
        arrival_time: document.getElementById("editarrivalTime").value,
        bus_type: document.getElementById("editbusType").value,
        price: document.getElementById("editprice").value,
        available_seats: document.getElementById("editavailableSeats").value
    };

    // Validate data
    for (const key in formData) {
        if (!formData[key]) {
            alert(`${key.replace('_', ' ')} is required!`);
            return;
        }
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
    });
}

function deleteBus() {
    const id = document.getElementById("editBusId").value;
    
    if (confirm("Are you sure you want to delete this bus?")) {
        fetch(`../api/bus_api.php?id=${id}`, {
            method: "DELETE"
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
        });
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
                    <td>${bus.id}</td>
                    <td>${bus.location}</td>
                    <td>${bus.destination}</td>
                    <td>${bus.departure_time}</td>
                    <td>${bus.arrival_time}</td>
                    <td>${bus.bus_type}</td>
                    <td>${bus.price}</td>
                    <td>${bus.available_seats}</td>
                    <td>${bus.status}</td>
                    <td>
                        <button class="status-btn">Active</button>
                    </td>
                `;
                
                row.style.cursor = "pointer";
                row.onclick = function() {
                    openUpdateModal(
                        bus.id, 
                        bus.location, 
                        bus.destination, 
                        bus.departure_time,
                        bus.arrival_time, 
                        bus.bus_type, 
                        bus.price, 
                        bus.available_seats,
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

