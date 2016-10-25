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
		//Wenn das PW okay ist, gebe ich abhängig von der Policy noch die getChallenge() zurück oder leite gleich zum Webflow
		//Weil vielleicht noch neue Policies dazu kommen mit switch arbeiten
		switch($policy) {
			case 0: {
				$responseText = "window.location = 'https://www.5webflow.ch/category/allgemein/';";
				break;
			}
			case 1:
				/*
				 * Bei Case 1 muss nun nach dem erfolgereichen PW Check noch eine Assertion geholt werden.
				 * Dabei geben wir
				 */
				$responseText = 				
				"var x = document.createElement('script');
				x.src = '../Client/test.js';
				document.getElementsByTagName('head')[0].appendChild(x);
				
				alert('loading lorem');
				lorem();		
						
				";
				
				
				//$responseText = "alert('Invoke getAssertion because+".$policy."')";
				break;
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

