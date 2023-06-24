<?php
if (isset($_POST['div_campos'])) {
    require('FPDF/fpdf.php');

    $conteudo = $_POST['div_campos'];

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, $conteudo);

    $pdf->Output('nome_arquivo.pdf', 'D');
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
    <button type="button" id="btn-print" class="btn btn-default"><i class="fas fa-download"></i> Baixar PDF</button>

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