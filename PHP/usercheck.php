<?php

require_once('util.php');

if(!isset($_POST['username'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erhält keinen Nutzernamen';
}

//D.h. ein username wurde übermittelt, der jetzt gecheckt wird.
else {
	
	$username = $_POST['username'];
	
	if(checkUsername($username)) {
		
		//start session
		session_start();
		
		$policy = $_SESSION['policy'] = getPolicy($username); //int value of policy wird in $policy gespeichert
		
		/*
		 * In DB nun prüfen, ob überhaupt Keys für den User bestehen
		 */
		$userHasKeys = $_SESSION['PKeys'] = hasKeys($username);
		
		$_SESSION['username'] = $username;
		
		//Wenn der User keine Keys hat, aber gleichzeitig die Policy 
		if(!$userHasKeys && ($policy==1 || $policy ==2)) { //Wenn 
			$responseStatus = '202 Accepted';
			$responseText = 'User'.$username. ' OK, aber es existieren keine Keys...';
		}
		
		else { 
		$responseStatus = '200 OK';
		$responseText = json_encode(array("user" => $username, "policy" => $policy));
		//$responseText = 'User'.$username.' OK, forwarding...'; 
		
		// Returns: {"4":"four","8":"eight"}
		
		
		//Eventuell daraus ein JSON machen, wo die Policy mit drin steht, damit im AJAX call auf indexedDB geprüft werden kann wenn Policy 1/2 ist
		}
			
	}
			
	else {
		$responseStatus = '401 Bad Request';
		$responseText = 'Username falsch';
	}
}

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;

?>