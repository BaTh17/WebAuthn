<?php

require_once('util.php');

if(!isset($_POST['username'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage ohne Nutzernamen';
}

//we got a username, now check it
else {
	
	$username = $_POST['username'];
	
	if(checkUsername($username)) {
		//start session
		session_start();
		
		$policy = $_SESSION['policy'] = getPolicy($username);
		$_SESSION['username'] = $username;
		
		/* UserId: Die Unique ID per account
		 * in the webapi description the userid should not be unique because he can have more than one device to login 
		 */
		//$userId = md5(openssl_random_pseudo_bytes(8)); //damit es randomized ist
		$userId = md5(mt_rand(0,100000)); // openssl_random_pseudo_bytes() is not useable for PHP 5.2
		
		//check db for userkeys
		$userHasKeys = $_SESSION['PKeys'] = hasKeys($username);

		//user has no keys but needs it because of policy
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
