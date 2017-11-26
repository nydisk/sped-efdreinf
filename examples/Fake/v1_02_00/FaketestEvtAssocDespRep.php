<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../../../bootstrap.php';

use NFePHP\EFDReinf\Event;
use NFePHP\Common\Certificate;
use JsonSchema\Validator;

$config = [
    'tpAmb' => 3, //tipo de ambiente 1 - Produção; 2 - Produção restrita - dados reais;3 - Produção restrita - dados fictícios.
    'verProc' => '0_1_2', //Versão do processo de emissão do evento. Informar a versão do aplicativo emissor do evento.
    'eventoVersion' => '1_02_00', //versão do layout do evento
    'serviceVersion' => '1_02_00',//versão do webservice
    'empregador' => [
        'tpInsc' => 1,  //1-CNPJ, 2-CPF
        'nrInsc' => '99999999', //numero do documento
        'nmRazao' => 'Razao Social'
    ],    
    'transmissor' => [
        'tpInsc' => 1,  //1-CNPJ, 2-CPF
        'nrInsc' => '99999999999999' //numero do documento
    ]
];
$configJson = json_encode($config, JSON_PRETTY_PRINT);

$std = new \stdClass();
$std->sequencial = 1;
$std->indretif = 1;
$std->nrrecibo = '737373737373737';
$std->perapur = '2017-11';
$std->tpinscestab = 1;
$std->nrinscestab = '12345678901234';

$std->recursosrec[0] = new \stdClass();
$std->recursosrec[0]->cnpjassocdesp = '12345678901234';
$std->recursosrec[0]->vlrtotalrec = 1000;
$std->recursosrec[0]->vlrtotalret = 100;
$std->recursosrec[0]->vlrtotalnret = 10;

$std->recursosrec[0]->inforecurso[0] = new \stdClass();
$std->recursosrec[0]->inforecurso[0]->tprepasse = 3;
$std->recursosrec[0]->inforecurso[0]->descrecurso = 'sei la';
$std->recursosrec[0]->inforecurso[0]->vlrbruto = 5000;
$std->recursosrec[0]->inforecurso[0]->vlrretapur = 500;

$std->recursosrec[0]->infoproc[0] = new \stdClass();
$std->recursosrec[0]->infoproc[0]->tpproc = 1;
$std->recursosrec[0]->infoproc[0]->nrproc = 'ABC21';
$std->recursosrec[0]->infoproc[0]->codsusp = '12345678901234';
$std->recursosrec[0]->infoproc[0]->vlrnret = 1000.66;

try {
    
   //carrega a classe responsavel por lidar com os certificados
    $content     = file_get_contents('expired_certificate.pfx');
    $password    = 'associacao';
    $certificate = Certificate::readPfx($content, $password);
    
    //cria o evento e retorna o XML assinado
    $xml = Event::evtAssocDespRep(
        $configJson,
        $std,
        $certificate,
        '2017-08-03 10:37:00'
    )->toXml();
    
    //$xml = Evento::r2040($json, $std, $certificate)->toXML();
    //$json = Event::evtAssocDespRep($configjson, $std, $certificate)->toJson();
    
    header('Content-type: text/xml; charset=UTF-8');
    echo $xml;
    
} catch (\Exception $e) {
    echo $e->getMessage();
}
