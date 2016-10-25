<?php


/*
 * �berpr�fen ob ein Benutzername in der DB existiert oder ev. gesperrt ist.
 * Return: boolean
 */
function checkUsername($username) {

	if($username=="schf" || $username=="tscm") 
		return true;
	else 
		return false;
	
}

/*
 * checkPassword() - PW vom Benutzer �berpr�fen
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

/*
 * Return: Integer
 * 0 = Password only
 * 1 = 2-FA
 * 2 = Passwordless
 */
function getPolicy($username) {
	
	//returnieren der Policy des Users - vorher nochmals �berpr�fen ob es ihn gibt:
	
	if($username=="schf")
		return 0;
	if($username=="tscm")
		return 1;
	if($username=="hello")
		return 2;

	
}

/*
 * Pr�fen ob f�r einen mitgegebenen Benutzername Public Keys in der Tabelle gespeichert sind
 * Return: boolean
 */

function checkKeys($username) {
	
	if($username=="schf")
		return true;
	
	else
		return false;
}



?>