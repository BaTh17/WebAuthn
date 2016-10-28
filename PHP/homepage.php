<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>

 <?php 
 session_start();
 
 $username = $_SESSION['username'];
 echo 'LOGIN WAS SUCCESSFUL WITH USER: '.$username; 
 
 ?> 
 </body>
</html>