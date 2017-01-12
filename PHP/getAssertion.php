<?php
/**
 * assertion-Call forciert von getAssertion.php
 * @author FS
 */
require_once('util.php');

session_start();

$_SESSION['challenge'] = $challenge = getChallenge();
$webflowPageAfterSuccess = $_SESSION['redirectToAfterSuccess'];
$userName = $_SESSION['username'];
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

echo "
		<p class='heading topPadding' >Working with these values:</p>
		<table class='systemInfoTable systemInfoLayout'>
		<tr tr class='systemInfoTable'><td class=' systemInfoTable systemInfoLabel label' >Challenge String: </td><td class='systemInfoTable systemInfoMessage'>".$challenge."</td></tr>
		<tr tr class='systemInfoTable'><td class=' systemInfoTable systemInfoLabel label' >Given Username:</td><td class='systemInfoTable systemInfoMessage'>".$_SESSION['username']."</td></tr>
		<tr tr class='systemInfoTable'><td class=' systemInfoTable systemInfoLabel label' >Used Policy:</td><td class='systemInfoTable systemInfoMessage'>".$_SESSION['policy']."</td></tr>
		<tr tr class='systemInfoTable'><td class=' systemInfoTable systemInfoLabel label' >AssertionState Info: </td><td class='systemInfoTable systemInfoMessage' id='assertionStateInfo'></td></tr>
		</table>
		<br />";
echo "<p id='assertionState'></p>"; // result check of getAssertion will be printed here


$getAssertionCode = "<p id='status'></p><br />		
						<script>
						console.log('assertion-Call forciert von getAssertion.php');
						getAssertion('$challenge','$webflowPageAfterSuccess','$userName');
						</script>
					";
echo $getAssertionCode;

echo '</div>'; // end centerBox
echo "</body>
		</html>";



?>
