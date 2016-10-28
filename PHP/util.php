<?php


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
		return true;
	if($username=="hello")
		return true;
	else
		return false;
}

/**
 * @return: boolean
 * Es werden die vom Client übertragenen Credentials in der DB gespeichert
 */
function saveCredentials($username, $id, $pubKey) {

return true;

}



?>