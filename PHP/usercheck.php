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
		
		//start session & setzen der Session Variablen
		session_start();
		
		$policy = $_SESSION['policy'] = getPolicy($username);
		$_SESSION['username'] = $username;
		
		//In DB nun prüfen, ob überhaupt Keys für den User bestehen
		$userHasKeys = $_SESSION['PKeys'] = hasKeys($username);
		
		//Wenn der existierende User keine Keys hat, diese aber benötigt
		if(!$userHasKeys && ($policy==1 || $policy ==2)) { //Wenn 
			$responseStatus = '202 Accepted';
			$responseText = 'User'.$username. ' OK, aber es existieren keine Keys...';
		}
		
		else { 
		$responseStatus = '200 OK';
		$responseText = json_encode(array("user" => $username, "policy" => $policy)); 
		//$responseText = 'User'.$username.' OK, forwarding...'; 
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