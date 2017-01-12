<?php 
/**
 * file for tests of most of the classes, depending on a correct and unchanged default installation of
 * the required databases.
 * To add neu checks, write them and add them to function 'callAllTests()'.
 * They will get executed when caling test.php
 * @author MT
 */
//many include errors on live version, dont show them
error_reporting(E_ALL & ~E_NOTICE);
require_once("util.php");
_plugin_utility::resetLog();


//generate titel html
$pageTitle = 'Test - Page';
echo '<!DOCTYPE html>
<head>
<title>'.$pageTitle.'</title>
		<link rel="stylesheet" href="../CSS/default.css" type="text/css">
<body>
<div class="titel" >'.$pageTitle.'</div>
<p>Run these tests after installation to check if everything was set up correctly.</p>
<p>As soon as you change any value after a fresh installation some checks can return a negative result because default values were expected.</p>
';

//call all tests
test::callAllTests();


/**
 * the test class should provide all test cases and functions to check if the installation 
 * was successful by testing most of the importent functions with
 * static values
 */
class test{


	/**
	 * Collection of all testcases. Will be executed on calling this page
	 * @param {void}
	 * @return {string} html echos
	 */
	function callAllTests()
	{
		_plugin_utility::printImportantSessionValues();
		test::test_checkUser();
		test::test_checkPW();
		test::test_getPolicyFromUser();
		test::test_credentials();
		test::test_hasKeys();
		test::test_getPolicy();
		test::testLocalDb();
		test::testSessionDb();
	}
	


	/**
	 * Returns the "it is wrong" html answer
	 * @param {void}
	 * @return {string}
	 */
	function htmlIsCorrect()
	{
		return '<span class="isCorrect"> Correct </span>';
	}
	
	/**
	 * returns the "it is wrong" html answer
	 * @param {void}
	 * @return {string}
	 */
	function htmlIsFalse()
	{
		return '<span class="isFalse"> False </span>';
	}
	
	/**
	 * echos a html line break
	 * @param {void}
	 * @return {string}
	 */	
	function htmlLineBreak()
	{
		echo "<br />";
	}
	
	function makeHtmlLogTitleStartCheck($text)
	{
		echo "<span class='logTitel'> Starting check: $text</span><br /> ";
	}

	
	/**
	 * tests the chechUser() function and puts out the result as html
	 * @param {void}
	 * @return {string} html echos
	 */
	function test_checkUser()
	{
		self::makeHtmlLogTitleStartCheck(__METHOD__);
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		$validNames = array('tscm','schf','hello');
		$invalidNames = array('nichtVorhanden','Hacker');
		$combinedList = array_merge($validNames,$invalidNames);
		foreach($combinedList as $name)
		{
			$result = _plugin_utility::checkUsername($name);

			if( in_array($name,$validNames) && $result){
				$resultAnswer = $htmlIsCorrect;
			}elseif(in_array($name,$invalidNames)  && !$result){
				$resultAnswer = $htmlIsCorrect;
			}else{
				$resultAnswer = $htmlIsFalse;
			}
			
			$resultText = $result ? 'true' : 'false';
			
			echo 'Check: checkUsername: '.$name.' with Result '.$resultText.' and this is:'.$resultAnswer.'<br />';
		}
		test::htmlLineBreak();
	}
	
	
	/**
	 * tests the checkPW() function and puts out the result as html
	 * @param {void}
	 * @return {string} html echos
	 */
	function test_checkPW()
	{
		/*
		eafe55b5a378e5265053bde745200d28be9783de tscm 123456
		ec61b400dcadd53b018f036485f001e9250faea0 schf 123456
		e063f15ca2fb176e3cb51f3d244d1fb51c288e69 hello 123456
		 */
		self::makeHtmlLogTitleStartCheck(__METHOD__);
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		//alle correct logins
		$validNames = array('tscm','schf','hello');
		$password = '123456';
		$invalidNames = array();
		$combinedList = array_merge($validNames,$invalidNames);
		foreach($combinedList as $name)
		{
			$result = _plugin_utility::checkPW($name,$password);

			if($result){
				$resultAnswer = $htmlIsCorrect;
			}else{
				$resultAnswer = $htmlIsFalse;
			}
			
			$resultText = $result ? 'true' : 'false';
			
			echo 'Check: checkPW: Name '.$name.' and PW '.$password.' with Result '.$resultText.' and this is:'.$resultAnswer.'<br />';
		}
		
		//alle wring logins or wrong pw
		$validNames = array('tscm','schf','hello');
		$password = '12a51';
		$invalidNames = array('nichtVorhanden','Hacker');
		$combinedList = array_merge($validNames,$invalidNames);
		foreach($combinedList as $name)
		{
			$result = _plugin_utility::checkPW($name,$password);

			if($result){
				$resultAnswer = $htmlIsFalse;
			}else{
				$resultAnswer = $htmlIsCorrect;
			}
			
			$resultText = $result ? 'true' : 'false';
			
			echo 'Check: checkPW: Name '.$name.' and PW '.$password.' with Result '.$resultText.' and this is:'.$resultAnswer.'<br />';
		}
		test::htmlLineBreak();
	}


