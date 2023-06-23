<?php
if (isset($_POST['conteudo'])) {
    require('FPDF/fpdf.php');

    $conteudo = $_POST['conteudo'];

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, $conteudo);

    $pdf->Output('nome_arquivo.pdf', 'D');
    exit;
}
