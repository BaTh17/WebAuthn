<?php 
/**
 * the administratior mask to view and regulate the policies
 * @autor MT
 */
session_start();
require_once('util.php');
require_once('utility.php');

if($_REQUEST['resetLog']=1){
	_plugin_utility::resetLog();
}

// Session & Log
_plugin_utility::addLog('Beginne mit Laden der Admin maske');

//definitions
$pageTitle = 'Admin - Page';

echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
		
		<script type="text/javascript">
		
		function createPolicy() 
		{
			var Varuserid = document.getElementById("select_WF_USER_USERID_FULLNAME").value;
			var Varpolicy = document.getElementById("select_PT_USER_PTID_POLICY").value;
			var Varfunction = "createPolicy";
			//alert(Varuserid);
			//alert(Varpolicy);
			//alert(" " + Varuserid + " " + Varpolicy +" ");
			window.location.href = "'.$_SERVER['PHP_SELF'].'?" + Varfunction + "=1&userid=" + Varuserid + "&policyid=" + Varpolicy +"";    
		}
		
		function deletePolicy()
		{
			var Varptid = document.getElementById("select_PT_USER_PTID_PTID").value;
			var Varfunction = "deletePolicy";
			window.location.href = "'.$_SERVER['PHP_SELF'].'?" + Varfunction + "=1&ptid=" + Varptid + "";    
		}
		
		function deletePublicKey()
		{
			var Varkeyid = document.getElementById("select_PUBLICKEYS_KEYID_KEYID").value;
			var Varfunction = "deletePublicKey";
			window.location.href = "'.$_SERVER['PHP_SELF'].'?" + Varfunction + "=1&keyid=" + Varkeyid + "";    
		}
			
		function changeWindowsHelloStatus()
		{
			window.location.href = "'.$_SERVER['PHP_SELF'].'?changeWindowsHelloStatus=1";    
		}

		function reloadPage()
		{
			window.location.href = "'.$_SERVER['PHP_SELF'].'";    
		}
					
		</script>
</head>
<body>';
echo '<div class="titel">'.$pageTitle.' <input id="reloadPage" type="button" class="rounded button" value="reloadPage" onclick="return reloadPage()" /></div><br />';

_plugin_utility::addLog('Admin mask called');

////////////////////////////
// html Policy Table
////////////////////////////
echo '
<div class="heading">User Policy</div>';

echo '		<span>Create Policy: </span>';
echo _plugin_utility::createSelect('WF_USER','USERID','FULLNAME');

echo _plugin_utility::createSelectPolicy('PT_USER','PTID','POLICY');

echo '
		<input id="createPolicy" type="button" class="rounded button" value="createPolicy" onclick="return createPolicy()" /><br />
		';

echo '		<span>Delete Policy, select PTID: </span>';
echo _plugin_utility::createSelect('PT_USER','PTID','PTID');
echo '
		<input id="deletePolicy" type="button" class="rounded button" value="deletePolicy" onclick="return deletePolicy()" /><br />
		';
//loads the benutzertabelle
$result = _plugin_utility::createTable('PT_USER');
if($result){
	print_r($result);
}else{
	echo '  <div class="label"> No entries found!</div>';
}
echo '<br />';




////////////////////////////
// html Settings table
////////////////////////////
echo '  <div class="heading">Settings</div>
		<span>Windows Hello Status:</span>
		<input id="changeWindowsHelloStatus" type="button" class="rounded button" value="changeWindowsHelloStatus()" onclick="changeWindowsHelloStatus()" />

		<p id="demo"></p>
		
		';
$result = _plugin_utility::createTable('SETTINGS|WINDOWS_HELLO_STATUS');
if($result){
	print_r($result);
}else{
	echo '  <div class="label"> No entries found!</div>';
}
echo '<br />';

////////////////////////////
// html PublicKey Table
////////////////////////////
echo '
<div id="PublicKey">
<div class="heading">PublicKey</div>
</div>
';
echo '<span>Delete PublicKey, select KEYID: </span>';
echo _plugin_utility::createSelect('PUBLICKEYS','KEYID','KEYID');
echo '
		<input id="deletePublicKey" type="button" class="rounded button" value="deletePublicKey" onclick="return deletePublicKey()" /><br />
		';
//loads the User Table
$result = _plugin_utility::createTable('PUBLICKEYS|KEYID,USERID,KEYVALUE,KEYIDENTIFIER,METADATA1,HOSTNAME,CREATEDTIME,CHANGEDTIME,AKTIV');
if($result){
	print_r($result);
}else{
	echo '  <div class="label"> No entries found!</div>';
}
echo '<br />';

////////////////////////////
// html User Table
////////////////////////////
echo '
<div id="Users">
<div class="heading">Application Users</div>
</div>
';

//loads the User Table
$result = _plugin_utility::createTable('WF_USER|USERID,NAME,FULLNAME,USERPASSWORD,AKTIV');
if($result){
	print_r($result);
}else{
	echo '  <div class="label"> No entries found!</div>';
}
echo '<br />';


////////////////////////////
// html Log Table
////////////////////////////
echo '<div id="logTable">';
_plugin_utility::addLog('WF_USER Table anzeigen');
echo '<div class="heading">Log Messages</div>';
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
echo '<br />';


echo '
 </body>
</html>';

?>
