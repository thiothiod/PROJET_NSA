<?php
// Inclure l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php'; // Assurez-vous que le chemin est correct

// Créer un objet FPDF
$pdf = new \FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Bonjour le monde!');
$pdf->Output(); // Afficher le PDF dans le navigateur
?>
