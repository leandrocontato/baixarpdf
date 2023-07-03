<?php
header('Content-Type: text/html; charset=UTF-8');
require('FPDF/fpdf.php');

class CustomPDF extends FPDF
{
    public $headerText = 'Cabeçalho';
    public $footerText = 'Rodapé';
    public $margemEsquerda = 7;
    public $margemDireita = 10;
    public $margemTopo = 10;
    public $alturaCell = 4;
    public $linhaCabecalho_01 = 0;
    public $alturaBordaRodape = 15;
    public $isTable = false;

    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $this->headerText), 0, 1, 'C');
        $this->Cell(30, 10, 'infoconsig', 1, 0, 'C');
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-$this->alturaBordaRodape);
        $this->SetFont('Arial', 'I', 8);

        $this->SetX($this->margemEsquerda);
        date_default_timezone_set('America/Sao_Paulo');
        $this->Cell(0, 18, date('d/m/Y H:i:s'), 0, 0, 'L');

        $this->SetX(($this->margemEsquerda + $this->margemDireita) / 2);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $this->footerText), 0, 0, 'C');

        $this->SetX($this->margemDireita);
        $this->AliasNbPages();
        $this->Cell(0, 18, iconv('UTF-8', 'ISO-8859-1', 'Página') . ' ' . $this->PageNo() . ' de ' . '{nb}', 0, 0, 'C');

        $this->Cell(0, 18, 'http://www.infoconsig.com.br', 0, 0, 'R');
    }

    function HTMLContent($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $dom->encoding = 'UTF-8';
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->processNode($body);
    }

    function processNode($node)
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $this->Write($this->alturaCell, iconv('UTF-8', 'ISO-8859-1', $node->nodeValue));
        } elseif ($node->nodeType === XML_ELEMENT_NODE) {
            $tag = strtolower($node->nodeName);

            switch ($tag) {
                case 'br':
                    $this->Ln($this->alturaCell);
                    break;
                case 'b':
                case 'i':
                case 'u':
                    $this->SetFont('Arial', $tag === 'b' ? 'B' : ($tag === 'i' ? 'I' : 'U'), '', true);
                    foreach ($node->childNodes as $childNode) {
                        $this->processNode($childNode);
                    }
                    $this->SetFont('Arial', '');
                    break;
                case 'table':
                    $this->isTable = true;
                    $rows = $node->getElementsByTagName('tr');
                    $columnCount = 0;

                    // Calcular o número de colunas
                    foreach ($rows as $row) {
                        $cells = $row->getElementsByTagName('td');
                        $columnCount = max($columnCount, $cells->length);
                    }

                    // Calcular a largura disponível para a tabela
                    $availableWidth = $this->GetPageWidth() - $this->margemEsquerda - $this->margemDireita;

                    // Calcular a largura de cada coluna
                    $columnWidth = $availableWidth / $columnCount;

                    foreach ($rows as $row) {
                        $cells = $row->getElementsByTagName('td');
                        foreach ($cells as $cell) {
                            $this->processNode($cell);
                            $this->Cell($columnWidth, $this->alturaCell, '', 1);
                        }
                        $this->Ln();
                    }

                    $this->isTable = false;
                    break;
                case 'ul':
                    $this->Ln($this->alturaCell);
                    $items = $node->getElementsByTagName('li');
                    foreach ($items as $item) {
                        $this->Ln(5);
                        $this->SetFont('Arial', '', 10);
                        $bullet = chr(149); // Caractere de bullet (•)
                        $this->Cell(10, $this->alturaCell, $bullet, 0, 0, 'C');
                        $this->processNode($item);
                    }
                    $this->Ln($this->alturaCell);
                    break;
                case 'li':
                    $this->SetFont('Arial', '');
                    $this->Ln($this->alturaCell);
                    $bullet = chr(149); // Caractere de bullet (•)
                    $this->Cell(10, $this->alturaCell, $bullet, 0, 0, 'C');
                    foreach ($node->childNodes as $childNode) {
                        $this->processNode($childNode);
                    }
                    $this->Ln($this->alturaCell);
                    break;
                case 'h1':
                    $this->SetFont('Arial', 'B', 14);
                    foreach ($node->childNodes as $childNode) {
                        $this->processNode($childNode);
                    }
                    $this->SetFont('Arial', '');
                    break;
                default:
                    foreach ($node->childNodes as $childNode) {
                        $this->processNode($childNode);
                    }
                    break;
            }
        }
    }
}

if (isset($_POST['div_campos'])) {
    $pdf = new CustomPDF();
    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();
    $div_campos = $_POST['div_campos'];
    $conteudo_sem_tags = strip_tags($div_campos);
    $html = htmlspecialchars($conteudo_sem_tags, ENT_QUOTES, 'UTF-8');
    $pdf->headerText = 'Cabeçalho';
    $pdf->footerText = 'Rodapé';
    $pdf->SetAutoPageBreak(true, $pdf->alturaBordaRodape);
    $pdf->SetTopMargin($pdf->margemTopo);
    $pdf->SetLeftMargin($pdf->margemEsquerda);
    $pdf->SetRightMargin($pdf->margemDireita);
    $pdf->HTMLContent($html);
    $pdf->Output('I', 'filename.pdf');

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
