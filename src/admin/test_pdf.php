<?php
// Simple test to verify TCPDF is working
require_once '../libs/TCPDF-main/tcpdf.php';

try {
    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Insidentia System Test');
    $pdf->SetTitle('Test PDF Generation');
    $pdf->SetMargins(20, 20, 20);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(255, 20, 147); // Pink color
    
    // Write content
    $pdf->Cell(0, 15, 'Test PDF Generation', 0, 1, 'C');
    $pdf->Ln(5);
    
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(0, 0, 0); // Black color
    $pdf->Cell(0, 10, 'TCPDF is working properly!', 0, 1, 'C');
    $pdf->Ln(10);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 5, 'This is a simple test to verify that TCPDF library is correctly installed and configured in your system.', 0, 'L');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Generated on: ' . date('d F Y, H:i:s') . ' WIB', 0, 1, 'L');
    
    // Output the PDF
    $pdf->Output('test_pdf_' . date('Y-m-d_H-i-s') . '.pdf', 'I');
    
} catch (Exception $e) {
    echo "Error generating PDF: " . $e->getMessage();
}
?>
