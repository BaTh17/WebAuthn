<?php
require_once('util.php');

session_start();

//von welcome.php übergeben
$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];

echo "Active Policy: "+$policy;

//Bei PW Check


/*
 * Auf Keys gecheckt. wenn das true wäre, könnte man die Challenge schicken,
 * mit dem Risiko, dass keiner der ID's passt. Dann würde man JavaScript seitig die makeCredential Methode aufrufen.
 * 
 * Wenn false:
 * 	1 (2FA): 	PW Eingabe und Meldung dass keine Keys + Button um Credentials zu generieren.
 * 	2 (HELLO):	Meldung dass keine Keys + Button um Credentials zu generieren
 */
$keys = $_SESSION['PKeys']; 



$pwCode = "
		
<div>
Enter your password: <br><input type='text' size='30' id='pwInput'><br>
<button id='pwButton' onclick='checkPW()'>Check PW</button>
</div>
		
";

$passwordless = "
		
		";

echo "<script src='../Client/utils.js'></script> ";

/*
 * Je nach dem welche Policy für den Benutzer aktiv ist, wird der ReturnString zusammengebaut.
 * Bei 2 wird direkt die getAssertion Funktion aufgerufen
 */

if($policy == 0 || 1) {
	
	echo $pwCode;
	
}

else {
	echo "Passwordless is active";
}


echo "<p id='pwState'></p><br>username: $username";



	
?>