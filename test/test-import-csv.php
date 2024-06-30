<?php

require_once '../config/database.php';
require_once '../src/classes/realEstateManager.php';

$realEstateManager = new realEstateManager($pdo);

$csvFilePath = 'C:\Users\Franco\Downloads\resource_accommodation.csv';

try {
    $realEstateManager->importDataFromCSV($csvFilePath);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
