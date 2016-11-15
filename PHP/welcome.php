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
//id="body-login"

$pageTitle = 'Welcome - Page';
echo '<!DOCTYPE html>
<head>
<title>Welcome to FIVE Webflow</title>
</head>
<body >
		';

echo "<script src='../Client/utils.js'></script>";
echo "<script src='../Client/webauthn.js'></script>";
echo '<link rel="stylesheet" href="../CSS/default.css" type="text/css">';


echo '<p class="titel" >'.$pageTitle.'</p>'; 
echo '<div class="centerBox">';
echo '<p class="heading" >Checking your operating system: </p>'; 
//&#xDC;berpr&#xFC;fen der Systemumgebung:
// kleines ue = &#xFC;  , grosses UE = &#xDC;

echo '<span class="label">Operating System: </span>
		<script>
			if((navigator.appVersion.indexOf("Windows NT 10.0")) > -1) document.write("Windows 10");
		</script>
		<br />';
echo "<p id='browserInfo'></p>";

echo "
	<script>
		if(window.navigator.userAgent.indexOf('Edge') > -1)
			document.write('You use Microsoft Edge, Windows Hello login is possible.');

		else {
			document.getElementById('browserInfo').innerHTML = 'You do not use the Microsoft Edge Browser. Please use it to access the Windows Hello features.<br>';
			var elem = document.getElementById('userAndLogin');
			elem.parentElement.removeChild(elem);

		}
	</script>
		<br />
						";
// Verwendeter Browser ist Microsoft Edge.
// Kein Edge, bitte Browser wechseln.

echo '	
		<img class="logo" alt="" src="../CSS/logo.png" >
		<br />
		<img class="icon" alt="" src="../CSS/avatar.png" >
		Username: 
		<input type="text" id="userNameInput" class="rounded" />
		<br />
		<br /><div><div class="label">Status message:</div><div id="status" class="message"></div></div>
		<br />
		<button id="loginButton" onclick="login()" class="rounded"><span><img class="" alt="" src="../CSS/Button_White_Check.png" ></span> Login</button>
		';
//<span><img class="logo" alt="" src="../CSS/Button_White_Check.png" ></span>


echo '</div>';

echo '
</body>
</html>';

?>