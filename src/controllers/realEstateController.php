<?php

require_once '../src/classes/realEstateManager.php';

class RealEstateController
{
    private $realEstateManager;

    public function __construct(PDO $pdo)
    {
        $this->realEstateManager = new realEstateManager($pdo);
    }

    public function importDataFromCSV($csvFilePath)
    {
        try {
            $this->realEstateManager->importDataFromCSV($csvFilePath);
            return json_encode(['message' => 'Data imported successfully']);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public function filterProperties($minPrice, $maxPrice, $bedrooms)
    {
        try {
            $properties = $this->realEstateManager->filterProperties($minPrice, $maxPrice, $bedrooms);
            return json_encode($properties);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public function calculateAveragePrice($latitude, $longitude, $distance) {
        try {
            $averagePrice = $this->realEstateManager->calculateAveragePrice($latitude, $longitude, $distance);
            return json_encode(['average_price' => $averagePrice]);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public function generateReport($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance, $format) {
        switch ($format) {
            case 'pdf':
                return $this->generateReportPDF($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance);
            case 'csv':
                return $this->generateReportCSV($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance);
            default:
                return json_encode(['error' => 'Formato no soportado']);
        }
    } 

    private function generateReportPDF($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance) {

        require_once 'C:/xampp/htdocs/Test-PHP/lib/fpdf/fpdf.php'; // Include the FPDF library

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Reporte de Propiedades', 0, 1, 'C');
        $pdf->Ln();

        $properties = $this->realEstateManager->filterToReport($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance);

        $pdf->Line(10, 20, 200, 20);
        foreach ($properties as $property) {
            
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 5, 'Titulo: ' . mb_convert_encoding($property['title'], 'ISO-8859-1', 'UTF-8'), 0,1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 5, 'ID: ' . mb_convert_encoding($property['id'], 'ISO-8859-1', 'UTF-8'), 0, 1);
            $pdf->Cell(0, 5, 'Precio: ' . $property['price'], 0, 1);
            $pdf->Cell(0, 5, 'Metros cuadrados: ' . $property['square_meters'], 0, 1);
            $pdf->Ln();

            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw a horizontal line
            $pdf->Ln();
        }

        $pdf->Output('I', 'reporte_propiedades.pdf');
    }

    private function generateReportCSV($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance) {
        $properties = $this->realEstateManager->filterToReport($minPrice, $maxPrice, $bedrooms, $latitude, $longitude, $distance);

        $csvFilePath = 'C:/Users/Public/Documents/reporte_propiedades.csv';

        $file = fopen($csvFilePath, 'w');

        // Write the header
        fputcsv($file, ['Título', 'Descripción', 'Precio', 'Metros cuadrados']);

        // Write the data
        foreach ($properties as $property) {
            fputcsv($file, [
                $property['title'],
                $property['description'],
                $property['price'],
                $property['square_meters']
            ]);
        }

        fclose($file);

        return "El archivo fue creado en: " . $csvFilePath; // Return the path to the CSV file
    }
  
}