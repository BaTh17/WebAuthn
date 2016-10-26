<html>
<head>
<title>admin.php</title>
</head>
<body>
<?php 
require_once('utility.php');

echo '<p>admin.php</p>'; 
echo '<p>Display and Change of policy and users</p>';


utility::dbconnect();



?>

<form action="utility.php" method="post" > 
<input id=getUsersLabel value="" disabled=disabled /><input id=getUsers value="" /></br>
<input id=getUsersLabel value="" disabled=disabled /><input id=getUsers value="" /></br>
</form>

 </body>
</html>