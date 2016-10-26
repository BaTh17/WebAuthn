<?php 
error_reporting(E_ALL);

// Verbindungsdaten
define ( 'HOST',      'localhost' );
define ( 'BENUTZER',  'webflow' );
define ( 'KENNWORT',  '1234' );
define ( 'DATENBANK', 'thesis' );

$db_link = mysqli_connect (HOST,
		BENUTZER,
		KENNWORT,
		DATENBANK);
mysqli_set_charset($db_link, 'utf8');

if ( $db_link )
{
	echo 'Verbindung erfolgreich: ';
	print_r( $db_link);
}
else
{
	// hier sollte dann später dem Programmierer eine
	// E-Mail mit dem Problem zukommen gelassen werden
	die('keine Verbindung möglich: ' . mysqli_error());
}

// Check connection
if ($db_link->connect_error) {
	die("Connection failed: " . $db_link->connect_error);
}

$sql = "SELECT USERID, NAME, FULLNAME FROM WF_USER";
$result = $db_link->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo "USERID: " . $row["USERID"]. " - NAME: " . $row["NAME"]. " - FULLNAME: " . $row["FULLNAME"]. "<br>";
	}
} else {
	echo "Sorry, 0 results found";
}
$db_link->close();