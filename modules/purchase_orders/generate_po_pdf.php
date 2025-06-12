<?php
// Include the Composer autoloader to load Dompdf
require_once '../../vendor/autoload.php';

// Reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Check for a valid PO ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Purchase Order ID.");
}
$po_id = $_GET['id'];

// To generate the HTML for our PDF, we will cleverly include the 
// full print_po.php file and capture its output into a variable.
ob_start();
include_once 'print_po.php'; // Using include_once to be safe
$html = ob_get_clean();

// Configure Dompdf with some options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Allows loading of images, etc.
$dompdf = new Dompdf($options);

// Load the HTML content
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// We can get the PO number from the $po variable that was created
// by the print_po.php script we included. This is very efficient.
$po_number_for_filename = isset($po['po_number']) ? str_replace(' ', '_', $po['po_number']) : 'PO_' . $po_id;

// Stream the generated PDF to the browser for download
// The second argument "1" forces download, "0" would show a preview.
$dompdf->stream($po_number_for_filename .".pdf", ["Attachment" => 1]);
?>