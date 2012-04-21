<?
	require('Fpdf.php');

$pdf=new Fpdf();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,1);
$pdf->Cell(60,10,utf8_decode('Conserjería'),1,1);
$pdf->Cell(80,10,'Conserjería',1,1);
$pdf->Close();
$pdf->Output();
?>