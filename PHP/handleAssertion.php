<?php
session_start();
$modulus = 1;


require_once('util.php');
require('Crypt\RSA.php');

$responseText = "empty";
$username = $_SESSION['username'];

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
	
	/*Die Assertion in ihre einzelnen Teile zerlegen. Vergleich: Code Snippets von Microsoft */
	
	//Credential
	$type = $assertionJs['credential']['type'];
	$id = $assertionJs['credential']['id']; //Assertion ID, aber gleichzeitig die ID zum hinterlegten PublicKey
	
	//ClientData (noch base64 encodiert): Enthält die Challenge
	$clientData = $assertionJs['clientData']; //clientData liegen nun vor als: ew0KCSJjaGFsbGVuZ2UiIDogImMyMGFkNGQ3NmZlOTc3NTlhYTI3YTBjOTliZmY2NzEwIg0KfQA
	$c = rfc4648_base64_url_decode($clientData);//Danach liegt cData als { "challenge" : "c232...." }
		
	//Extrahieren der Challenge
	$cData = json_decode(trim($c)); //das macht das ganze zu einem Array, damit ich die Challenge unten auslesen kann
	$cChallenge = $cData->{'challenge'}; //Zugriff auf den Value mit Key "challenge"
	
	//AuthenticatorData
	$authnData64 = $assertionJs['authenticatorData'];
	$a = rfc4648_base64_url_decode($authnData64);
	
	//Signature 
	$signature = $assertionJs['signature']; //B8UoaYhTXdTG7O83iQVpGnhKqizl5182q-KQdSpvP8E_2vi115xlVbEIuHEK....
	$s = rfc4648_base64_url_decode($signature);
	
	//Challenge vergleichen
	if($_SESSION['challenge']!=$cChallenge) {
		$responseStatus = '400 Bad Request';
		$responseText = 'Fehlerhafte Challenge';
	}
	
	//Restliche Assertion (->Signatur) überprüfen
	if(validateAssertion($c,$a,$s)) {
		$responseStatus = '200 OK';
		$responseText = "Validierung erfolgreich. Leite weiter zum Webflow. Modulus: ".$modulus;
	}
	else {
		$responseStatus = '400 Bad Request';
		$responseText = 'Fehlerhafte Assertion';
	}
	//" ClientData B64: " .$clientData. " danach b64decodiert: ".$c." |   AuthData: ". $authnData64. "  |   Assertion mit ID:".$id." und Signatur: ".$signature;
	
		
} //End of Else 


/* Validieren der Assertion */
	
function validateAssertion($c,$a,$s) {
	
	//HASH DATA
	$hash = new Crypt_Hash('sha256');
	$h = $hash->hash($c); //$c welches als { "challenge" : "c232...." } vorliegt wird gehasht und danach konkateniert (unten mit $a.$h)
	
	//LOAD PUBLIC KEY
	$rsa = new Crypt_RSA();
	$rsa = buildPubKey("c20ad4d76fe97759aa27a0c99bff6710", $rsa);
	
	//Vorbereiten der Validierung
	$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
	$rsa->setHash('sha256');
	
	// Verify signature is correct for authnrData + hash: Das decrypted nun die Signatur und vergleich sie mit dem hash
	$result = $rsa->verify($a . $h,$s);
	
	return $result;

}


function buildPubKey($id, $rsa) {

	global $modulus;
	//global $username;
	$username = "hello";
	
	// Mockup
	//$pKey64 = "4K5_6m8Lq-VQ5JfyDafJGU4-Jk5hCdSUFum_gGU7AZUFbPjeViY1NZLZTHWhFL-UCzhUuimMgC5KlE8Ixm5rVUbLe7FsBR8YAPIDbF5OBtZiM46HCBRASqLgRAfg-Vh3Oo9KBZj-kGWSq9MNnTXR-ErokajymsuJqn3C_Od9aKk3qe_KDZmspgTx12_GXgFmxZaVS7ajLEeZ_gNpQjQ8pbUSMRc1e5dzIofZt6_4VqgekwDwEdrSnDEPtNMBIO6gQq5sN-bHCeLLxggYFHNYxXbWxPHIyOxrnUyAIhNPQ21Wt6ttKYJ4NTr_cW7pMQZPaGte48L2YbPtuanHT0iDaQ";
	
	//NOK:
	$pKey64 = util.php::getPublicKey($username,$id);
	
	$n = rfc4648_base64_url_decode($pKey64);

	$e64 = "AQAB"; //ist statisch (Exponent)
	$e = rfc4648_base64_url_decode($e64);

	/* Math_BigInteger($e,256)
	 * 1. Parameter: .....
	 * 2. Parameter: 256 = Basis der Zahl?  */

	//Bau des Keys. Der Public Key "setzt" sich aus Exponent und Modulus zusammen => http://crypto.stackexchange.com/questions/18031/the-modulus-of-rsa-public-key
	$raw = array("n"=>new Math_BigInteger($n,256),"e"=>new Math_BigInteger($e,256));
	$exponent = $raw['e']; //65537 => Das ist der Exponent
	$modulus = $raw['n']; //Modulus, Produkt zweier grosser Primzahlen.

	//loadKey akzeptiert scheinbar ein Array, wo der Exponent und der Modulus als Elemente einetragen sind:
	$rsa->loadKey($raw);
	return $rsa;
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

	
header($_SERVER['SERVER_PROTOCOL'].' '.$responseStatus);
header('Content-type: text/html; charset=utf-8');
echo $responseText;


?>