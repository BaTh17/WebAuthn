<?php

require_once('util.php');

if(!isset($_POST['username'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erh�lt keinen Nutzernamen';
}

//D.h. ein username wurde �bermittelt, der jetzt gecheckt wird.
else {
	
	$username = $_POST['username'];
	
	if(checkUsername($username)) {
		
		//start session & setzen der Session Variablen
		session_start();
		
		$policy = $_SESSION['policy'] = getPolicy($username);
		$_SESSION['username'] = $username;
		
		/*die UserId: Die Unique ID f�r einen Account.
		 *damit wir mit unserem Konzept weiterfahren k�nnen darf die im Moment noch nicht unique sein.
		 *Grund in Doku beschrieben, wegen Ger�tewechsel w�rde das problematisch werden
		 */
		//$userId = md5(openssl_random_pseudo_bytes(8)); //damit es randomized ist
		$userId = md5(mt_rand(0,100000)); // openssl_random_pseudo_bytes() is not useable for PHP 5.2
		
		//In DB nun pr�fen, ob �berhaupt Keys f�r den User bestehen
		$userHasKeys = $_SESSION['PKeys'] = hasKeys($username);
		
		//Wenn der existierende User keine Keys hat, diese aber ben�tigt
		if(!$userHasKeys && ($policy==1 || $policy ==2)) { //Wenn 
			$responseStatus = '202 Accepted';
			$message = "OK. But no registered keys";
			//$responseText = 'User'.$username. ' OK. No registered keys.';
			$responseText = json_encode(array("message" => $message, "user" => $username, "userId" => $userId, "policy" => $policy));
		}
		
		else { 
		$responseStatus = '200 OK';
		$responseText = json_encode(array("user" => $username, "userId" => $userId, "policy" => $policy)); 
		//$responseText = 'User'.$username.' OK, forwarding...'; 
		}
			
	}
			
	else {
		$responseStatus = '401 Bad Request';
		$responseText = 'unknown username';
	}
}

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;

?>