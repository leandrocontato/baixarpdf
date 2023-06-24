<?php
if (isset($_POST['div_campos'])) {
    require('FPDF/fpdf.php');

    class CustomPDF extends FPDF
    {
        // Variáveis para cabeçalho e rodapé
        private $headerText = 'Cabeçalho';
        private $footerText = 'Rodapé';

        function Header()
        {
            // Cabeçalho
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode($this->headerText), 0, 1, 'C');
        }

        function Footer()
        {
            // Rodapé
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode($this->footerText), 0, 0, 'C');
        }

        function HTMLTable($html)
        {
            // Função para desenhar uma tabela HTML
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

    $pdf = new CustomPDF('P', 'mm', 'A4'); // Define o tamanho do papel como A4
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(10, utf8_decode($conteudo));

    // Verifica se há tabelas dentro da div
    if (strpos($conteudo, '<table') !== false) {
        $pdf->HTMLTable($conteudo);
    }

    $data = date('Ymd'); // Obtém a data atual no formato YYYYMMDD
    $nome_arquivo = 'consulta-de-margem-de-consignacao-' . $data . '.pdf'; // Personaliza o nome do arquivo com a data

    $pdf->Output($nome_arquivo, 'D');
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