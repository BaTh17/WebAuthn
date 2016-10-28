<?php
require_once('util.php');

session_start();

$_SESSION['challenge'] = $challenge = getChallenge();

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
		getAssertion('$challenge');

		</script>	

		</body>
		</html>
";

echo $getAssertionCode;


?>