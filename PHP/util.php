<?php

/**
 * @param unknown $username
 * @param unknown $keyID
 * 
 * Returniert den Public Key als String der zu dieser KeyID in der DB abgelegt wurde.
 */

function getPublicKey($username, $keyID){
	
	return "1";
	
}


/**
 * überprüfen ob ein Benutzername in der DB existiert oder ev. gesperrt ist.
 * Return: boolean
 */
function checkUsername($username) {

	//User exists and is activ
	
	if($username=="schf" || $username=="tscm" || $username =="hello") 
		return true;
	else 
		return false;
	
}

/**
 * checkPassword() - PW vom Benutzer überprüfen
 * Return: boolean
 * 
 */

function checkPW($username, $pw) {
	if($username=="schf" && $pw=="test")
		return true;
	if($username=="tscm" && $pw=="test")
		return true;
	
	else
		return false;
}

/**
 * @return: Integer
 * 0 = Password only
 * 1 = 2-FA
 * 2 = Passwordless
 */
function getPolicy($username) {
	
	//returnieren der Policy des Users - vorher nochmals überprüfen ob es ihn gibt:
	
	if($username=="schf")
		return 0;
	if($username=="tscm")
		return 1;
	if($username=="hello")
		return 2;
	
}

/**
 * Prüfen ob für einen mitgegebenen Benutzername Public Keys in der Tabelle gespeichert sind
 * TSCM: 20161016 umbenennt auf hasKeys() , entspricht eher dem, was gemacht wird
 * Return: boolean
 * 
 */
function hasKeys($username) {
	
	if($username=="schf")
		return true;
	if($username=="tscm")
		return false;
	if($username=="hello")
		return true;
	else
		return false;
}

/**
 * @return: boolean
 * Es werden die vom Client übertragenen Credentials in der DB gespeichert
 * Im Moment wäre das der $usernamen, die Key/Cred-ID und der Public Key
 * 
 * Alle Parameter werden als Strings übergeben. Der Public Key ist hier Base64URL encodiert.
 * Das ist aber okay, da ich das in der Verifizierung dekodiere.
 */
function saveCredentials($username, $id, $pubKey) {

return true;

}

function getChallenge() {
	
	return md5(openssl_random_pseudo_bytes(16));
}


?>