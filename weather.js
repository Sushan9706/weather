document.getElementById("date").textContent = new Date().toDateString();

async function fetchWeatherData(city) {
    try {
        // Convert city name to lowercase to ensure unique key for each city
        const cityLowerCase = city.toLowerCase();

        // Check if weather data for the city is in localStorage
        const cachedWeatherData = localStorage.getItem(cityLowerCase);

        if (cachedWeatherData) {
            // If data exists in localStorage, use it
            const data = JSON.parse(cachedWeatherData);
            console.log("Using cached data for", city);
            updateWeatherUI(data);
        } else {
            // Fetch weather data from API
            const response = await fetch(`http://localhost/phpfile/weatherapp/weather.php?q=${city}`);
            const data = await response.json();

            if (!city) {
                alert('Please enter a city name!');
                return;
            }

            // Log entire API response for debugging
            console.log("Fetched new data for", city);

            // Cache the new data in localStorage (using lowercase city name as key)
            localStorage.setItem(cityLowerCase, JSON.stringify(data));

            updateWeatherUI(data);
        }
    } catch (err) {
        // Display errors in the console
        alert('Could not process!');
    }
}

function updateWeatherUI(data) {
    const cityNameElem = document.querySelector(".city-name");
    const temperatureElem = document.querySelector(".temperature");
    const pressureElem = document.querySelector(".pressure");
    const humidityElem = document.querySelector(".humidity");
    const windSpeedElem = document.querySelector(".wind-speed");
    const windDirectionElem = document.querySelector(".wind-direction");
    const weatherIcon = document.querySelector('.weather-image');
    const weatherCondition = document.querySelector('#main-weather');
    
    const iconCode = data[0].Icon_Code;
    weatherCondition.innerHTML = data[0].Main_Weather;
    cityNameElem.innerHTML = data[0].city;
    temperatureElem.innerHTML = data[0].Temp_Degree + "°C";
    pressureElem.innerHTML = data[0].Pressure + " hPa";
    humidityElem.innerHTML = data[0].Humidity + "%";
    windSpeedElem.innerHTML = data[0].Wind_Speed + " m/s";
    windDirectionElem.innerHTML = data[0].Wind_Direction + '°';
    weatherIcon.src = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
}

// Event listener for search button click
document.querySelector(".search-box button").addEventListener("click", () => {
    const city = document.querySelector(".search-box input").value;
    fetchWeatherData(city); // Trigger weather check for the entered city
    document.querySelector(".search-box input").value = ""; // Clear input field
});

// Fetch weather data for the default city
fetchWeatherData("Sheffield");
