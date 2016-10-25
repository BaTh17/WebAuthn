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
		
		
		$_SESSION['policy'] = getPolicy($username); //get Policy
		$_SESSION['username'] = $username;
		$_SESSION['PKeys'] = checkKeys($username);
		
		$responseStatus = '200 OK';
		$responseText = 'User OK, forwarding...';
			
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