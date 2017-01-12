<?php 
$pageTitle = 'Index - Page';
echo '<!DOCTYPE html>
<head >
 <title>'.$pageTitle.'</title>
 <link rel="stylesheet" href="../CSS/default.css" type="text/css">
</head>

<body> 
';
echo '<div class="titel" >'.$pageTitle.'</div>'; 
echo '<p>Hello user! Thank you for using our WebAuth prototyp.</p>';
echo '<p>Please use the button below to go to the project start page.</p>'; 
echo '
				<button type="button" value="Back to Welcome" class="rounded" id="btnBackToWelcome"
onClick="document.location.href=\'welcome.php\'" >Go to welcome screen</button>

';
echo '<div ></div>';
echo ' </body>
</html>';

?>
