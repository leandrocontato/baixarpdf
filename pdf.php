<?php
header('Content-Type: text/html; charset=UTF-8');

if (isset($_POST['div_campos'])) {
    require('FPDF\fpdf.php');

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

        function processTable($node)
        {
            $rows = $node->getElementsByTagName('tr');
            foreach ($rows as $row) {
                $cells = $row->getElementsByTagName('td');
                foreach ($cells as $cell) {
                    $this->processNode($cell);
                    $this->Cell(40, 10, '', 1); // Adicione células vazias para delimitar as colunas
                }
                $this->Ln();
            }
        }

        function processChildren($node)
        {
            foreach ($node->childNodes as $childNode) {
                $this->processNode($childNode);
            }
        }



        // Função para processar a lista HTML
        function processList($list)
        {
            $items = $list->getElementsByTagName('li');
            foreach ($items as $item) {
                $this->Ln(5);
                $this->SetFont('Arial', '', 10);
                $bullet = chr(149); // Caractere de bullet (•)
                $this->Cell(10, $this->alturaCell, $bullet, 0, 0, 'C');
                $this->processNode($item);
            }
        }
    }

    // Exemplo de uso:
    $pdf = new CustomPDF();
    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();

    // // HTML com os campos preenchidos
    $div_campos = $_POST['div_campos'];
    $conteudo_sem_tags = strip_tags($div_campos);
    $html = htmlspecialchars($conteudo_sem_tags, ENT_QUOTES, 'UTF-8');

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
                                <div class="col-md-4 cards">
                                    <div class="panel panel-primary">
                                        <div class="panel-body">
                                            <p class="text-center">Margem de:</p>
                                            <div class="text-center vr-fundo">
                                                <p class="text-center  vr-positivo">5%</p>
                                            </div>
                                            <p class="text-center vr-positivo">R$ 450,00</p>
                                            <p class="text-center vr-texto">Valor da margem de:</p>
                                            <hr>
                                            <p class="text-center margem-utilizada">Margem utilizada</p>
                                            <ul class="ul-margem">
                                                <li class="texto-esquerda">Cartão de Crédito: <span class="texto-direita">450,00</span></li>
                                            </ul>
                                            <hr>
                                            <ul class="ul-black-1">
                                                <li class="texto-esquerda">Total Utilizado<span class="texto-direita">450,00</span></li>
                                            </ul>
                                            <p class="text-center saldo-margem">Saldo da Margem</p>
                                            <div class="text-center div-final">
                                                <p class="vr-texto">0,00</p>
                                            </div>
                                            <div class="text-center"><!-- add essa class quando houver mensagem: aviso-margem -->
                                                <p style="color: white;">space</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 cards">
                                    <div class="panel panel-primary">
                                        <div class="panel-body">
                                            <p class="text-center">Margem de:</p>
                                            <div class="text-center vr-fundo">
                                                <p class="text-center  vr-positivo">10%</p>
                                            </div>
                                            <p class="text-center vr-positivo">R$ 900,00</p>
                                            <p class="text-center vr-texto">Valor da margem de:</p>
                                            <hr>
                                            <p class="text-center margem-utilizada">Margem utilizada</p>
                                            <ul class="ul-margem">
                                                <li class="texto-esquerda">Cartão Beneficio: <span class="texto-direita">450,00</span></li>
                                            </ul>
                                            <hr>
                                            <ul class="ul-black-1">
                                                <li class="texto-esquerda">Total Utilizado<span class="texto-direita">450,00</span></li>
                                            </ul>
                                            <p class="text-center saldo-margem">Saldo da Margem</p>
                                            <div class="text-center div-final">
                                                <p class="vr-ok">300,00</p>
                                            </div>
                                            <div class="aviso-margem text-center">
                                                <p style="color: white;">Margem limitada devido excedente margem</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    <p style="font-size: 13px; font-weight: 500;"><span style="color: red;">Importante:</span> Somente quando determinado pelo regulamento da sua folha de pagamento, o limite do uso da margem pode ser comprometido em função do valor excedente utilizado da margem em outro tipo de consignação, por isso, o salda da margem consultado poderá ser menor.</p>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="button" id="btn-print" class="btn btn-default"><i class="fas fa-download"></i> Baixar PDF</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-group">
                        <div class="panel panel-primary" style="cursor: pointer;" data-toggle="collapse" href="#collapse1">
                            <div class="panel-heading">
                                <h4 class="panel-title"><i class="fa-solid fa-chevron-down" style="margin-right: 5px;"></i>Informações e Avisos Importantes</h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <h6 class="sub-aviso">ENTENDA COMO FUNCIONA O QUADRO DO RESUMO DA MARGEM</h6>
                                    <h6 class="sub-aviso">ATUALIZAÇÃO</h6>
                                    <p>Data da última atualização do valor da margem consignável.</p>
                                    <h6 class="sub-aviso">TIPO DE CONSIGNAÇÃO</h6>
                                    <p>Consiste na especificação da consignação com base na característica do contrato/operação realizada.</p>
                                    <h6 class="sub-aviso">MARGEM</h6>
                                    <p>Valor do limite da margem consignável autorizado pelo regulamento. O valor poderá aparecer repetido em razão da existência de uma única margem para vários tipos de consignações.</p>
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
                    <div class="panel-group">
                        <div class="panel panel-primary" style="cursor: pointer;" data-toggle="collapse" href="#collapse3">
                            <div class="panel-heading">
                                <h4 class="panel-title"><i class="fa-solid fa-chevron-down" style="margin-right: 5px;"></i>Resumo das Consignações</h4>
                            </div>
                            <div id="collapse3" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table" id="table-margem">
                                            <thead>
                                                <caption class="titulo-td">Consignações da Margem de 35%</caption>
                                                <tr class="tr">
                                                    <td class="td-head">Solicitação</td>
                                                    <td class="td-head">Data</td>
                                                    <td class="td-head">Consignação</td>
                                                    <td class="td-head">Consignatária</td>
                                                    <td class="td-head">Margem Reservada</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="active info">
                                                    <td class="td-body cursor">104530</td>
                                                    <td class="td-body">09/05/2023</td>
                                                    <td class="td-body">Empréstimo</td>
                                                    <td class="td-body">Bradesco</td>
                                                    <td class="td-body">1.000,00</td>
                                                </tr>
                                                <tr class="dif">
                                                    <td class="td-body cursor">104526</td>
                                                    <td class="td-body">09/05/2023</td>
                                                    <td class="td-body">Mensalidade</td>
                                                    <td class="td-body">Unimed</td>
                                                    <td class="td-body">1.000,00</td>
                                                </tr>
                                                <tr class="three">
                                                    <td class="td-body cursor">104526</td>
                                                    <td class="td-body">09/05/2023</td>
                                                    <td class="td-body">Convênios Diversos</td>
                                                    <td class="td-body">Daycoval</td>
                                                    <td class="td-body">1.000,00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table" id="table-margem">
                                            <thead>
                                                <caption class="titulo-td">Consignações da Margem de 5%</caption>
                                                <tr class="tr">
                                                    <td class="td-head">Solicitação</td>
                                                    <td class="td-head">Data</td>
                                                    <td class="td-head">Consignação</td>
                                                    <td class="td-head">Consignatária</td>
                                                    <td class="td-head">Margem Reservada</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="active info">
                                                    <td class="td-body cursor">104330</td>
                                                    <td class="td-body">09/05/2023</td>
                                                    <td class="td-body">Cartão de Crédito</td>
                                                    <td class="td-body">Banco Master</td>
                                                    <td class="td-body">1.000,00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table" id="table-margem">
                                            <caption class="titulo-td">Consignações da Margem de 10%</caption>
                                            <tr class="tr">
                                                <td class="td-head">Solicitação</td>
                                                <td class="td-head">Data</td>
                                                <td class="td-head">Consignação</td>
                                                <td class="td-head">Consignatária</td>
                                                <td class="td-head">Margem Reservada</td>
                                            </tr>
                                            <tbody>
                                                <tr class="active info">
                                                    <td class="td-body">102530</td>
                                                    <td class="td-body">09/05/2023</td>
                                                    <td class="td-body">Cartão Beneficio</td>
                                                    <td class="td-body">Credcesta</td>
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
    </div>
</div><!-- fim div_campos -->
<!-- <button type="button" id="btn-print" class="btn btn-default"><i class="fas fa-download"></i> Baixar PDF</button> -->
<div class="clearfix"></div>
<!-- <script src="View\public\script\jquery-1.7.2.min.js"></script> -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="jquery-3.7.0.js"></script>
<script src="bootstrap.min.js"></script>
<link rel="stylesheet" href="css/layout.css">
<script src="https://kit.fontawesome.com/b7a90d0458.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // mensagem
        $('#alert .close').click(() => $('#alert').hide());
        $('#btn-print').click(() => $('.mensagem').show()); // pdf
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
    });
</script>