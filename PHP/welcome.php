<html>
<head>
<title>Log-In to the FIVE Webflow</title>
</head>
<body>
<?php 

echo "<script src='../Client/utils.js'></script> ";


echo '<p><u><h1>LOGIN-Page</h1></p></u><br>'; 
echo '<p><h2>&#xDC;berpr&#xFC;fen der Systemumgebung:</h2></p>';

echo 'Betriebsystem: 
		<script>
			if((navigator.appVersion.indexOf("Windows NT 10.0")) > -1) document.write("Windows 10");
		</script>
		<br>';

echo '	<div id="userAndLogin"><br>Type in your username: <br><br> <input type="text" size="30" id="userNameInput"><br><br><br>
				<button id="loginButton" onclick="login()">Login</button></div>';

echo "<p id='browserInfo'></p>";

echo "
	<script>
		if(window.navigator.userAgent.indexOf('Edge') > -1)
			document.write('(Verwendeter Browser ist Microsoft Edge.)');

		else {
			document.getElementById('browserInfo').innerHTML = 'Kein Edge, bitte Browser wechseln.<br>';
			var elem = document.getElementById('userAndLogin');
			elem.parentElement.removeChild(elem);

		} 
	</script>
						<br><p id='status'><p>";

		
echo "<script>
	function login(){
					
	var username = document.getElementById('userNameInput').value;
	var params = 'username='+username;
	postAjaxCall(params, '../PHP/usercheck.php');
 }


	function checkPW(){

	var pw = document.getElementById('pwInput').value;
	var params = 'username='+username;
	postAjaxCall(params, '../PHP/usercheck.php');
				}
	</script";




?> 

 </body>
</html>