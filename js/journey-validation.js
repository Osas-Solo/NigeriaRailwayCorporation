let arrivalDateInput = document.getElementById("journey-date");

let date = new Date();
const numberOfMilliSecondsInADay = 86400000;
date = new Date(date.getTime() + numberOfMilliSecondsInADay);

let currentYear = date.getFullYear();
let currentMonth = date.getMonth() + 1;
let currentDay = date.getDate();

dateString = currentYear + "-";
dateString += (currentMonth < 10) ? ("0" + currentMonth) : currentMonth;
dateString += "-";
dateString += (currentDay < 10) ? ("0" + currentDay) : currentDay;

arrivalDateInput.setAttribute("min", dateString);

function updateDestinations() {
    let locationList = ["Warri", "Benin", "Asaba", "Lagos", "Onitsha", "Port Harcourt"];

    let stationInput = document.getElementById("station");
    let destinationInput = document.getElementById("destination");

    let station = stationInput.value;
    let destination = destinationInput.value;

    locationList = locationList.filter(location => location.localeCompare(station) != 0);

    destinationInput.options.length = 0;
    locationList.forEach(location => {
        let option = document.createElement("option");
        option.setAttribute("value", location);
        option.innerHTML = location;
        destinationInput.appendChild(option);
    });
}