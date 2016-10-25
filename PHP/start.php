<html>
<head>
<title>Log-In to the FIVE Webflow</title>
</head>
<body>
<?php 


echo '<p><u><h1>LOGIN-Page</h1></p></u><br>'; 
echo '<p><h2>&#xDC;berpr&#xFC;fen der Systemumgebung:</h2></p>';

echo 'Betriebsystem: 
		<script>if((navigator.appVersion.indexOf("Windows NT 10.0")) > -1) document.write("Windows 10");</script><br><br>
		<div id="userAndLogin">Type in your username: <br><br> <input type="text" size="30" id="userNameInput"><br><br><br>
				<button id="loginButton" onclick="login()">Login</button></div>';

echo '<p id="para1">Browserversion:</p> <script> 
		if(window.navigator.userAgent.indexOf("Edge") > -1)
				document.write("Verwendeter Browser ist Microsoft Edge.<br>");

		else {
			document.write("Kein Edge, bitte Browser wechseln.<br>");
			var elem = document.getElementById("userAndLogin");
			elem.parentElement.removeChild(elem);
			var elem2 = document.getElementById("para1");
			elem2.parentElement.removeChild(elem2);		
		} </script><br>';


?> 

 </body>
</html>