document.addEventListener("DOMContentLoaded", function () {
    getPassenger();
});

function getPassenger() {
    fetch("../api/store_session.php")
        .then(response => response.json())
        .then(data => {
            if (!data.userId) {
                console.error("User not authenticated");
                return;
            }
            return fetch("../api/passenger_api.php");
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const content = document.getElementById("content");
                content.innerHTML = ""; 

                data.passengers.forEach(passenger => {
                    content.innerHTML += `
                        <tr>
                            <td>${passenger.first_name}</td>
                            <td>${passenger.last_name}</td>
                            <td>${passenger.email}</td>
                            <td class="actions-cell">
                                <button class="delete-btn" data-id="${passenger.passenger_id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                document.querySelectorAll(".delete-btn").forEach(button => {
                    button.addEventListener("click", function (event) {
                        event.preventDefault();
                        const passengerId = this.getAttribute("data-id");
                        deletePassenger(passengerId);
                    });
                });
            } else {
                console.error("Error fetching passengers:", data.message);
            }
        })
        .catch(error => console.error("Error:", error));
}

function deletePassenger(id) {
    fetch("../api/passenger_api.php", {
        method: "DELETE",
        body: JSON.stringify({ PassengerId: Number(id) }), 
        headers: { "Content-Type": "application/json" }
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Passenger deleted successfully!");
                window.location.reload();
            } else {
                console.error("Error deleting passenger:", data.message);
                alert("Failed to delete passenger: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while deleting the passenger.");
        });
}