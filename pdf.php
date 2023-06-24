<?php
if (isset($_POST['div_campos'])) {
    require('FPDF/fpdf.php');

    class CustomPDF extends FPDF
    {
        private $headerText = 'Cabeçalho';
        private $footerText = 'Rodapé';
        private $margemEsquerda = 7;
        private $margemDireita = 10;
        private $margemTopo = 10;
        private $alturaCell = 4;
        private $linhaCabecalho_01 = 0;
        private $alturaBordaRodape = 13;

        function Header()
        {
            // $this->Image('logo_infoconsig.png', $this->margemEsquerda, $this->margemTopo, 30);

            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode($this->headerText), 0, 1, 'C');
        }

        function Footer()
        {
            $this->SetY(-$this->alturaBordaRodape);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode($this->footerText), 0, 0, 'C');
            $this->Cell(0, 10, utf8_decode(date('d/m/Y H:i:s') . ' Página ' . $this->PageNo() . ' de ' . $this->AliasNbPages()), 0, 0, 'L');
            $this->Cell(0, 10, utf8_decode('http://www.infoconsig.com.br'), 0, 0, 'R');
        }

        function HTMLTable($html)
        {
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $tables = $dom->getElementsByTagName('table');

            foreach ($tables as $table) {
                $rows = $table->getElementsByTagName('tr');
                $this->Ln();
                foreach ($rows as $row) {
                    $cells = $row->getElementsByTagName('td');
                    foreach ($cells as $cell) {
                        $content = $cell->nodeValue;
                        $this->Cell(40, 10, utf8_decode($content), 1);
                    }
                    $this->Ln();
                }
            }
        }
    }

    $conteudo = $_POST['div_campos'];

    $orientation = 'L';
    $formatPage = 'A4';
    $margemEsquerda = 7;
    $margemDireita = 10;
    $margemTopo = 10;
    $alturaCell = 4;
    $linhaCabecalho_01 = 0;
    $alturaBordaRodape = 13;

    $pdf = new CustomPDF($orientation, 'mm', $formatPage);
    $pdf->AliasNbPages();
    $pdf->SetMargins($margemEsquerda, $margemTopo, $margemDireita);
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(10, utf8_decode($conteudo));

    if (strpos($conteudo, '<table') !== false) {
        $pdf->HTMLTable($conteudo);
    }

    ob_clean();
    $nomeArquivo = 'Consulta-de-Margem-de-Consignacao' . date('Ymd') . '.pdf';
    $pdf->Output($nomeArquivo, 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div id="div_campos" class="col-md-12">oi eu sou o goku</div>
    <button href="#" type="button" id="btn-print" class="btn btn-default"><i class="fas fa-download"></i> Baixar PDF</button>

    <script>
        document.getElementById('btn-print').addEventListener('click', function() {
            var div_campos = document.getElementById('div_campos').innerHTML;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.responseType = 'blob';
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var blob = new Blob([xhr.response], {
                        type: 'application/pdf'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'nome_arquivo.pdf';
                    link.click();
                }
            };
            var formData = new FormData();
            formData.append('div_campos', div_campos);
            xhr.send(new URLSearchParams(formData).toString());
        });
    </script>
</body>

</html>