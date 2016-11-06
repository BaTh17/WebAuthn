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
echo '<html>
<head>
<title>Log-In to the FIVE Webflow</title>
</head>
<body >
		';
//var_Dump($_SERVER['PHP_SELF']);

echo '
		
		<link rel="stylesheet" href="Five_Login-Dateien/styles.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/header.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/mobile.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/icons.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/fonts.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/apps.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/fixes.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/multiselect.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/jquery-ui.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/jquery-ui-fixes.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/jquery-tipsy.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/jquery.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/share.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/versions.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/style.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/mediaelementplayer.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/colorbox.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/firstrunwizard.css" media="screen">
					<link rel="stylesheet" href="Five_Login-Dateien/slideshow.css" media="screen">
							<script src="Five_Login-Dateien/oc.js"></script>
					<script src="Five_Login-Dateien/jquery_002.js"></script>
					<script src="Five_Login-Dateien/jquery-migrate.js"></script>
					<script src="Five_Login-Dateien/jquery-ui.js"></script>
					<script src="Five_Login-Dateien/underscore.js"></script>
					<script src="Five_Login-Dateien/moment-with-locales.js"></script>
					<script src="Five_Login-Dateien/handlebars.js"></script>
					<script src="Five_Login-Dateien/md5.js"></script>
					<script src="Five_Login-Dateien/placeholders.js"></script>
					<script src="Five_Login-Dateien/jquery-tipsy.js"></script>
					<script src="Five_Login-Dateien/compatibility.js"></script>
					<script src="Five_Login-Dateien/jquery.js"></script>
					<script src="Five_Login-Dateien/oc-dialogs.js"></script>
					<script src="Five_Login-Dateien/js.js"></script>
					<script src="Five_Login-Dateien/l10n.js"></script>
					<script src="Five_Login-Dateien/de.js"></script>
					<script src="Five_Login-Dateien/octemplate.js"></script>
					<script src="Five_Login-Dateien/eventsource.js"></script>
					<script src="Five_Login-Dateien/config.js"></script>
					<script src="Five_Login-Dateien/search.js"></script>
					<script src="Five_Login-Dateien/oc-requesttoken.js"></script>
					<script src="Five_Login-Dateien/apps.js"></script>
					<script src="Five_Login-Dateien/snap.js"></script>
					<script src="Five_Login-Dateien/placeholder.js"></script>
					<script src="Five_Login-Dateien/jquery_003.js"></script>
					<script src="Five_Login-Dateien/avatar.js"></script>
					<script src="Five_Login-Dateien/share.js"></script>
					<script src="Five_Login-Dateien/de_002.js"></script>
					<script src="Five_Login-Dateien/share_002.js"></script>
					<script src="Five_Login-Dateien/external.js"></script>
					<script src="Five_Login-Dateien/de_003.js"></script>
					<script src="Five_Login-Dateien/versions.js"></script>
					<script src="Five_Login-Dateien/previewplugin.js"></script>
					<script src="Five_Login-Dateien/viewer.js"></script>
					<script src="Five_Login-Dateien/de_005.js"></script>
					<script src="Five_Login-Dateien/jquery_004.js"></script>
					<script src="Five_Login-Dateien/firstrunwizard.js"></script>
					<script src="Five_Login-Dateien/de_004.js"></script>
					<script src="Five_Login-Dateien/jquery_005.js"></script>
					<script src="Five_Login-Dateien/slideshow.js"></script>
					<script src="Five_Login-Dateien/public.js"></script>
					<script src="Five_Login-Dateien/jstz.js"></script>
					<script src="Five_Login-Dateien/visitortimezone.js"></script>
					<script src="Five_Login-Dateien/lostpassword.js"></script>
		
		
		';

echo "<script src='../Client/utils.js'></script>";
echo "<script src='../Client/webauthn.js'></script>";
echo ' <link rel="stylesheet" href="../CSS/default.css" type="text/css"> ';


echo '<p><h1>LOGIN-Page</h1></p><br>'; 
echo '<p><h2>&#xDC;berpr&#xFC;fen der Systemumgebung:</h2></p>';

echo 'Betriebsystem: 
		<script>
			if((navigator.appVersion.indexOf("Windows NT 10.0")) > -1) document.write("Windows 10");
		</script>
		<br>';

echo '	<div id="userAndLogin"><br>Type in your username: <br />
		<br />
		Benutzername: <input type="text" size="30" id="userNameInput">
		<img class="svg" alt="" src="Five_Login-Dateien/user.svg">
		<br />
		<br />
		<br />
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