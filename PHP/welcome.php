<?php 

//redirection if Windows Hello is not active
require_once('utility.php');
$checkHelloIsActive = utility::getWindowsHelloStatus();
//var_dump($checkHelloIsActive);
if(!$checkHelloIsActive){
//header("Location: http://localhost/phpmyadmin/");

$path_parts = pathinfo($_SERVER['PHP_SELF']);
$redirectUrl = $path_parts['dirname'].'/originalWebflowStartPage.php';

header("Location: http://localhost$redirectUrl");

}

echo '<html>
<head>
<title>Log-In to the FIVE Webflow</title>
</head>
<body>
		';
//var_Dump($_SERVER['PHP_SELF']);



echo "<script src='../Client/utils.js'></script>";
echo "<script src='../Client/webauthn.js'></script>";

		
echo '<p><h1>LOGIN-Page</h1></p><br>'; 
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
						<br><div id='status'></div>";

		



?> 

</body>
</html>