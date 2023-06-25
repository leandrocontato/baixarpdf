<?php
header('Content-Type: text/html; charset=UTF-8');

if (isset($_POST['div_campos'])) {
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
        public $alturaBordaRodape = 13;
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
            $this->Cell(0, 10, date('d/m/Y H:i:s'), 0, 0, 'L');

            $this->SetX(($this->margemEsquerda + $this->margemDireita) / 2);
            $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $this->footerText), 0, 0, 'C');

            $this->SetX($this->margemDireita);
            $this->Cell(0, 0, iconv('UTF-8', 'ISO-8859-1', 'Página') . ' ' . $this->PageNo() . ' de ' . $this->AliasNbPages(), 0, 0, 'C');

            $this->Cell(0, 10, 'http://www.infoconsig.com.br', 0, 0, 'R');
        }

        function HTMLContent($html)
        {
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">' . $html);

            // Definir o conjunto de caracteres ao carregar o HTML
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
                        $this->SetFont('Arial', $tag === 'b' ? 'B' : ($tag === 'i' ? 'I' : 'U'));
                        $this->processChildren($node);
                        $this->SetFont('Arial', '');
                        break;
                    case 'table':


                        $this->isTable = true;
                        $this->HTMLTable($node->C14N());
                        $this->isTable = false;
                        break;
                    case 'ul':
                        $this->Ln($this->alturaCell);
                        $this->processChildren($node);
                        $this->Ln($this->alturaCell);
                        break;
                    case 'li':
                        $this->SetFont('Arial', '');
                        $this->Ln($this->alturaCell);
                        $bullet = chr(149); // Caractere de bullet (•)
                        $this->Cell(10, $this->alturaCell, $bullet, 0, 0, 'C');
                        $this->processChildren($node);
                        $this->Ln($this->alturaCell);
                        break;
                    case 'h1':
                        $this->SetFont('Arial', 'B', 14);
                        $this->processChildren($node);
                        $this->SetFont('Arial', '');
                        break;
                    default:
                        $this->processChildren($node);
                        break;
                }
            }
        }

        function HTMLTable($html)
        {
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">' . $html);

            // Definir o conjunto de caracteres ao carregar o HTML
            $dom->encoding = 'UTF-8';

            $tables = $dom->getElementsByTagName('table');

            foreach ($tables as $table) {
                $rows = $table->getElementsByTagName('tr');
                $this->Ln();
                foreach ($rows as $row) {
                    $cells = $row->getElementsByTagName('td');
                    foreach ($cells as $cell) {
                        $content = htmlspecialchars_decode($cell->nodeValue, ENT_QUOTES);
                        $this->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', $content), 1, 0, 'C');
                    }
                    $this->Ln();
                }
            }
        }

        function processChildren($node)
        {
            foreach ($node->childNodes as $childNode) {
                $this->processNode($childNode);
            }
        }
    }

    // Exemplo de uso:
    $pdf = new CustomPDF();
    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();

    // HTML com os campos preenchidos
    $html = $_POST['div_campos'];

    // Adicione qualquer personalização adicional necessária
    $pdf->headerText = 'Cabeçalho';
    $pdf->footerText = 'Rodapé';

    // Centralizar conteúdo
    $pdf->SetAutoPageBreak(true, $pdf->alturaBordaRodape);
    $pdf->SetTopMargin($pdf->margemTopo);
    $pdf->SetLeftMargin($pdf->margemEsquerda);
    $pdf->SetRightMargin($pdf->margemDireita);

    // Processar e gerar o conteúdo HTML no PDF
    $pdf->HTMLContent($html);

    // Gerar saída do PDF
    $pdf->Output();
}
?>

<script>
    document.getElementById('btn-print').addEventListener('click', function() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.responseType = 'blob';
        xhr.onload = function() {
            if (xhr.status === 200) {
                var blob = new Blob([xhr.response], {
                    type: 'application/pdf'
                });
                var fileName = 'Consulta de Margem de Consignação ' + new Date().toISOString().split('T')[0] + '.pdf';
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = fileName;
                link.click();
            }
        };
        xhr.send('div_campos=' + encodeURIComponent(document.getElementById('div_campos').innerHTML));
    });
</script>