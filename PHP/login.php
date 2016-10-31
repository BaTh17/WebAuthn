<?php
require_once('util.php');

session_start();

$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];

echo "Active Policy: ".$policy."<br><br>";
echo "<script src='../Client/utils.js'></script> ";
echo "<script src='../Client/webauthn.js'></script>";


/*
 * Auf Keys gecheckt. wenn das true w�re, k�nnte man die Challenge schicken,
 * mit dem Risiko, dass keiner der ID's passt. Dann w�rde man JavaScript seitig die makeCredential Methode aufrufen.
 * 
 * Wenn false:
 * 	1 (2FA): 	PW Eingabe und Meldung dass keine Keys + Button um Credentials zu generieren.
 * 	2 (HELLO):	Meldung dass keine Keys + Button um Credentials zu generieren
 */
$keys = $_SESSION['PKeys']; 

$pwCode = "
		
<div>
Enter your password: <br><input type='text' size='30' id='pwInput'><br>
<button id='pwButton' onclick='checkPW()'>check and proceed</button>
</div>
		
";

//$passwordless = "";

echo "<script src='../Client/utils.js'></script> ";

/*
 * Je nach dem welche Policy f�r den Benutzer aktiv ist, wird der ReturnString zusammengebaut.
 * Bei 2 wird direkt die getAssertion Funktion aufgerufen
 */

if($policy == 0 || $policy == 1) {
	
	echo $pwCode;
	
}

else {
	echo "Passwordless is active. Call of getAssertion:<br>";
	echo "<script>hello()</script>";
	
}


echo "<p id='pwState'></p><br>username:". $username;
echo "<br><p id='assertionState'></p>";




	
?>