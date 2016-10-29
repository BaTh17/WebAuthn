<?php
require_once('util.php');

session_start();

$_SESSION['challenge'] = $challenge = getChallenge();

echo "<script src='../Client/utils.js'></script> ";
echo "<script src='../Client/webauthn.js'></script>";

echo 'Challenge: '.$challenge; //ist pro Session
echo "<br><p id='assertionState'></p>"; //hier kommt wie bei der login.php Seite das Resultat des Assertion Checks rein


/*
 * Bau des zur�ckgegebenen JS Codes, dert aus dem util.js die getAssertionFunktion, analog Polyfill aufruft.
 * Direkt auch m�glich?
 */

$getAssertionCode = "
		<html>
		<head> </head>

		<body>
			<br><p id='status'></p><br>
		
		<script>
		var id;
		
		console.log('assertion-Call forciert von getAssertion.php');
		getAssertion('$challenge');

		</script>	

		</body>
		</html>
";

echo $getAssertionCode;


?>