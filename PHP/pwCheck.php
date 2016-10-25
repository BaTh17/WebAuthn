<?php

require_once('util.php');

session_start();
$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];


if(!isset($_POST['password'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erh�lt kein Passwort';
}

//D.h. ein username wurde �bermittelt, der jetzt gecheckt wird.
$password = $_POST['password'];

	if(checkPW($username, $password)) {
		//OK
		$responseStatus = '200 OK';
		//Wenn das PW okay ist, gebe ich abh�ngig von der Policy noch die getChallenge() zur�ck oder leite gleich zum Webflow
		//Weil vielleicht noch neue Policies dazu kommen mit switch arbeiten
		switch($policy) {
			case 0:
				$responseText = "alert('PW OK')";
				break;
			case 1:
				$responseText = "alert('Invoke getAssertion')";
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

