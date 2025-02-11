<?php
// mysql://root:gxPkBZZIbgVoHnnbTFWrGcHgOjzXOtQx@junction.proxy.rlwy.net:31788/railway
    $host = 'junction.proxy.rlwy.net';
    $username = 'root';
    $password = 'gxPkBZZIbgVoHnnbTFWrGcHgOjzXOtQx';
    $db = 'railway';
    $port = '31788';
    $conn = mysqli_connect($host, $username, $password. $db, $port);
    if (!$conn) {
        die('Failed to connect: ' . mysqli_connect_error());
    }

    // Create database
    // $createDatabase = "CREATE DATABASE IF NOT EXISTS Weathers;";
    // mysqli_query($conn, $createDatabase);

    // Select the created database
    mysqli_select_db($conn, 'railway');

    // Create table
    $createTable = "CREATE TABLE IF NOT EXISTS Cities (  
        city VARCHAR(255) PRIMARY KEY,
        country VARCHAR(255),
        Main_Weather VARCHAR(255),
        Icon_Code VARCHAR(100),
        Temp_Degree DECIMAL(10,2),
        Weather_Description VARCHAR(255),
        Pressure DECIMAL(10,2),
        Humidity DECIMAL(10,2),
        Wind_Speed DECIMAL(10,2),
        Wind_Direction DECIMAL(10,2),
        Get_Time DATETIME DEFAULT CURRENT_TIMESTAMP
    );";
    mysqli_query($conn, $createTable);

    // Fetch the city from the query parameter or default to "Sheffield"
    if(isset($_GET['q'])){
        $cityname = $_GET['q'];
        // echo $cityname;
    }else{
        $cityname = 'Sheffield';
    } 

    // Function to fetch and store weather data
    function fetchWeatherData($conn, $city) {
        $api_key = 'ba1e28eed3bfd2b911040b4b65f325c8';
        $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&units=metric&appid=$api_key";

        $city_data = file_get_contents($url);
        if ($city_data === false) {
            die('Error fetching data from the API.');
        }
        $data = json_decode($city_data, true);

        $city_name = $data['name'];
        $country = $data['sys']['country'];
        $main_weather = $data['weather'][0]['main'];
        $weather_desc = $data['weather'][0]['description'];
        $temp_degree = $data['main']['temp'];
        $icon_code = $data['weather'][0]['icon'];
        $pressure = $data['main']['pressure'];
        $humidity = $data['main']['humidity'];
        $wind_speed = $data['wind']['speed'];
        $wind_direction = $data['wind']['deg'];

        $sql_insert = "INSERT INTO Cities (City, country, Main_Weather, Weather_Description, Temp_Degree, Icon_Code, Pressure, Humidity, Wind_Speed, Wind_Direction) 
            VALUES ('$city_name', '$country', '$main_weather', '$weather_desc', '$temp_degree', '$icon_code', '$pressure', '$humidity', '$wind_speed', '$wind_direction')
            ON DUPLICATE KEY UPDATE 
            Main_Weather = '$main_weather', Weather_Description = '$weather_desc', Temp_Degree = '$temp_degree',
            Icon_Code = '$icon_code', Pressure = '$pressure', Humidity = '$humidity', Wind_Speed = '$wind_speed', Wind_Direction = '$wind_direction', Get_Time = CURRENT_TIMESTAMP;";

        mysqli_query($conn, $sql_insert);

        $sql_select = "SELECT * FROM Cities WHERE city = '$city_name';";
        $result = mysqli_query($conn, $sql_select);

        $finaldata = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $finaldata[] = $row;
            }
        }

        return $finaldata;
    }

    // Fetch weather data
$finaldata = fetchWeatherData($conn, $cityname);

    // Handle empty data
    if (empty($finaldata)) {
        $finaldata = [];
        }

        $json_data = json_encode($finaldata);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    echo $json_data;
?>
