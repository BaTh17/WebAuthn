<?php 

require_once('utility.php');

$pageTitle = 'Welcome - Page';
echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>';
echo "<script src='../Client/utils.js'></script>";
echo "<script src='../Client/webauthn.js'></script>";
echo '<link rel="stylesheet" href="../CSS/default.css" type="text/css">';
echo '</head>
<body>';


echo '<div class="titel" >'.$pageTitle.'</div>'; 
echo '<div class="centerBox">';


echo '<p class="heading topPadding" >Checking your operating system: </p>'; 
//redirection if Windows Hello is not active
$checkHelloIsActive = _plugin_utility::getWindowsHelloStatus();
if(!$checkHelloIsActive){
	$delayInSeconds = 5;
	header( "refresh:$delayInSeconds;url=".$_SESSION['redirectToLogin']."" );
	echo '<div class="label" style="width:600px;">Windows Hello Login is deactivated on this server. Redirect to login page in '.$delayInSeconds.' seconds...</div><br />';	
}

echo '<table class="systemInfoTable systemInfoLayout" >';
echo '<tr class="systemInfoTable" style="width:180px;"><td class="systemInfoTable systemInfoLabel label">Operating System: </td>
<td class="systemInfoTable systemInfoMessage">
		<script>
			if((navigator.appVersion.indexOf("Windows NT 10.0")) > -1) document.write("Windows 10");
		</script>
		</td>';
echo "<tr class='systemInfoTable'>
<td class='systemInfoTable systemInfoLabel label'>Web Browser:</td>
		
		
";

echo "
	<td id='browserInfo' class='systemInfoTable systemInfoMessage'>	
	<script>
		if(window.navigator.userAgent.indexOf('Edge') > -1){
			document.write('Microsoft Edge: Windows Hello login is possible.');
		}
		else {
			document.getElementById('browserInfo').innerHTML = 'You do not use the Microsoft Edge Browser. Please use it to access the Windows Hello features.<br />';
			var elem = document.getElementById('userAndLogin');
			elem.parentElement.removeChild(elem);
		}
		
		
	</script>

		<br />
	";

echo '</td></table>';

// Wrong browser, need microsoft edge
echo '	
		<img class="logo" alt="" src="../CSS/logo.png" >
		<br />
		<img class="icon" alt="" src="../CSS/user_white.png" >
		Username: <input type="text" id="userNameInput" class="rounded defaultInput" onKeydown="Javascript: if (event.keyCode == 13) login()"  autofocus />
		<br />
		<br /><div><div class="label">Status message: </div><div id="status" class="message"></div></div>
		<br />
		<button id="loginButton" onclick="login()" class="rounded"><span><img class="" alt="" src="../CSS/Button_White_Check.png" ></span> Login</button>
		';

echo '</div>';

echo '
</body>
</html>';

?>
