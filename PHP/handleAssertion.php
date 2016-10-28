<?php

require_once('util.php');
include('Crypt/RSA.php');

session_start();

$responseText = "empty";
$clientData = "";
$challengeC = "";
$signature = "";

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
	$assertionJs = json_decode($assertion,true); //macht ein assoziatives Array aus der Assertion die als JSON daherkam
	
	$type = $assertionJs['credential']['type'];
	$id = $assertionJs['credential']['id'];
	$clientData = $assertionJs['clientData'];
	$signature = $assertionJs['signature'];
	
	$cDataJs = base64_decode($clientData); //Danach liegt cData als { "challenge" : "c232...." } vor: 
	$cData = json_decode(trim($cDataJs)); //Vgl.https://github.com/adrianba/fido-snippets/blob/master/php/fido-authenticator.php
	
	$cChallenge = $cData->{'challenge'};
	
	$responseStatus = '200 OK';
	$responseText = "Challenge-Vergleich: ". validateAssertion($cChallenge). 
	" ClientData: " .$cChallenge. "   |     Assertion mit ID:".$id." und Signatur: ".$signature;
		
	}

/* 
 * Validieren der Assertion
 */
function validateAssertion($ChallengeFromClient) {
	//Pr�fen, ob die Challenges matchen. Die Challenge ist in den ClientData drin
	global $challengeC;
	global $signature;
	
	if($_SESSION['challenge']!=$ChallengeFromClient) {
		return "Falsche Challenge!";
	}
	else
		"Challenge matchen!";
	
	/* �berpr�fen der Signatur */
	
	/* Prepare Public Key */
	$rsa = new Crypt_RSA();
	
	$cSignature = json_decode(trim(base64_decode($signature)));
	$cSignature = $cSignature->{'challenge'};
	
	
	
	
}
	
	

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;






?>