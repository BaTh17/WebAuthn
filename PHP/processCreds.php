<?php
require_once('util.php');

session_start();

if(!isset($_POST['credentials'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Keine Credentials verschickt!';
}

else if (!isset($_SESSION['username'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'NO USERNAME';
}


else {
	
	$username = $_SESSION['username'];
	
	$credentials = $_POST['credentials'];
	$credString = json_decode($credentials, true);
	$id = $credString['credential']['id'];
	$publicKey = $credString['publicKey']['n'];
			
		
	if(saveCredentials($username,$publicKey,$id)) { //Den Check auf einen validen Public Key und Usernamen machen wir in der Funktion
		$responseStatus = '200 OK';
		$responseText = 'Credentials versandt. Auf dem Server wird  beim User '.$username.' folgendes gespeichert: ID: '.$id.' und der PublicKey: '.$publicKey;
	};
	
}

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;

?>
