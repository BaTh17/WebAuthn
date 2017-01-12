<?php

require_once('util.php');

session_start();
$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];

$webflowPageAfterSuccess = $_SESSION['redirectToAfterSuccess'];

if(!isset($_POST['password'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Call does not contain a password';
}

$password = $_POST['password'];
/*
 * Check if password is correct
 * then, depending on policy, what is the next step: redirect to webflow or getAssertion
 */
	if(checkPW($username, $password)) {
		//OK
		$responseStatus = '200 OK';
		//Weil vielleicht noch neue Policies dazu kommen mit switch arbeiten
		switch($policy) {
			case 0: {
				_plugin_Authentication::doThesisLogin($username);
				$responseText = "window.location = '".$webflowPageAfterSuccess."';";
				
				break;
			}
			case 1: {
				$_SESSION['challenge'] = $challenge = getChallenge();

				$responseText = "getAssertion('$challenge','$webflowPageAfterSuccess','$username');";
				
				//alt: redirect to getAssertion.php, and check for password after hello was successful
				
				break;
			}
			 //will not be reached in this prototyp
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

