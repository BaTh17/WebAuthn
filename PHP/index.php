<?php 
$pageTitle = 'Index - Page';
echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>

		</head>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">

		
		<body>
		';

// <script language="javascript" type="text/javascript">


echo '<div>'.$pageTitle.'</div>';
echo '<p>Hello User! Please use the button below to go to the project start page.</p>'; 
echo '
				<button type="button" value="Back to Welcome" class="rounded" id="btnBackToWelcome"
onClick="document.location.href=\'welcome.php\'" />
		';




echo ' </body>
</html>';

?>