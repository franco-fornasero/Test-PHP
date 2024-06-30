<?php

// Require the database connection
require_once '../config/database.php';

// Create the properties table
$sql = "
    CREATE TABLE IF NOT EXISTS properties (
        latitude DECIMAL(10, 8) NOT NULL,
        longitude DECIMAL(11, 8) NOT NULL,
        id INT(8) PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        advertiser VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        refurbished INT(1),
        phones VARCHAR(15),
        type VARCHAR(10),
        price DECIMAL(10, 2) NOT NULL,
        price_per_sqm DECIMAL(10, 2) NOT NULL,
        address VARCHAR(255) NOT NULL,
        province VARCHAR(255),
        city VARCHAR(255),
        square_meters DECIMAL(10, 2) NOT NULL,
        bedrooms INT NOT NULL,  
        bathrooms INT NOT NULL,
        parking INT(1),
        second_hand INT(1),
        built_in_wardrobes INT(1),
        built_in INT,
        furnished INT(1),
        individual_heating INT(1),
        energy_certification VARCHAR(50),
        floor INT,
        exterior INT(1),
        interior INT(1),
        elevator INT(1),
        date DATE,
        street VARCHAR(255),
        neighborhood VARCHAR(255),
        district VARCHAR(255),
        terrace INT(1),
        storage_room INT(1),
        equipped_kitchen INT(1),
        air_conditioning INT(1),
        swimming_pool INT(1),
        garden INT(1),
        usable_square_meters DECIMAL(10, 2),
        suitable_for_reduced_mobility INT(1),
        floors INT,
        pets_allowed INT(1),
        balcony INT(1),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

try {
    // Execute the SQL statement
    $pdo->exec($sql);
    echo "Table properties created successfully.";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
