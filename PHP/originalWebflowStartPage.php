<?php

/**
 * a dummy page to redirect to, so the flow is correct
 * @autor MT
 */
//includes
if(!isset($_SESSION['log'])){
	session_start();
}

require_once('utility.php');
require_once('util.php');

//definitions
$pageTitle = 'originalWebflowStartPage.php';

// Session & Log
utility::addLog('Beginne mit Laden der '.$pageTitle.' - Maske');


utility::catchResponse($_REQUEST);

//header("Location: ".$_SERVER['PHP_SELF'].'page=1');



echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
		<script language="javascript" type="text/javascript">
		

					
					
		</script>
</head>
<body>';
echo '<h1>Das ist die Page: '.$pageTitle.'</h1><br />';

echo '
				<input type="button" value="Back to Welcome" class="button" id="btnBackToWelcome" 
onClick="document.location.href=\'welcome.php\'" />
		';

?>