<?php

require_once('util.php');

session_start();
$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];

getConfiguration();
$webflowPageAfterSuccess = $_SESSION['redirectToAfterSuccess'];

if(!isset($_POST['password'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Call does not contain a password';
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
				$responseText = "window.location = ".$webflowPageAfterSuccess.";";
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
		$responseText = 'Wrong password';
		
}
		
header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;

?>

