<?php 


/**
 * Here are all functions for the serverside, that are used in different files of the thesis project.
 * If you want to enable these functions, use require_once('utility.php'); in the first lines of your php
 * script
 * @author Marcel
 *
 */
class utility {

function dbconnect(){
//connect to mysql DB
$host="localhost";
$user="webflow";
$password="1234";
$database="thesis";
$connection = mysqli_connect($host,$user,$password,$database);

if ( $connection )
{
	echo 'Verbindung erfolgreich: ';
	print_r( $connection);
}
else
{
	die('keine Verbindung möglich: ' . mysqli_error());
}


if ($db_link->connect_error) {
	die("Connection failed: " . $connection->connect_error);
}

//set charset
mysqli_set_charset($connection, 'utf8');

$sql = "SELECT * FROM WF_USER";
mysqli_query($connection, $sql) or die('Error selecting table WF_USER.');


// Check connection
if ($db_link->connect_error) {
	die("Connection failed: " . $db_link->connect_error);
}

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
}

//get WF_USERs
/**
 * Get inforation about the given user
 */
function getWfUser($userInfo, $infoValue = 1){
	
	$usersSQL = "SELECT * FROM WF_USER WHERE NAME = '$userInfo'";
	$db = new $db;
	$rs = $db->executeSQL($usersSQL);
	if($rs){
		return $rs;
	}else{
		return false;
	}
}



//get Policy ( 0,1,2)

// create Policy (dropdown)


//get Keys from WF_USERS
//input ( string USERNAME)
// return True / false


// checkPassword (default)
//


//get Public KEy from USER (NAME)



}//end utility class