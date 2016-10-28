<?php

require_once('util.php');

session_start();
$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];


if(!isset($_POST['password'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erh�lt kein Passwort';
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
		//Wenn das PW okay ist, gebe ich abh�ngig von der Policy noch die getChallenge() zur�ck oder leite gleich zum Webflow
		//Weil vielleicht noch neue Policies dazu kommen mit switch arbeiten
		switch($policy) {
			case 0: {
				$responseText = "window.location = 'https://www.5webflow.ch/category/allgemein/';";
				break;
			}
			case 1: {
				/*
				 * Bei Case 1 muss nun nach dem erfolgereichen PW Check noch eine Assertion geholt werden.
				 * Darum auch eine Challenge generieren.
				 */
				$_SESSION['challenge'] = $challenge = getChallenge();
				
				$responseText = "				
				var x = document.createElement('script');
				x.src = '../Client/test.js';
				document.getElementsByTagName('head')[0].appendChild(x);
				getAssertion('$challenge');				
				";
				break;
			}
			//Case 2 wird es gar nicht geben, da hier ja kein Passwort �berpr�ft wird.
			//Einem User mit Policy = 2 wird nach erfolgreichen Userlookup & der Pr�fung nach Keys die getAssertion geschickt
				
				
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

