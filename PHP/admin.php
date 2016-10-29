<?php 
//includes
if(!isset($_SESSION['log'])){
	session_start();
}

require_once('utility.php');
require_once('util.php');

if($_REQUEST['resetLog']=1){
	utility::resetLog();
}


// Session & Log
utility::addLog('Beginne mit Laden der Admin maske');

print_r($_SERVER['PHP_SELF']);

//header("Location: ".$_SERVER['PHP_SELF'].'page=1');

//definitions
$pageTitle = 'admin.php';

echo '<html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
		<script language="javascript" type="text/javascript">
		function btntest_onclick() 
		{
		window.location.href = "http://www.google.com";    
		
		}
		</script>
</head>
<body>';
echo '<h1>Das ist die Page: '.$pageTitle.'</h1><br />'; 
echo '<p>Display and Change of policy and users</p><br />';


$db = db::dbconnect();
utility::addLog('erste log-Meldung erstellt');


//html Set Policy
echo '
<h2>Benutzer Policy erstellen</h2>
		<input id="btntest" type="button" value="Check"
		onclick="return btntest_onclick()" />
		
		';

//loads the benutzertabelle
utility::createTable('PT_USER');
utility::addLog('PT_USER Table erstellt');


//html Benutzertabelle
echo '
<div id="Benutzertabelle">
<h2>Benutzertabelle</h2><input id=getUsers value="Anzeige neu laden" />
		
				<form action="utility.php" method="post" > 
<input id=getUsersLabel value="getUser" disabled=disabled /><input id=getUsers value="" /></br>
<input id=setUser value="setUser" disabled=disabled /><input id=getUsers value="" /></br>
<input id=getUsersLabel value="setPolicy" disabled=disabled /><input id=getUsers value="" /></br>
</form>
		
</div>
		';



//loads the benutzertabelle
utility::createTable('WF_USER');
//utility::addLog('WF_USER Table erstellt');


//test.php
echo '<h2>Test.php</h2>';
require_once('test.php');



//print LogTabelle
utility::addLog('WF_USER Table anzeigen');
echo '<h2>LOG</h2>';
if($_SESSION['log']){
	echo '<table>';
	foreach($_SESSION['log'] as $log){
		echo '<tr><td>';
		echo $log;
		echo '</tr></td>';
	}
	echo '<table>';
}else{
	echo 'no log found!';
}
echo '</div>';


echo '
 </body>
</html>';

?>