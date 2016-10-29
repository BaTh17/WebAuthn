<?php 
//includes

require_once('utility.php');
require_once('util.php');

// Session & Log
session_start();
$_SESSION['log'] = 'admin maske geladen';


//definitions
$pageTitle = 'admin.php';

echo '<html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
</head>
<body>';
echo '<p>admin.php</p>'; 
echo '<p>Display and Change of policy and users</p>';



$db = utility::dbconnect();

$_SESSION['log'][] = 'erstes log erstellt';

var_dump($_SESSION['log']);

?>

<form action="utility.php" method="post" > 
<input id=getUsersLabel value="getUser" disabled=disabled /><input id=getUsers value="" /></br>
<input id=setUser value="" disabled=disabled /><input id=getUsers value="" /></br>
<input id=getUsersLabel value="" disabled=disabled /><input id=getUsers value="" /></br>
</form>

<div>
<h1>Benutzertabelle</h1><input id=getUsers value="Anzeige neu laden" />
<?php 

createTable('WF_USER');

?>

</div>

 </body>
</html>