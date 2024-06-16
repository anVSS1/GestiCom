<?php
require_once('tcpdf/tcpdf.php');
require_once('vendor/autoload.php');

use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Generate barcode
    $barcodeGenerator = new BarcodeGeneratorPNG();
    $barcode = $barcodeGenerator->getBarcode($productId, $barcodeGenerator::TYPE_CODE_128);

    // Output the barcode image
    header('Content-Type: image/png');
    echo $barcode;
}
