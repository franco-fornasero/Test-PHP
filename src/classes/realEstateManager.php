<?php 

require_once '../config/database.php';

class realEstateManager {
    
    private $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function importDataFromCSV($csvFilePath)
    {
        try {
            // Open the CSV file for reading
            $csvFile = fopen($csvFilePath, 'r');
            if ($csvFile === false) {
                throw new Exception('Cannot open the CSV file');
            }

            // Read the first line to get the column names
            $headers = fgetcsv($csvFile);
            if ($headers === false) {
                throw new Exception('Cannot read the headers of the CSV file');
            }

            // Check if the CSV file has duplicate headers
            if (count($headers) !== count(array_unique($headers))) {
                throw new Exception('The CSV file has duplicate headers');
            }
            
            // Require the column mapping
            $columnMapping = require_once '../utils/column_mapping.php';

            // Normalize the headers to match the column names in the database
            $headers = array_map(function($header) use ($columnMapping) {
                return $columnMapping[$header];
            }, $headers);

            // Check if all the headers are mapped to a column name
            if (in_array(null, $headers, true)) {
                throw new Exception('Some headers are not mapped to a column name');
            }

            // Convert the headers to a comma-separated string and prepare placeholders for the prepared statement
            $columnNames = implode(', ', $headers);
            // Create a string with the same number of placeholders as the number of columns
            $placeholders = implode(', ', array_fill(0, count($headers), '?'));

            // Prepare the SQL statement for insertion
            $sql = "INSERT INTO properties ($columnNames) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);

            // Process each row in the CSV file
            while (($row = fgetcsv($csvFile)) !== false) {
                // Convert boolean values from "false"/"true" to 0/1
                $normalizedRow = array_map(function($value) {
                    if ($value === 'false') {
                        return 0;
                    } elseif ($value === 'true') {
                        return 1;
                    } else {
                        return $value; // Keep other values as they are
                    }
                }, $row);

                // Execute the prepared statement with the normalized row data
                $stmt->execute($normalizedRow);
            }

            echo "Data imported successfully.";

        }
        catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function filterProperties($minPrice, $maxPrice, $bedrooms) {
        $query = "SELECT * FROM properties WHERE price BETWEEN :minPrice AND :maxPrice AND bedrooms = :bedrooms";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':minPrice', $minPrice);
        $stmt->bindParam(':maxPrice', $maxPrice);
        $stmt->bindParam(':bedrooms', $bedrooms);
        $stmt->execute();

        // Fetch all the results into an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $results;
    }


    function calculateAveragePrice($latitude, $longitude, $distance) {
        $query = "SELECT AVG(price_per_sqm) AS average_price FROM properties WHERE ST_Distance_Sphere(point(longitude, latitude), point(:longitude, :latitude)) <= :distance";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':distance', $distance);
        $stmt->execute();
    
        // Fetch the result into an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result['average_price'];
      
    }

    function filterToReport($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance) {	
        $query = 'SELECT * FROM properties WHERE price BETWEEN :minPrice AND :maxPrice AND bedrooms = :bedrooms AND ST_Distance_Sphere(point(longitude, latitude), point(:longitude, :latitude)) <= :distance';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':minPrice', $minPrice);
        $stmt->bindParam(':maxPrice', $maxPrice);
        $stmt->bindParam(':bedrooms', $bedrooms);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':distance', $distance);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
}