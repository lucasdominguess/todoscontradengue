<?php
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
ini_set('default_charset', 'UTF-8');
$GLOBALS['TZ'] = new \DateTimeZone( 'America/Sao_Paulo');
$GLOBALS['INICIO_PROGRAMA'] = '2024-01-01';
$inico_programa = new \DateTime($GLOBALS['INICIO_PROGRAMA'],$GLOBALS['TZ']);
define('INICIO_PROGRAMA_FORMAT', $inico_programa->format('d/m/Y'));
define('APP_MODE','dev');
define('APP_ID','h_todoscontraadengue');//controla o identificador de sessão para não conflitar com outras aplicações que o usuário esteja logado
define('APP_TITLE','Enfrentamento dengue');

define('URI_PACIENTE','http://ws.siga.prodam/smsservices/services/PessoaService?wsdl');//produção (dentro rede prodam)
define('URI_PROFISSIONAL','http://ws.siga.prodam/smsservices/services/ProfissionalService?wsdl');//não testada
define('COMPRIMENTO_MINIMO_LOGRADOURO', 5);
define('COMPRIMENTO_MINIMO_QUARTEIRAO', 5);


define('SIGA_USER_NAME','GETCONNECT');
define('SIGA_USER_PASS','GETCONNECT!@#$');
define('SIGA_USER_SYSTEM','eSAUDESP');
