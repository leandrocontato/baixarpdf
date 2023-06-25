<?php
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

        function HTMLContent($html)
        {
            $dom = new DOMDocument();
            $dom->loadHTML($html);

            $body = $dom->getElementsByTagName('body')->item(0);
            $this->processNode($body);
        }

        function processNode($node)
        {
            if ($node->nodeType === XML_TEXT_NODE) {
                $this->Write($this->alturaCell, utf8_decode($node->nodeValue));
            } elseif ($node->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($node->nodeName);

                switch ($tag) {
                    case 'br':
                        $this->Ln($this->alturaCell);
                        break;
                    case 'b':
                        $this->SetFont('Arial', 'B');
                        $this->processChildren($node);
                        $this->SetFont('Arial', '');
                        break;
                    case 'i':
                        $this->SetFont('Arial', 'I');
                        $this->processChildren($node);
                        $this->SetFont('Arial', '');
                        break;
                    case 'u':
                        $this->SetFont('Arial', 'U');
                        $this->processChildren($node);
                        $this->SetFont('Arial', '');
                        break;
                    case 'table':
                        $this->isTable = true;
                        $this->HTMLTable($node->C14N());
                        $this->isTable = false;
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

        function processChildren($node)
        {
            foreach ($node->childNodes as $childNode) {
                $this->processNode($childNode);
            }
        }
    }

    $conteudo = $_POST['div_campos'];

    $pdf = new CustomPDF('L', 'mm', 'A4');
    $pdf->headerText = 'Cabeçalho';
    $pdf->footerText = 'Rodapé';
    $pdf->margemEsquerda = 7;
    $pdf->margemDireita = 10;
    $pdf->margemTopo = 10;
    $pdf->alturaCell = 4;
    $pdf->linhaCabecalho_01 = 0;
    $pdf->alturaBordaRodape = 13;

    $pdf->AliasNbPages();
    $pdf->SetMargins($pdf->margemEsquerda, $pdf->margemTopo, $pdf->margemDireita);
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $pdf->HTMLContent($conteudo);

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
    <div id="div_campos" class="col-md-12">
        <div class="container col-xs-10 col-sm-8 col-md-10 col-lg-10 col-xs-offset-1 col-sm-offset-2 col-md-offset-1 col-lg-offset-1">
            <div id="alert" class="mensagem" role="alert">
                <button class="close" type="button"><i class="fa-solid fa-circle-xmark"></i></button>
                <strong>Congratulations!</strong> You successfully tied your shoelace!
            </div>
            <div style="text-align:left;">
                <h2 class="title"><i class="fa-solid fa-filter-circle-dollar" style="color:#1384AD;"></i> Consulta Margem de Consignação</h2>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="col-md-6">
                        <div class="panel-primary">
                            <div class="panel-heading">
                                <h4 class="panel-title"> Resumo da Margem: <span>última atualização: 01/05/2023</span></h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4 cards">
                                        <div class="panel panel-primary">
                                            <div class="panel-body">
                                                <p class="text-center">Margem de:</p>
                                                <div class="text-center vr-fundo">
                                                    <p class="text-center vr-positivo">35%</p>
                                                </div>
                                                <p class="text-center vr-positivo">R$ 3.200,00</p>
                                                <p class="text-center vr-texto">Valor da margem de:</p>
                                                <hr>
                                                <p class="text-center margem-utilizada">Margem utilizada</p>
                                                <ul class="ul-margem">
                                                    <li class="texto-esquerda">Empréstimos: <span class="texto-direita">3.000,00</span></li>
                                                    <li class="texto-esquerda">Mensalidades: <span class="texto-direita">100,00</span></li>
                                                    <li class="texto-esquerda">Convênios: <span class="texto-direita">250,00</span></li>
                                                </ul>
                                                <hr>
                                                <ul class="ul-black-1">
                                                    <li class="texto-esquerda">Total Utilizado<span class="texto-direita">3.350,00</span></li>
                                                </ul>
                                                <p class="text-center" style="font-weight: 700; text-transform: uppercase;">Saldo da Margem</p>
                                                <div class="text-center div-final">
                                                    <p class="vr-negat">-150,00</p>
                                                </div>
                                                <div class="aviso-margem text-center">
                                                    <p style="color: white;">Margem Excedente</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <p style="font-size: 13px; font-weight: 500;"><span style="color: red;">Importante:</span> Somente quando determinado pelo regulamento da sua folha de pagamento, o limite do uso da margem pode ser comprometido em função do valor excedente utilizado da margem em outro tipo de consignação, por isso, o salda da margem consultado poderá ser menor.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel-group">
                            <div class="panel panel-primary" style="cursor: pointer;" data-toggle="collapse" href="#collapse2">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa-solid fa-chevron-down" style="margin-right: 5px;"></i>Histórico das Margens</h4>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <table class="table" id="table-margem">
                                            <thead>
                                                <caption class="titulo-td">Resumo do Histórico</caption>
                                                <tr class="tr text-center">
                                                    <td class="td-head">Data</td>
                                                    <td class="td-head">Valor da Margem 35%</td>
                                                    <td class="td-head">Valor da Margem 5%</td>
                                                    <td class="td-head">Valor da Margem 10%</td>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                <tr class="active info">
                                                    <td class="td-body">10/01/2023</td>
                                                    <td class="td-body">1.000,00</td>
                                                    <td class="td-body">1.000,00</td>
                                                    <td class="td-body">1.000,00</td>
                                                </tr>
                                                <tr class="dif">
                                                    <td class="td-body">10/01/2023</td>
                                                    <td class="td-body">1.000,00</td>
                                                    <td class="td-body">1.000,00</td>
                                                    <td class="td-body">1.000,00</td>
                                                </tr>
                                                <tr class="three">
                                                    <td class="td-body">10/01/2023</td>
                                                    <td class="td-body">1.000,00</td>
                                                    <td class="td-body">1.000,00</td>
                                                    <td class="td-body">1.000,00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- fim div_campos -->
    <!-- <div id="div_campos" class="col-md-12">oi eu sou o goku</div> -->
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