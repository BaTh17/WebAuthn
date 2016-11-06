<?php 

/**
 * file for tests of most of the classes, depending on a correct and unchanged default installation of
 * the required databases.
 * To add neu checks, write them and add them to function 'callAllTests()'.
 * They will get executed when caling test.php
 * @author MT
 */
error_reporting(E_ALL);
require_once('utility.php');
require_once('util.php');

if(!isset($_SESSION['log'])){
	session_start();
}


//generate titel html
$pageTitle = 'test.php';
echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
<body>
<h1>'.$pageTitle.'</h1>
		
';

//call all tests
test::callAllTests();


/**
 * the test class should provide all test cases and functions to check if the installation 
 * was successfull by testing most of the importent functions with
 * static values
 */
class test{


	/**
	 * Collection of all testcases. Will be executed on calling this page
	 * @param {void}
	 * @return {string} html echos
	 */
	function callAllTests(){
		test::test_checkUser();
		test::test_getPolicyFromUser();
		test::test_credentials();
	}

	/**
	 * Returns the "it is wrong" html answer
	 * @param {void}
	 * @return {string}
	 */
	function htmlIsCorrect(){
		return '<span style="color:green;font-weight:normal;">Correct</span>';
	}
	
	/**
	 * returns the "it is wrong" html answer
	 * @param {void}
	 * @return {string}
	 */
	function htmlIsFalse(){
		return '<span style="color:red;font-weight:bold;">False</span>';
	}

	
	/**
	 * tests the chechUser() function and puts out the result as html
	 * @param {void}
	 * @return {string} html echos
	 */
	function test_checkUser()
	{
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		$validNames = array('tscm','schf','hello','nichtVorhanden');
		$invalidNames = array('nichtVorhanden','Hacker');
		$combinedList = array_merge($validNames,$invalidNames);
		foreach($combinedList as $name)
		{
			$result = utility::checkUsername($name);
			
			if( in_array($name,$validNames) && $result){
				$resultIsCorrect = $htmlIsCorrect;
			}elseif(in_array($name,$invalidNames)  && !$result){
				$resultIsCorrect = $htmlIsCorrect;
			}else{
				$resultIsCorrect = $htmlIsFalse;
			}
			
			echo 'Check: checkUsername: '.$name.' with Result '.$result.' and this is: '.$resultIsCorrect.'<br />';
		}
	}


	/**
	 * tests Function getUseridFromPtid() and getPolicyFromUser() from utility.php
	 * @param {void}
	 * @return {string} html echos
	 */
	function test_getPolicyFromUser(){
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		$validPolicies = array(1 => 1, 6 => 2, 4 => 0);
		$invalidPolicies = array(7 => 2, 8 => 0, 9 => 0);
		$combinedList = array(1 => 1, 6 => 2, 4 => 0 ,7 => 2, 8 => 0, 9 => 0);
		foreach($combinedList as $ptid => $policyid )
		{
			$userid = utility::getUseridFromPtid($ptid,false);
			$result = utility::getPolicyFromUser($userid);
		
		if( in_array($policyid,$validPolicies) && $policyid == $result['POLICY'] ){
				$resultFinal = $htmlIsCorrect;
			}elseif(in_array($policyid,$invalidPolicies)  && !$policyid == $result['POLICY']){
				$resultFinal = $htmlIsCorrect;
			}else{
				$resultFinal = $htmlIsFalse;
			}
				
			echo 'Check: getPolicyFromUser: '.$userid.' with Result '.$result['POLICY'].' and this is: '.$resultFinal.'<br />';
		}
	}

	
	
	function test_getUseridFromUsername()
	{
		//TODO
	}
	
	
	/**
	 * Test for getWfUser() and checkPW()
	 */
	function test_checkPW(){
		//TODO
		//INSERT INTO PUBLICKEYS (USERID,KEYVALUE) VALUES ('2', '23452345-346745856-673434');
		//tscm und schf check with test as pw
	}
	
	
	/**
	 * tests saveCredentials() and getUseridFromUsername() and getCredentials gives output via getCredentials 
	 */
	function test_credentials()
	{
		echo 'Start with Check: '.__METHOD__.'<br />';
		
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		$username = 'schf';
		$id = '123123123123';
		$pubKey = 'TEST-2453456453-4g5h5345h-54h45h-45-6456-TEST';
		
		$userid = utility::getUseridFromUsername($username);
		
		echo 'Check: saveCredentials: $username = '.$username.' and  $id = '.$id.' and $pubKey = '.$pubKey.'  <br />';
		//echo 'Check: test_credentials:  $username: '.$username.' with Result '.$result['POLICY'].' and this is: '.$resultFinal.'<br />';
	//	var_dump(__METHOD__.__LINE__);
		utility::saveCredentials($username, $id, $pubKey);
//var_dump(__METHOD__.__LINE__);
		$rs = utility::getCredentials($userid, $id, $pubKey);
		//INSERT INTO PUBLICKEYS (USERID,KEYVALUE) VALUES ('2', '23452345-346745856-673434');
		echo 'Result via  getCredentials:<br />';
		//print_r($rs[0]);
		var_dump($rs[0]);
		echo '<br />nur erstes resultat wird gezeigt...<br />';
		echo '<br />';
		//var_dump(__METHOD__.__LINE__);
		
		utility::deleteCredentials($userid, $id, $pubKey);
		//var_dump(__METHOD__.__LINE__);
		
		$rs = utility::getCredentials($userid, $id, $pubKey);
		if($rs){
			echo 'Still got some credentials after deleteCredentials , this is: '.$htmlIsFalse.'<br />';
		}else{
			echo 'Nothing found after deleteCredentials , this is: '.$htmlIsCorrect.'<br />';
		}
		
	}




}

// Connection data for local testing
define ( 'HOST',      'localhost' );
define ( 'BENUTZER',  'webflow' );
define ( 'KENNWORT',  '1234' );
define ( 'DATENBANK', 'thesis' );

$db_link = mysqli_connect (HOST,
		BENUTZER,
		KENNWORT,
		DATENBANK);
mysqli_set_charset($db_link, 'utf8');

if ( $db_link )
{
	utility::addLog('Verbindung mit Datenbank '.DATENBANK.' erfolgreich.');
	print_r( $db_link);
}
else
{
	// no connection possible
	die('keine Verbindung möglich: ' . mysqli_error());
}

//check connection
if ($db_link->connect_error) {
	die("Connection failed: " . $db_link->connect_error);
}

$sql = "SELECT USERID, NAME, FULLNAME FROM WF_USER";
$result = $db_link->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo "USERID: " . $row["USERID"]. " - NAME: " . $row["NAME"]. " - FULLNAME: " . $row["FULLNAME"]. "<br>";
	}
} else {
	echo "Sorry, 0 results found";
}
$db_link->close();




echo '
</body>
</html>
';