	/**
	 * tests Function getUseridFromPtid() and getPolicyFromUser() from utility.php
	 * @param {void}
	 * @return {string} html echos
	 */
	function test_getPolicyFromUser()
	{
		self::makeHtmlLogTitleStartCheck(__METHOD__);
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		$validPolicies = array(1 => 1, 6 => 2, 4 => 0);
		$invalidPolicies = array(7 => 2, 8 => 0, 9 => 0);
		$combinedList = array(1 => 1, 6 => 2, 4 => 0,7 => 2, 8 => 0, 9 => 0); //dont merge or the key values will not be the same
		foreach($combinedList as $ptid => $policyid )
		{
			$userid = _plugin_utility::getUseridFromPtid($ptid,false);
			if(!$userid)$userid = 'Not Found';
			$result = _plugin_utility::getPolicyFromUser($userid);

			if( in_array($policyid,$validPolicies) && $policyid == $result['POLICY'] ){
				$resultFinal = $htmlIsCorrect;
			}elseif(in_array($policyid,$invalidPolicies)  && !$policyid == $result['POLICY']){
				$resultFinal = $htmlIsCorrect;
			}else{
				$resultFinal = $htmlIsFalse;
			}
				
			echo 'Check: getPolicyFromUser: '.$userid.' with Result '.$result['POLICY'].' and this is: '.$resultFinal.'<br />';
		}
		test::htmlLineBreak();
	}
	
	
	
	/**
	 * Tests function getPolicy()
	 */
	function test_getPolicy()
	{
		self::makeHtmlLogTitleStartCheck(__METHOD__);	
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		$answer1 = getPolicy("schf") === 0 ? $htmlIsCorrect : $htmlIsFalse;
		$answer2 = getPolicy("tscm") === 1 ? $htmlIsCorrect : $htmlIsFalse;
		$answer3 = getPolicy("hello") === 2 ? $htmlIsCorrect : $htmlIsFalse;
		
		//single tests indirect calls
		echo 'Check: getPolicy with schf, has to be 0:  '.getPolicy("schf").' '.$answer1.' <br />';
		echo 'Check: getPolicy with tscm, has to be 1:  '.getPolicy("tscm").' '.$answer2.'<br />';
		echo 'Check: getPolicy with hello, has to be 2:  '.getPolicy("hello").' '.$answer3.'<br />';
		test::htmlLineBreak();
	}
		

	/**
	 * tests saveCredentials() and getUseridFromUsername() and getCredentials gives output via getCredentials 
	 */
	function test_credentials()
	{
		self::makeHtmlLogTitleStartCheck(__METHOD__);
		
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		$username = 'schf';
		$id = '1231233123123';
		$pubKey = 'TEST-24533456453-4g5h5345h-54h45h-45-6456-TEST';
		$userid = _plugin_utility::getUseridFromUsername($username);
		echo 'Check: saveCredentials: $username = '.$username.' and  $id = '.$id.' and $pubKey = '.$pubKey.'  <br />';
		_plugin_utility::saveCredentials($username, $id, $pubKey);
		$rs = _plugin_utility::getCredentials($userid, $id, $pubKey);
		//INSERT INTO PUBLICKEYS (USERID,KEYVALUE) VALUES ('2', '23452345-346745856-673434');
		echo 'Result via  getCredentials:<br />';
		var_dump($rs[0]);
		$checkCredentialFound = $rs[0] ? $htmlIsCorrect : $htmlIsFalse ;
		echo '<br />only the first result is shown... but result is '.$checkCredentialFound;
		echo '<br />';
	
		_plugin_utility::deleteCredentials($userid, $id, $pubKey);
		$rs = _plugin_utility::getCredentials($userid, $id, $pubKey);

		if($rs){
			echo 'Still got some credentials after deleteCredentials , this is: '.$htmlIsFalse.'<br />';
		}else{
			echo 'Nothing found after deleteCredentials , this is: '.$htmlIsCorrect.'<br />';
		}
		test::htmlLineBreak();
	}
	
