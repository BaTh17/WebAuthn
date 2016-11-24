<?php 

/**
 * the administratior mask to view and regulate the policies
 * @autor MT
 */
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


utility::catchResponse($_REQUEST);

//header("Location: ".$_SERVER['PHP_SELF'].'page=1');

//definitions
$pageTitle = 'Admin - Page';

echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
		<script language="javascript" type="text/javascript">
		
		function createPolicy() 
		{
			var Varuserid = document.getElementById("select_WF_USER_USERID").value;
			var Varpolicy = document.getElementById("select_PT_USER_USERID").value;
			var VarStart = "createPolicy";
		alert(Varuserid);
		alert(Varpolicy);
		//TODO TODO TODO
		var getFullstring = VarStart.concat();
		//alert(?createPolicy=1&userid=Varuserid&policyid=Varpolicy);
		//id=select_'.$tableName.'_'.$id.'
			window.location.href = "'.$_SERVER['PHP_SELF'].'?createPolicy=1&userid=Varuserid&policyid=Varpolicy";    
		
		}
					
		function changeWindowsHelloStatus()
		{
			//alert("calling me");
			window.location.href = "'.$_SERVER['PHP_SELF'].'?changeWindowsHelloStatus=1";    
		}
					
					
					
		</script>
</head>
<body>';
echo '<div class="titel">'.$pageTitle.'</div><br />';


$db = new db;
utility::addLog('erste log-Meldung erstellt');


//html Set Policy
echo '
<h2>Benutzer Policy Tabelle (PT_USER)</h2>';

echo '		<span>Policy erstellen:</span>';
utility::createSelect('WF_USER','USERID','FULLNAME');
//utility::createSelect('PT_USER','USERID','POLICY');
utility::createSelectPolicy('PT_USER','USERID','POLICY');

echo '
		<input id="createPolicy" type="button" class="rounded button" value="createPolicy" onclick="return createPolicy()" />
		';
//loads the benutzertabelle
print_r(utility::createTable('PT_USER'));


//html Benutzertabelle
echo '
<div id="Benutzertabelle (WF_USER)">
<div class="heading">Benutzertabelle</div><input id=getUsers value="Anzeige neu laden" />
				<form action="utility.php" method="post" > 
<input id=getUsersLabel value="getUser" disabled=disabled /><input id=getUsers value="" /><br />
<input id=getUsersLabel value="setPolicy" disabled=disabled /><input id=getUsers value="" /><br />
</form>
		
</div>
';



//loads the benutzertabelle
print_r(utility::createTable('WF_USER'));
//utility::addLog('WF_USER Table erstellt');


echo '  <div class="heading">Settingstabelle</div>
		<span>Windows Hello Status:</span>
		<input id="changeWindowsHelloStatus" type="button" class="rounded button" value="changeWindowsHelloStatus()" onclick="changeWindowsHelloStatus()" />

		<p id="demo"></p>
		
		';
//loads the benutzertabelle
print_r(utility::createTable('SETTINGS'));


//test.php
require_once('test.php');



//print LogTabelle
echo '<div id="logTable">';
utility::addLog('WF_USER Table anzeigen');
echo '<div class="heading">LOG</div>';
if($_SESSION['log']){
	echo '<table>';
	foreach($_SESSION['log'] as $log){
		echo '<tr><td>';
		echo $log;
		echo '</td></tr>';
	}
	echo '</table>';
}else{
	echo 'no log found!';
}
echo '</div>';


echo '
 </body>
</html>';

?>
