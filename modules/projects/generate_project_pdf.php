<?php
// Include the Composer autoloader to load Dompdf
require_once '../../vendor/autoload.php';

// Reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Check for a valid Project ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Project ID.");
}
$project_id = $_GET['id'];

// To generate the HTML for our PDF, we will cleverly include the 
// full print_project.php file and capture its output into a variable.
ob_start();
include_once 'print_project.php'; // Using include_once to be safe
$html = ob_get_clean();

// Configure Dompdf with some options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Load the HTML content
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// We can get the project name from the $project variable that was created
// by the print_project.php script we included.
$project_name_for_filename = isset($project['project_name']) ? str_replace(' ', '_', $project['project_name']) : 'Project_' . $project_id;

// Stream the generated PDF to the browser for download
$dompdf->stream($project_name_for_filename . ".pdf", ["Attachment" => 1]);
?>