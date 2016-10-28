<?php

require_once('util.php');

session_start();

$responseText = "empty";

/* Korrekten HTTP Status Code reintun*/
if(!isset($_POST['assertion'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erh�lt keine Assertion';
}

//D.h. ein username wurde �bermittelt, der jetzt gecheckt wird.
else {
	//Die �bertragene Assertion wird als JSON empfangen und dann in ihre einzelnen Strings zerlegt:
	//Eigentlich m�sste hier noch immer eine Pr�fung rein, um es von dem und dem Format ist, oder dann am Ende alle Werte auf undefined / empty pr�fen?
	
	$assertion = $_POST['assertion'];
	$assertionJs = json_decode($assertion,true);
	$type = $assertionJs['credential']['type'];
	$id = $assertionJs['credential']['id'];
	$clientData = $assertionJs['clientData'];
	$signature = $assertionJs['signature'];
	
	$cData = base64_decode($clientData);
	
	
	
	$responseStatus = '200 OK';
	$responseText = "ClientData: ".$cData. "    |     Assertion mit ID:".$id." und Signatur: ".$signature;
		
	}

/* 
 * Validieren der Assertion
 */
function validateAssertion() {
	//Pr�fen, ob die Challenges matchen. Die Challenge ist in den ClientData drin
	$challengeS = $_SESSION['challenge'];
	$challengeC = $clientData['challenge'];
	
}
	
	

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;






?>