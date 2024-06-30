<?php

require_once '../config/database.php';
require_once '../src/controllers/RealEstateController.php';

$controller = new RealEstateController($pdo);

$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
$requestMethod = $_SERVER['REQUEST_METHOD'];    

switch ($requestMethod) {
    case 'GET':
        switch ($requestUri) {
            case '/importDataFromCSV':
                $csvFilePath = isset($_GET['csvFilePath']) ? $_GET['csvFilePath'] : '';
                echo $controller->importDataFromCSV($csvFilePath);
                break;

            case '/filterProperties':
                $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
                $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : PHP_INT_MAX;
                $bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : 1;
                echo $controller->filterProperties($minPrice, $maxPrice, $bedrooms);
                break;

            case '/calculateAveragePrice':
                $latitude = isset($_GET['latitude']) ? $_GET['latitude'] : 0;
                $longitude = isset($_GET['longitude']) ? $_GET['longitude'] : 0;
                $distance = isset($_GET['distance']) ? $_GET['distance'] : 1;
                echo $controller->calculateAveragePrice($latitude, $longitude, $distance);
                break;

            case '/generateReport':
                $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
                $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : PHP_INT_MAX;
                $bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : 1;
                $latitude = isset($_GET['latitude']) ? $_GET['latitude'] : 0;
                $longitude = isset($_GET['longitude']) ? $_GET['longitude'] : 0;
                $distance = isset($_GET['distance']) ? $_GET['distance'] : 1;
                $format = isset($_GET['format']) ? $_GET['format'] :'pdf';
                echo $controller->generateReport($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance, $format);
                break;
                
            default:
                http_response_code(404);
                echo json_encode(['message' => 'Not Found']);
                break;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}
