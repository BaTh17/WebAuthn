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



//header("Location: ".$_SERVER['PHP_SELF'].'page=1');

//definitions
$pageTitle = 'admin.php';

echo '<html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
		<script language="javascript" type="text/javascript">
		function createPolicy() 
		{
		window.location.href = "'.$_SERVER['PHP_SELF'].'?createPolicy=1&userid=1&policyid=2";    
		
		}
		</script>
</head>
<body>';
echo '<h1>Das ist die Page: '.$pageTitle.'</h1><br />';


$db = new db;
utility::addLog('erste log-Meldung erstellt');


//html Set Policy
echo '
<h2>Benutzer Policy Tabelle (PT_USER)</h2>
		<span>Policy erstellen:</span>
		<input id="createPolicy" type="text" value="" onclick="return createPolicy()" />
		<input id="createPolicy" type="button" value="createPolicy" onclick="return createPolicy()" />
		';
utility::createTable('PT_USER','');

//loads the benutzertabelle
utility::createTable('PT_USER');


//html Benutzertabelle
echo '
<div id="Benutzertabelle (WF_USER)">
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