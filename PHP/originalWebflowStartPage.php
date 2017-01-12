<?php
/**
 * a dummy starting page of your application after the user successfully logged in.
 * redirect to this local site, so the flow is correct. Has to be changed 
 * @autor MT
 */
//includes
session_start();
require_once('util.php');

//definitions
$pageTitle = 'Dummy Application Homepage';

// Session & Log
_plugin_utility::addLog('Beginne mit Laden der '.$pageTitle.' - Maske');

_plugin_utility::catchResponse($_REQUEST);

echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
		<script language="javascript" type="text/javascript"></script>
</head>
<body>';
echo '<div class="titel" >'.$pageTitle.'</div><br />';
echo '<p>This is a dummy site for the homepage of your application.</p>';
echo '<p>The authentication of the user was valid.</p>';
echo '<p>Here ends the Web Authentication API Demo.</p>';
echo '
<input class="button rounded" type="button" value="Back to welcome page" id="btnBackToWelcome"  onClick="document.location.href=\'welcome.php\'" />
		';

?>
