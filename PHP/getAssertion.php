<?php
require_once('util.php');

session_start();

$_SESSION['challenge'] = $challenge = md5(mt_rand(12,12));

echo "<script src='../Client/utils.js'></script> ";
echo "<script src='../Client/webauthn.js'></script>";

echo 'Challenge: '.$challenge; //ist pro Session


/*
 * Bau des zurückgegebenen JS Codes, dert aus dem util.js die getAssertionFunktion, analog Polyfill aufruft.
 * Direkt auch möglich?
 */
$getAssertionCode = "
		<html>
		<head> </head>

		<body>
			<br><p id='status'></p><br>
		
		<script>
		var id;
		
		console.log('assertion-Call forciert von getAssertion.php');
		
		navigator.authentication.getAssertion('$challenge').then(function(assertion) {
		console.log('sending assertion');
		document.getElementById('status').innerHTML = assertion.credential.id;
		
		/*Funktionsaufruf für Ajax Call - direkt den call mit anonymer Funktion machen hat nicht geklappt*/
		handleAssertion(JSON.stringify(assertion));				

	

		});

		</script>	

		</body>
		</html>
";

echo $getAssertionCode;


?>