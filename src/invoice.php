<?php
require_once 'admin_check.php';
require_once 'databases.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$payment = $conn->query("SELECT * FROM payments WHERE id = $id")->fetch_assoc();

if ($payment) {
    // Try Composer path first, then fallback to manual path
    if (file_exists(__DIR__.'/../vendor/autoload.php')) {
        require_once __DIR__.'/../vendor/autoload.php';
    } else {
        require_once __DIR__.'/../tcpdf/tcpdf.php';
    }

    // Generate PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('ClubSphere');
    $pdf->SetAuthor('ClubSphere');
    $pdf->SetTitle('Invoice '.$payment['invoice_number']);
    $pdf->SetSubject('Payment Invoice');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Add a page
    $pdf->AddPage();
    
    // Generate HTML content
    $html = '<h1>Invoice #'.$payment['invoice_number'].'</h1>';
    $html .= '<p>Date: '.date('F j, Y', strtotime($payment['payment_date'])).'</p>';
    // Add more invoice details as needed
    
    // Write HTML content
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Output PDF
    $pdf->Output('invoice_'.$payment['invoice_number'].'.pdf', 'D');
} else {
    header("Location: payment_records.php");
    exit();
}