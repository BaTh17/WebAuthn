<html>
<head>
<title>admin.php</title>
</head>
<body>
<?php 

class db {
	
	
	
}
echo '<p>admin.php</p>'; 
echo '<p>Display and Change of policy and users</p>';

//connect to mysql DB
$host="localhost";
$user="webflow";
$password="1234";
$database="thesis";
$connection = mysql_connect($host,$user,$password);
if(!$connection) {
	echo '<h1> MySQL DB '.$database.' connected</h1>';
} else {
	echo '<h1>MySQL Server '.$database.' is not connected</h1>';
}

$sql = "SELECT * FROM WF_USER";
mysql_query($connection, $sql) or die('Error selecting table WF_USER.');


/*
$host="localhost";
$user="root";
$password="";
$con=mysql_connect($host,$user,$password);
if(!$con) {
	echo '<h1>Connected to MySQL</h1>';
	//if connected then Select Database.
	$db=mysql_select_db("YOUR_DATABASE_NAME",$con);
	$query=mysql_query("YOUR_MYSQL_QUERY",$db);
}
else {
	echo '<h1>MySQL Server is not connected</h1>';
}
*/


//get WF_USERs
$usersSQL = "SELECT * FROM WF_USER";


//get Policy


//get Keys from WF_USERS


//check Password (default)


//get Public KEy from USER (NAME)




?>
 </body>
</html>