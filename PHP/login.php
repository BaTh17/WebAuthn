<?php
require_once('util.php');

session_start();


$username =  $_SESSION['username'];
$policy =  $_SESSION['policy'];



/*
 * Check the keys, if it was true, the challenge can be send
 * with the risk that none of the IDs will match. Then one would call the makeCredential mehtode from the client (javascript)
 * 
 * if false:
 * 	1 (2FA): 	password entry and a message that no keys are found and a button to create credentials
 * 	2 (HELLO):	show a message that no keys are found + show button to generate credentials
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
		<p class='heading topPadding' >Known information:</p>
		<table class='systemInfoTable systemInfoLayout'>
		<tr tr class='systemInfoTable'><td class=' systemInfoTable systemInfoLabel label' >Username:</td><td class='systemInfoTable systemInfoMessage'>".$username."</td></tr>
		<tr tr class='systemInfoTable'><td class=' systemInfoTable systemInfoLabel label' >Active policy:</td><td class='systemInfoTable systemInfoMessage'>".$policy."</td></tr>
		</table>
		<br />";

/*
 * Depending on the policy for the active user, the return-string is build
 * policy calls getAssertion function directly
 */
$pwCode = "
<div>
<img class='icon' alt='' src='../CSS/key_white.png' >
Enter your password: <input class='rounded' type='text' id='pwInput' onKeydown='Javascript: if (event.keyCode == 13) checkPW(\"".$_SESSION['redirectToAfterSuccess']."\",\"".$policy."\")'  autofocus /><br />
<br />
<button id='pwButton' class='rounded' onclick='checkPW(\"".$_SESSION['redirectToAfterSuccess']."\",\"".$policy."\")'>check password and proceed</button>
</div>
";

//check, if pw is needed
if($policy == 0 || $policy == 1) {
	echo $pwCode;
}

//should never display but could be used if the order of checks is changed
else {
	echo "Passwordless is active. Call of getAssertion:<br />";
	echo "<script>hello()</script>";
	
}

echo "<p id='pwState' class='message' ></p>";
echo "<p id='assertionState'  class='message'></p>";

echo '</div>'; // from centerBox div

echo '
</body>
</html>';
?>
