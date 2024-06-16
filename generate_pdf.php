<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "g_client";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $commandId = $_GET['id'];

        $sql = "SELECT c.id_commande, c.client_id, c.date, c.observation, c.total_prix, cl.name AS client_name
                FROM commande c
                JOIN client cl ON c.client_id = cl.id
                WHERE c.id_commande = :commandId";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':commandId', $commandId);
        $statement->execute();
        $command = $statement->fetch(PDO::FETCH_OBJ);

        if (!$command) {
            echo 'Invalid command ID.';
            exit();
        }
    } else {
        echo 'Command ID not provided.';
        exit();
    }
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
    exit();
}

require_once 'tcpdf/tcpdf.php';

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator('GestiCom');
$pdf->SetAuthor('Anass & Kaoutar');
$pdf->SetTitle('Details de commande');
$pdf->SetAutoPageBreak(true, 10);

$pdf->AddPage();

// Set the logo image
$pdf->Image('logo.png', 10, 10, 30, '', 'PNG');

$pdf->SetFont('helvetica', 'B', 16);
// Company information
$companyName = 'GestiCom';
$companyAddress = 'Drarga, Agadir, Maroc';
$companyContact = 'Phone: +1 234 567 890, Email: gesticom23@gmail.com';

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, $companyName, 0, 1, 'R');
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 10, $companyAddress . "\n" . $companyContact, 0, 'R');

$pdf->Cell(0, 20, 'PDF de commande', 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Commande ID: ' . $command->id_commande, 0, 1, 'L');
$pdf->Cell(0, 10, 'Nom de client: ' . $command->client_name, 0, 1, 'L');
$pdf->Cell(0, 10, 'Date: ' . $command->date, 0, 1, 'L');
$pdf->Cell(0, 10, 'Observation: ' . $command->observation, 0, 1, 'L');
$pdf->Cell(0, 10, 'Prix total: ' . $command->total_prix . ' DH', 0, 1, 'L');

$pdf->Ln(15);

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Command Details Table', 0, 1, 'L');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(30, 10, 'Produit ID', 1, 0, 'C', true);
$pdf->Cell(70, 10, 'Nom produit', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Quantite', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Prix Unitaire', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Total', 1, 0, 'C', true);
$pdf->Ln();

// Fetch command details
$sql = "SELECT * FROM details WHERE id_commande = :commandId";
$statement = $conn->prepare($sql);
$statement->bindValue(':commandId', $commandId);
$statement->execute();
$details = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($details) {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetFillColor(255, 255, 255);
    foreach ($details as $row) {
        $pdf->Cell(30, 10, $row['id_produit'], 1, 0, 'C', true);
        $pdf->Cell(70, 10, $row['nom_produit'], 1, 0, 'L', true);
        $pdf->Cell(30, 10, $row['quantite'], 1, 0, 'C', true);
        $pdf->Cell(40, 10, $row['prix_unitaire'] . ' DH', 1, 0, 'R', true);
        $pdf->Cell(20, 10, $row['total'] . ' DH', 1, 0, 'R', true);
        $pdf->Ln();
    }
} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(0, 10, 'Pas de details pour cette commande', 1, 1, 'L', true);
}

$pdf->Output('details.pdf', 'I');
exit();
?>
