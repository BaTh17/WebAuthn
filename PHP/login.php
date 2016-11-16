<?php
require_once('util.php');

session_start();


getConfiguration();
$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];



/*
 * Auf Keys gecheckt. wenn das true wäre, könnte man die Challenge schicken,
 * mit dem Risiko, dass keiner der ID's passt. Dann würde man JavaScript seitig die makeCredential Methode aufrufen.
 * 
 * Wenn false:
 * 	1 (2FA): 	PW Eingabe und Meldung dass keine Keys + Button um Credentials zu generieren.
 * 	2 (HELLO):	Meldung dass keine Keys + Button um Credentials zu generieren
 */
$keys = $_SESSION['PKeys']; 

$pageTitle = 'Login - Page';
echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>
</head>
<body>
		';
echo "<script src='../Client/utils.js'></script> ";
echo "<script src='../Client/webauthn.js'></script>";
echo '<link rel="stylesheet" href="../CSS/default.css" type="text/css">';

echo '<div class="titel" >'.$pageTitle.'</div>';
echo '<div class="centerBox">';



echo "
		<div class='heading' >Known information:</div><br />
		<div class='label float' style='clear:both;' >username:  </div>". $username." <br />
		<div class='label float' >active policy: </div>".$policy."
		<div class='clear'></div><br />
		";

/*
 * Je nach dem welche Policy für den Benutzer aktiv ist, wird der ReturnString zusammengebaut.
 * Bei 2 wird direkt die getAssertion Funktion aufgerufen
 */
$pwCode = "
<div>
<img class='icon' alt='' src='../CSS/key_white.png' >
Enter your password: <input class='rounded' type='text' id='pwInput' onKeydown='Javascript: if (event.keyCode == 13) checkPW()'  autofocus /><br />
<button id='pwButton' class='rounded' onclick='checkPW()'>check password and proceed</button>
</div>
";

//check, if pw is needed
if($policy == 0 || $policy == 1) {
	echo $pwCode;
}

//Entfernen: Denn hier kommt man gar nie rein
else {
	echo "Passwordless is active. Call of getAssertion:<br />";
	echo "<script>hello()</script>";
	
}


echo "<p id='pwState'></p><br />
	";
echo "<br />
		<p id='assertionState'></p>";

echo '</div>'; // from centerBox div

echo '
</body>
</html>';
?>