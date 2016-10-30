<?php

require_once('util.php');

session_start();
$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];


if(!isset($_POST['password'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erhält kein Passwort';
}

$password = $_POST['password'];


/*
 * Check von Passwort ob das korrekt ist.
 * Danach wird entschieden, ob direkt zum Webflow weitergeleitet wird
 * oder die getAssertion() Funktion aufgerufen wird
 */

	if(checkPW($username, $password)) {
		//OK
		$responseStatus = '200 OK';
		//Weil vielleicht noch neue Policies dazu kommen mit switch arbeiten
		switch($policy) {
			case 0: {
				$responseText = "window.location = 'https://www.5webflow.ch/category/allgemein/';";
				break;
			}
			case 1: {

				$_SESSION['challenge'] = $challenge = getChallenge();
				
				$responseText = "getAssertion('$challenge');";
				
				//Alternative: redirect to getAssertion.php, aber dort noch prüfen ob PW Check okay war.
				
				break;
			}
			//Case 2 existiert nicht, da Benutzer mit dieser Policy bereits nach dem Benutzercheck zu getAssertion.php geleitet wurden
	
			}
		}	
	else {
		$responseStatus = '401 Bad Request';
		$responseText = 'Falsches PW';
		
}
		
header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;

?>