	/**
	 * Enter test function hasKeys()
	 */
	function test_hasKeys()
	{
		self::makeHtmlLogTitleStartCheck(__METHOD__);
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();

		$username = 'tscm';
		$id = '12312322223123';
		$pubKey = 'TEST-24533111113-4g5h5345h-54h45h-45-6456-TEST';
		$userid = _plugin_utility::getUseridFromUsername($username);
		_plugin_utility::deleteCredentials($userid);
		
		echo 'Check: hasKeys: $username = '.$username.' <br />';
		$check = _plugin_utility::hasKeys($username);
		if($check){
			echo 'Check: hasKeys at the start: $username = '.$username.' but got result $check = '.$check.'  , this is: '.$htmlIsFalse.'  <br />';
		}else{
			echo 'Check: hasKeys at the start: $username = '.$username.' got result $check = '.$check.'  , this is: '.$htmlIsCorrect.' <br />';
		}
		
		
		_plugin_utility::saveCredentials($username, $id, $pubKey);
		
		$check = _plugin_utility::hasKeys($username);
		if($check){
			echo 'Check: hasKeys after saveCredentials: $username = '.$username.' got result $check = '.$check.'  , this is: '.$htmlIsCorrect.' <br />';
		}else{
			echo 'Check: hasKeys after saveCredentials: $username = '.$username.' but got result $check = '.$check.'  , this is: '.$htmlIsFalse.'  <br />';
		}
		
		_plugin_utility::deleteCredentials($userid, $id, $pubKey);
		
		$check = _plugin_utility::hasKeys($username);
		if($check){
			echo 'Check: hasKeys after deleteCredentials: $username = '.$username.' but got result $check = '.$check.'  , this is: '.$htmlIsFalse.'  <br />';
		}else{
			echo 'Check: hasKeys after deleteCredentials: $username = '.$username.' got result $check = '.$check.'  , this is: '.$htmlIsCorrect.' <br />';
		}
		test::htmlLineBreak();
	}
	

	/**
	 * Test local database connection
	 */
	function testLocalDb()
	{
		self::makeHtmlLogTitleStartCheck(__METHOD__);
		
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
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
			_plugin_utility::addLog('Connection to database:  '.DATENBANK.' was successful.');
			print_r( $db_link);
		}
		else
		{
			if( $_SESSION['importWebflowClasses'] === true ){
				$resultAnswer = $htmlIsCorrect;
			}else{
				$resultAnswer = $htmlIsFalse;
			}
			echo('No local connection possible: ' . mysqli_error(). $resultAnswer.'<br />');
			test::htmlLineBreak();
			return;
		}
		
		//check connection
		if ($db_link->connect_error) {
			if( $_SESSION['importWebflowClasses'] === true ){
				$resultAnswer = $htmlIsCorrect;
			}else{
				$resultAnswer = $htmlIsFalse;
			}
			echo("Local Connection failed: " . $db_link->connect_error).$resultAnswer."<br />";
			test::htmlLineBreak();
			return;
		}
		
		$sql = "SELECT USERID, NAME, FULLNAME FROM WF_USER";
		$result = $db_link->query($sql);
		
		if ($result->num_rows > 0) {
			echo "List of local users:<br>";
			while($row = $result->fetch_assoc()) {
				echo "USERID: " . $row["USERID"]. " - NAME: " . $row["NAME"]. " - FULLNAME: " . $row["FULLNAME"]. "<br>";
			}
		} else {
			echo "Sorry, 0 results found on local database<br />";
		}
		$db_link->close();
		test::htmlLineBreak();
	}
	
	/**
	 * Test session/webflow database connection
	 * - depends on the configuration in utility php
	 */
	function testSessionDb()
	{
		self::makeHtmlLogTitleStartCheck(__METHOD__);
		
		$htmlIsCorrect = test::htmlIsCorrect();
		$htmlIsFalse = test::htmlIsFalse();
		
		$answer = $_SESSION['importWebflowClasses'] ? 'Yes' : 'No';
		echo " SESSION[importWebflowClasses] : ".$answer."<br />";

		$db = _plugin_utility::loadDb();
		$sql = "SELECT * FROM WF_USER";
		$rs = $db->executeSQL($sql,true);
		if($rs){
			if( $_SESSION['importWebflowClasses'] === true ){
				$resultAnswer = $htmlIsCorrect;
			}else{
				$resultAnswer = $htmlIsFalse;
			}
			echo('session/webflow database connection was build! That is: '.$resultAnswer.'<br />');
		}else{
			if( $_SESSION['importWebflowClasses'] === true ){
				$resultAnswer = $htmlIsFalse;
			}else{
				$resultAnswer = $htmlIsCorrect;
			}
			echo('no connection was possible for session/webflow database. That is: '.$resultAnswer.'<br />');
		}
		test::htmlLineBreak();
	}
	
	
}//end class test



echo '
</body>
</html>
';
