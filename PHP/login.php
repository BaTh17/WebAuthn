<?php

if(!isset($_POST['username'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erhält keinen Nutzernamen';
}

//D.h. ein username wurde übermittelt, der jetzt gecheckt wird.
else {
	if(util::checkUser ($username)) {
		$responseStatus = '200 OK';
		$responseText = 'Credentials OK, forwarding...';
	}
			
	else {
		$responseStatus = '401 Bad Request';
		$responseText = 'Username oder Passwort falsch';
	}
}

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;

?>