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
    <style>
        /* Consulta Margem de Consignação
----------------------------------------------------------------------------------------------------*/
        /* Estilo geral */
        .cards {
            border-radius: 8px;
        }

        .cards .vr-texto {
            padding: 4px;
            font-weight: 700;
            font-size: 13px;
        }

        .cards .vr-fundo {
            background-color: #dfdddd;
            border-radius: 10px;
        }

        /* Feedback positivo */
        .vr-ok {
            padding: 4px;
            color: #049413;
            font-weight: 700;
        }

        .cards .vr-positivo {
            color: #049413;
            font-weight: 700;
            font-size: 20px;
        }

        /* Feedback negativo */
        .vr-negat {
            padding: 4px;
            color: #FF0303;
            font-weight: 700;
        }

        /* Margem utilizada */
        .margem-utilizada {
            font-weight: 700;
        }

        /* Estilo de lista */
        .cards .ul-black {
            line-height: 7;
            padding: 0;
            margin: 0;
        }

        .cards .ul-black-1 {
            line-height: 0;
            padding: 0;
            font-weight: 700;
            margin-bottom: 4rem;
        }

        .ul-margem {
            padding: 0;
        }

        /* Saldo de margem */
        .cards .saldo-margem {
            font-weight: 700;
            line-height: 3;
            text-transform: uppercase;
        }

        /* Layout de tabela */
        .td-left {
            float: left;
        }

        .td-right {
            float: right;
        }

        .three {
            background: #E0EFFC;
            color: #1384AD;
        }

        .td-body,
        .td-head {
            border: solid 1px transparent;
            border-style: solid none;
        }

        .td-head {
            background-color: #71B5CE;
            color: white;
        }

        .td-body:first-child,
        .td-head:first-child,
        .titulo-td:first-child {
            border-left-style: solid;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .td-body:last-child,
        .td-head:last-child,
        .titulo-td:last-child {
            border-right-style: solid;
            border-bottom-right-radius: 10px;
            border-top-right-radius: 10px;
        }

        .td1 {
            background: #ECF6FE;
        }

        .td2 {
            background: #E0EFFC;
        }

        /* Estilo final */
        .div-final {
            background-color: #dfdddd;
            border-radius: 10px;
        }

        /* Componentes de painel */
        .panel-primary,
        .panel-group {
            width: 100%;
        }

        .panel-primary>.panel-heading {
            background-color: #1384AD;
        }

        .panel-body {
            width: 100%;
        }

        /* Alinhamento de texto */
        .texto-esquerda {
            display: flex;
            justify-content: space-between;
            text-align: left;
        }

        .texto-direita {
            text-align: right;
        }

        /* Aviso de margem */
        .aviso-margem {
            background-color: #1384AD;
            font-size: 13px;
            padding: 9px 7px 1px 7px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Informações adicionais */
        .table-responsive .info {
            color: #1384AD;
            font-weight: 400;
            border-radius: 8px;
        }

        /* Título da tabela */
        .titulo-td {
            color: white;
            font-weight: 700;
            background: #1384AD;
            border-radius: 10px;
            padding: 15px;
            margin: 0;
        }

        .sub-aviso {
            font-size: 13px;
            font-weight: 700;

        }

        .mensagem {
            padding: 15px;
            border-radius: 4px;
            background-color: #a2dbf0;
        }

        .navbar-fixed-top {
            position: sticky;
            top: 0;
            z-index: 1000000;
        }

        .fixed-dados {
            top: 145px;
            background-color: #fdfdfd;
        }

        @media (max-width: 767px) {
            .navbar-fixed-top {
                position: relative;
            }

            .fixed-dados {
                top: 0;
            }
        }
    </style>
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
</body>

</html>