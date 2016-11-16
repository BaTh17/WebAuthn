<?php
/**
 * assertion-Call forciert von getAssertion.php
 * @author FS
 */
require_once('util.php');

session_start();

$_SESSION['challenge'] = $challenge = getChallenge();


//start html
$pageTitle = 'getAssertion - Page';
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
echo '<p class="heading" >Working with these values:</p>';
echo '<div class="label float">Challenge String: </div>'.$challenge.'<br />';
echo '<div class="label float">Given Username: </div>'.$_SESSION['username'].'<br />';
echo '<div class="label float">Used Policy: </div>'.$_SESSION['policy'].'<br />';
echo "<div class='label float'>AssertionState Info: </div><div id='assertionStateInfo'></div><br />";
echo "<p id='assertionState'></p>"; //hier kommt wie bei der login.php Seite das Resultat des Assertion Checks rein


$getAssertionCode = "<p id='status'></p><br />		
						<script>
						console.log('assertion-Call forciert von getAssertion.php');
						getAssertion('$challenge');
						</script>
					";
echo $getAssertionCode;

echo '</div>'; // end centerBox
echo "</body>
		</html>";



?>