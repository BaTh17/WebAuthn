<?php
session_start();

require_once('util.php');
require('Crypt\RSA.php');

$responseText = "empty";


/* Korrekten HTTP Status Code reintun*/
if(!isset($_POST['assertion'])) {
	$responseStatus = '401 Bad Request';
	$responseText = 'Anfrage erhält keine Assertion';
}

//D.h. eine Assertion wurde übermittelt, die wir jetzt überprüfen
else {
	//Die übertragene Assertion wird als JSON empfangen und dann in ihre einzelnen Strings zerlegt:
	//Eigentlich müsste hier noch immer eine Prüfung rein, um es von dem und dem Format ist, oder dann am Ende alle Werte auf undefined / empty prüfen?
	
	$assertion = $_POST['assertion'];
	$assertionJs = json_decode($assertion,true); //macht ein assoziatives Array aus der Assertion die als JSON daherkam
	
	/*Die Assertion in ihre einzelnen Teile zerlegen. Vergleich: */
	
	//Credential
	$type = $assertionJs['credential']['type'];
	$id = $assertionJs['credential']['id'];
	
	//ClientData (noch base64 encodiert): Enthält die Challenge
	$clientData = $assertionJs['clientData']; //clientData liegen nun vor als: ew0KCSJjaGFsbGVuZ2UiIDogImMyMGFkNGQ3NmZlOTc3NTlhYTI3YTBjOTliZmY2NzEwIg0KfQA
	//$c = base64_decode($clientData); //Danach liegt cData als { "challenge" : "c232...." }
	$c = rfc4648_base64_url_decode($clientData);
		
	
	$cData = json_decode(trim($c)); //das macht das ganze zu einem Array, damit ich die Challenge unten auslesen kann
	$cChallenge = $cData->{'challenge'}; //Zugriff auf den Value mit Key "challenge"
	
	//AuthenticatorData
	$authnData64 = $assertionJs['authenticatorData'];
	//$a = "1000000"; //statisch, da es so mitgegeben wird und die Dekodierung noch ein Problem ist
	$a = rfc4648_base64_url_decode($authnData64);
	//$authnData2 = bin2hex($authnData64);
	
	//Signature 
	$signature = $assertionJs['signature']; //B8UoaYhTXdTG7O83iQVpGnhKqizl5182q-KQdSpvP8E_2vi115xlVbEIuHEK....
	//$s = base64_decode($signature);
	$s = rfc4648_base64_url_decode($signature);
	
	$responseStatus = '200 OK';
	$responseText = "Validierung:". validateAssertion($cChallenge,$c,$a,$s). 
	" ClientData B64: " .$clientData. " danach b64decodiert: ".$c." |   AuthData: ". $authnData64. "  |   Assertion mit ID:".$id." und Signatur: ".$signature;
		
	}

	
/* Validieren der Assertion */
	
function validateAssertion($ChallengeFromClient,$c,$a,$s) {
	//Prüfen, ob die Challenges matchen. Die Challenge ist in den ClientData drin
	
// 	if($_SESSION['challenge']!=$ChallengeFromClient) {
// 		return "Falsche Challenge!";
// 	}
// 	else
// 		return "Challenge matchen!";
	
	/* Überprüfen der Signatur */
	
	//HASH DATA
	$hash = new Crypt_Hash('sha256');
	$h = $hash->hash($c); //$c welches als { "challenge" : "c232...." } vorliegt wird gehasht und danach konkateniert (unten mit $a.$h)
	
	//LOAD PUBLIC KEY
	$rsa = new Crypt_RSA();
	
	$pKey64 = "gpplFmlkpwee0lZQ5ZNkfKnA6xjXr_xkgL0zMXBThWkP9zFSveowDrHqS8hueV44U0jicgz_4fDkF7qR3tgJN0_-STnoEcZ-iSQrVw71OBare-x6fp6f5G4ApdXUhOCSxxauYAtPO3W3r8aJXNqn40ijfJbIK-3SSWF-qqHnMwjojLLpnwjap76PsfQHOUpl_p9FxKdbU6k1Y-SGTN8HvXRYiA-Uabem6ok6Thw4EgX3AGr3DleoHjpMhmegcMGO7aVBokq0Q8fpPAsmSMZ6Tfemj7zYJCRg8eQb1XTg-SStKvovCWDyBbBfzUOhI_JiOEBctsje6XYLjaEem2RDPw";
	$n = rfc4648_base64_url_decode($pKey64);
	
	$e64 = "AQAB"; //ist statisch
	$e = rfc4648_base64_url_decode($e64); //decodiert ist dies
	
	
	/* Weshalb bauen die ein Array aus den beiden Strings und übergeben das? */
	$raw = array("n"=>new Math_BigInteger($n,256),"e"=>new Math_BigInteger($e,256));
	
	$rsa->loadKey($raw);

	
	//Decoden der Signatur (Manuell)
	//$signature = "B8UoaYhTXdTG7O83iQVpGnhKqizl5182q-KQdSpvP8E_2vi115xlVbEIuHEK1qjS7Csz0N493xsqT-Ikn0AwBtzskV1ymS407kcCOGf_R9l9no7QlpzpNWJuanXjuPpwoEmMs4AUhZom9eQqFYHVFwY4n_2hdrUUFeuINP4ph-wN-MNQmpX5O_wMIjZkgS7Zxg0kI0g1pB77YbdBxggo4F7i8VwLwbPgJJtpTHU9d_2RoXCQRrbkjWqv5oiXBtFcfLZgglSeHylhAgAXTpB8jETkPctls1sVQSmbnm5oGx97q904t31mWkwB3CHAGWOLxH_YwAa1u3O1QWRJO6myTg";
	
	$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
	$rsa->setHash('sha256');
	
	
	
	// Verify signature is correct for authnrData + hash: Das decrypted nun die Signatur und vergleich sie mit dem hash
	$result = $rsa->verify($a . $h,$s);
	
	if($result)
		return "TRUE";
	else
		return "False";
	//return $result;
}
	
function rfc4648_base64_url_decode($url) {
	$url = str_replace('-', '+', $url); // 62nd char of encoding
	$url = str_replace('_', '/', $url); // 63rd char of encoding
	switch (strlen($url) % 4) // Pad with trailing '='s
	{
		case 0:
			// No pad chars in this case
			break;
		case 2:
			// Two pad chars
			$url .= "==";
			break;
		case 3:
			// One pad char
			$url .= "=";
			break;
		default:
			$url = FALSE;
	}
	if($url) $url = base64_decode($url);
	return $url;
}


function loadJWK($rsa,$pk)
{
	$jpk = json_decode($pk);
// 	if($jpk->{'kty'}!='RSA' || $jpk->{'alg'}!='RS256') throw new Exception('Invalid key type.');
	
	$n = fido_authenticator::rfc4648_base64_url_decode($jpk->{'n'});
	$e = fido_authenticator::rfc4648_base64_url_decode($jpk->{'e'});
	
	$raw = array("n"=>new Math_BigInteger($n,256),"e"=>new Math_BigInteger($e,256));
	
	$rsa->loadKey($raw);
}

	

header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;







?>