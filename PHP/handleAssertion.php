<?php

require_once('util.php');

session_start();

$responseText = "empty";

/* Korrekten HTTP Status Code reintun*/
if(!isset($_POST['assertion'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erhält keine Assertion';
}

//D.h. ein username wurde übermittelt, der jetzt gecheckt wird.
else {
	//Die übertragene Assertion wird als JSON empfangen und dann in ihre einzelnen Strings zerlegt:
	//Eigentlich müsste hier noch immer eine Prüfung rein, um es von dem und dem Format ist, oder dann am Ende alle Werte auf undefined / empty prüfen?
	
	$assertion = $_POST['assertion'];
	$assertionJs = json_decode($assertion,true);
	$type = $assertionJs['credential']['type'];
	$id = $assertionJs['credential']['id'];
	$clientData = $assertionJs['clientData'];
	$signature = $assertionJs['signature'];
	
	$responseStatus = '200 OK';
	$responseText = "Assertion mit ID:".$id." und Signatur: ".$signature;
		
	}

/* 
 * Validieren der Assertion
 */

	
	

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;






?>