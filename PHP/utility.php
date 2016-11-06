<?php

/**
* Here are all functions for the serverside, that are used in different files of the thesis project.
* If you want to enable these functions, use require_once('utility.php'); in the first lines of your php
* script
* @autor MT
*/
class utility {
	
	/**
	 * react to all get- or post-information
	 * @param unknown $response
	 */
	function catchResponse($response)
	{
		//TODO
		//print_r(__METHOD__.__LINE__);
		//print_r($response);
		//Policy Case
		if($response['createPolicy'] == 1 && isset($response['userid']) && isset($response['policyid']) ){
			utility::changePolicy($response['userid'],$response['policyid']);
		}
		
		if($response['changeWindowsHelloStatus'] == 1 ){
			utility::addLog('called  changeWindowsHelloStatus');
		}
	}
	
	
	/**
	 * Change policy or create a new one if none is set
	 * @param {int} $userid
	 * @param {int} $policy
	 */
	function changePolicy($userid,$policy)
	{
		utility::addLog('change Policy, Userid = '.$userid.' und Policy = '.$policy.'');
		$db = new db();
		$isActive = -1;
		
		//hat es bereits einen Eintrag? dann updaten
		$sql = "SELECT * FROM PT_USER WHERE USERID = $userid";
		$rs = $db->executeSQL($sql,true);
		//TODO
		//print_r(__METHOD__.__LINE__);
		//print_r($rs);
		
		if($rs){
			utility::addLog('Update Policy');
			$sql = "UPDATE PT_USER SET POLICY') VALUES ($userid,'$policy',$isActive)";
		}else{
			utility::addLog('insert Policy');
			$sql = "INSERT INTO PT_USER ('USERID','POLICY','AKTIV') VALUES ($userid,'$policy',$isActive)";
		}
		
		$rs = $db->executeSQL($sql,true);
		$htmlOutput = '';
		//Build HTML Table around result
		//print_r($rs);
	}

	
	/**
	 * Get a policy record as array from the given userid
	 * @param {int} $userid
	 * @param {boolean} $wholeEntry, if false, only return the policyid value
	 * @return  {array} $rs
	 */
	function getPolicyFromUser($userid,$wholeEntry = true, $isUsername = false)
	{
		if($isUsername){
			$userid = utility::getUseridFromUsername($userid);
		}
		
		utility::addLog('Aufruf getPolicyFromUser() mit USERID = '.$userid.'');
		$db = new db();
		$isActive = -1;
		$sql = "SELECT * FROM PT_USER WHERE USERID = $userid AND AKTIV = $isActive";
		$rs = $db->executeSQL($sql,true);
		
		if($rs){
			if($wholeEntry){
				return $rs[0];
			}else{
				return $rs[0]['POLICYID'];
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Get a userid as array from the given ptid
	 * @param {int} ptid
	 * @param {boolean} $wholeEntry, if false, only return the userid value
	 * @return  {array} $rs
	 */
	function getUseridFromPtid($ptid,$wholeEntry = true)
	{
		utility::addLog('Aufruf getUseridFromPtid() mit PTID = '.$ptid.'');
		$db = new db();
		$isActive = -1;
		$sql = "SELECT * FROM PT_USER WHERE PTID = $ptid AND AKTIV = $isActive";
		$rs = $db->executeSQL($sql,true);
	
		if($rs){
			if($wholeEntry){
				return $rs[0];
			}else{
				return $rs[0]['USERID'];
			}
		}else{
			return false;
		}
	}
	
	
	/**
	 * Enters a log line into log, it is a session array
	 * @param  {string}
	 * @retrun void
	 */
	function addLog($log)
	{
		if( $_SESSION['log'] OR !is_array($_SESSION['log'])){
			utility::resetLog();
		}
		//print_r(debug_backtrace()['1']['function']);
		//print_r(debug_backtrace());
		$a = debug_backtrace();
 		if(isset($a['1'])){
 			$b = $a['1'];
 		}else{
 			$b = $a['0'];
 		}
		//print_r($b);
		$lastSign = '\\';
		$c = substr(strrchr ( $b['file'],$lastSign),1);
		
		
		//print_r($c);
		array_push($_SESSION['log'],$c.' : '.$log);
	}
	
	/**
	 * reset log
	 * @param {void}
	 * @return {void}
	 */
	function resetLog()
	{
		if(!isset($_SESSION['log'])){
			session_start();
		}
		$_SESSION['log']=array();
	}


	/**
	 * Get inforation about the given username
	 * @param {string} $userInfo
	 * @param {int} $infoValue
	 * @return {array|boolean}
	 */
	function getWfUser($username, $infoValue = 1)
	{
		$usersSQL = "SELECT * FROM WF_USER WHERE NAME = '$username'";
		$db = new db;
		$rs = $db->executeSQL($usersSQL);
		if($rs){
			return $rs;
		}else{
			return false;
		}
	}
	
	/**
	 * TODO TESTEN
	 * Get inforation about the given user
	 */
	function getUseridFromUsername($username)
	{
		$usersSQL = "SELECT USERID FROM WF_USER WHERE NAME = '$username'";
		$db = new db;
		$rs = $db->executeSQL($usersSQL);
		if($rs){
			return $rs[0]['USERID'];
		}else{
			return false;
		}
	}

	




	//get Keys from WF_USERS
	//input ( string USERNAME)
	// return True / false


	
	/**
	 * check if 
	 *  - the user exists
	 *  - is activ
	 * boolean
	 */
	function checkUsername($username)
	{
		$isActive = -1;
		$sql = "SELECT * FROM WF_USER WHERE NAME = '$username' AND AKTIV = $isActive";
		$db = new db;
		$rs = $db->executeSQL($sql);
		if($rs){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * check the pw of the user
	 * @param {string} $username
	 * @param {string} $password
	 * @return boolean
	 */
	function checkPW($username, $password)
	{
		$userEntry = utility::getWfUser($username);
		
		if($userEntry){
			if($userEntry['0']['USERPASSWORD'] == $password){
				return true;
			}
			return false;
		}else{
			return false;
		}
	}
	
	
	/**
	 * Löscht eine Policy aufgrund des Benutzernamens
	 * @param int
	 * @return void
	 */
	function deletePolicy($username) {
	
		//TODO
	
	}

	/**
	 * Check, if the given username has policy keys already saved in PUBLICKEYS table
	 * TSCM: 20161016 renamed to hasKeys(), because that is what the function does
	 * @param {string} $username
	 * @return {boolean}
	 */
	function hasKeys($username) {
		
		//get userid
		$userid = utility::getUseridFromUsername($username);
	//print_r($userid);
		//check userid
		$rs = utility::getCredentials($userid);
	//print_r($rs);
		if($rs){
			if($rs[0]){
				return true;
			}else{
				return false;
			}
		}
		return false;
		
		//testvalues
// 		if($username=="schf")
// 			return true;
// 		if($username=="tscm")
// 			return true;
// 		if($username=="hello")
// 			return true;
// 		else
// 			return false;
	}
	
	
	
	
	/**
	 * Save the credatial information from the client in the database
	 * @return: boolean
	 * Es werden die vom Client übertragenen Credentials in der DB gespeichert
	 * USERID KEYVALUE KEYIDENTIFIER von Tabelle PUBLICKEYS
	 */
	function saveCredentials($username, $id, $pubKey)
	{
		utility::addLog('save credentials, Username = '.$username.' und KEYIDENTIFIER = '.$id.' und $KEYVALUE = '.$pubKey.'');
		
		//Username zu USERID wandeln
		$userid = utility::getUseridFromUsername($username);
		
		$db = new db();
		$isActive = -1;
		$timeNow = time();

		//TODO
		//print_r(__METHOD__.__LINE__);
		//print_r($rs);
		
// 		if($rs){
// 			utility::addLog('This Credential exists already! Abort.');
// 			$sql = "UPDATE PUBLICKEYS SET USERID = $userid , KEYVALUE =         ') VALUES ($userid,'$policy',$isActive)";
// 		}else{
			utility::addLog('New Credential, insert it');
			$sql = "INSERT INTO PUBLICKEYS (USERID,KEYVALUE,KEYIDENTIFIER, AKTIV,CREATEDTIME,CHANGEDTIME) VALUES ($userid,'$pubKey','$id',$isActive,$timeNow,$timeNow)";
			//INSERT INTO PUBLICKEYS (USERID,KEYVALUE) VALUES ('2', '23452345-346745856-673434');
// 		}
//print_r($sql);
		$rs = $db->executeSQL($sql,true);
		$htmlOutput = '';
		//Build HTML Table around result
		//print_r($rs);
		
		//select for check if the credential now exists
		$rs = utility::getCredentials($userid,$id,$pubKey);
		if($rs){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * getCredentials depending on userid, keyId, keyvalue or a combination of these
	 * but the function needs at least one correct value
	 * @param {string} $userid
	 * @param {string} $keyIdentifier
	 * @param {string} $pubKey
	 * @return {boolean|array|boolean}
	 */
	function getCredentials($userid = false,$keyIdentifier = false,$pubKey = false)
	{
		$db = new db();
		$isActive = -1;
		utility::addLog('getCredentials, $userid = '.$userid.' und $keyIdentifier = '.$keyIdentifier.' und $$pubKey = '.$pubKey.'');
		if($userid === false AND $pubKey === false AND $keyIdentifier === false){
			utility::addLog('getCredentials wurde ohne parameter aufgerufen');
			return false;
		}
		
		$sqlWhere = '';
		
		if($userid){
			$sqlWhere .= " AND USERID = $userid ";
		}
		if($pubKey){
			$sqlWhere .= " AND KEYVALUE = '$pubKey' ";
		}
		if($keyIdentifier){
			$sqlWhere .= " AND KEYIDENTIFIER = '$keyIdentifier' ";
		}
		
		$sql = "SELECT * FROM PUBLICKEYS WHERE AKTIV = $isActive  $sqlWhere";
	//	print_r($sql);
		$rs = $db->executeSQL($sql,true);
		return $rs;
	//print_r($rs);
		if($rs){
			return $rs;
		}else{
			return array();
		}
	}
	
	
	/**
	 * deletes a credential with matches a given information
	 * the information needs to be as precise as possible, otherwise multiple entrys will match and 
	 * get deleted
	 * @param {string} $userid
	 * @param {string} $pubKey
	 * @param {string} $keyIdentifier
	 * @return {void}
	 */
	function deleteCredentials($userid = false,$keyIdentifier = false,$pubKey = false){
		$isActive = -1;
		$db = new db();
		utility::addLog('deleteCredentials, $userid = '.$userid.' und $keyIdentifier = '.$keyIdentifier.' und $pubKey = '.$pubKey.'');
		if($userid === false AND $pubKey === false AND $keyIdentifier === false){
			utility::addLog('getCredentials wurde ohne parameter aufgerufen');
			return false;
		}
		$sqlWhere = '';
		
		if($userid){
			$sqlWhere .= " AND USERID = $userid ";
		}
		if($pubKey){
			$sqlWhere .= " AND KEYVALUE = '$pubKey' ";
		}
		if($keyIdentifier){
			$sqlWhere .= " AND KEYIDENTIFIER = '$keyIdentifier' ";
		}
		
		$sql = "DELETE FROM PUBLICKEYS WHERE AKTIV = $isActive $sqlWhere ";
//print_r($sql);
		$db->executeSQL($sql,true);
	}
	
	
	/**
	 * Generates a random md5 hash challenge returns it
	 * @param {void}
	 * @return string
	 * 
	 */
	function getChallenge() {
		return md5(mt_rand(12,12));
		//return md5(openssl_random_pseudo_bytes(16));
	}
	
	/**
	 * erstellt eine Tabelle, param muss der Tabellenname sein
	 * @param string Tabellenname z.B. 'WF_USER'
	 */
	function createTable($tableName)
	{
		utility::addLog('erstelle Tabelle: '.$tableName);
		$db = new db();
		$sql = "SELECT * FROM $tableName";
		$rs = $db->executeSQL($sql,true);
		$htmlOutput = '';
		//Build HTML Table around result
//print_r($rs);
		if($rs){
			$htmlOutputTable .= '
				<table id="table_'.$tableName.'" style="width:100%;border=1">
					';
//print_r($rs);
			$i = 1;
			$htmlOutputHeads .='<tr>';
			foreach($rs as $rowKey => $rowValue)
			{
				//print_r($rs);
				$htmlOutput .= '<tr id="'.$rowKey.'">';
				
				foreach($rowValue as $columnKey => $columnValue)
				{
					//create head row
					if($i === 1){
						$htmlOutputHeads .= '<th>'.$columnKey.'</th>';
					}
					$htmlOutput .= '<td id="'.$columnKey.'">'.$columnValue.'</td>';
				}
						
				$htmlOutput .= '<tr>';
				$i++;
			}
			$htmlOutputHeads .='</tr>';
			$htmlOutput .= '
					</table>
				';
			
		}

		$htmlOutputFinal = $htmlOutputTable.$htmlOutputHeads.$htmlOutput;
		print_r($htmlOutputFinal);
		return $htmlOutputFinal;
	}
	
	
	/**
	 * Erstellt ein Selectfeld mit gegebener ID und Wert
	 * @param unknown $id
	 * @param unknown $value
	 * @return string
	 */
	function createSelect($tableName,$id, $value, $rs = false){
		utility::addLog('erstelle Select zu: '.$tableName);
		
		if(!is_array($rs)){
			$db = new db();	
			$sql = "SELECT $id, $value FROM $tableName";
			$rs = $db->executeSQL($sql,true);
		}
	//print_r($rs);
		if($rs){
//print_r($rs);
			$htmlOutputFinal .='<select id=select_'.$tableName.'_'.$id.'>';
			foreach($rs as $rowKey)
			{
// 				foreach($rowValue as $columnKey => $columnValue)
// 				{
					//$columnKey
// 					var_dump($columnKey);
// 					var_dump($columnKey);
// 					var_dump($columnValue);
					$htmlOutputFinal .= '<option value='.$rowKey[$id].'>'.$rowKey[$value].'</option>';
// 				}
			}
			$htmlOutputFinal .='</select>';				
		}
		print_r($htmlOutputFinal);
		return $htmlOutputFinal;
	}
	
	/**
	 * get the current state of WINDOWS_HELLO_STATUS
	 * @param {void}
	 * @return string} value of WINDOWS_HELLO_STATUS
	 */
	function getWindowsHelloStatus(){
		utility::addLog('hole WindowsHelloStatus');
		$db = new db();
		$tableName = 'SETTINGS';
		$id = 'WINDOWS_HELLO_STATUS';
		$sql = "SELECT $id FROM $tableName";
		$rs = $db->executeSQL($sql,true);
		if($rs){
			return $rs[0][$id];
		}else{
			return 'oops, nothing found';
		}
	}
	
	/**
	 * Toggles the state of WINDOWS_HELLO_STATUS in table SETTINGS
	 * @param {void}
	 * @return {void}
	 */
	function setWindowsHelloStatus()
	{
		
		//get Old value
		$oldValue = utility::getWindowsHelloStatus();
		
		//set new value
		if($oldValue){
			$newValue = 0;
		}else{
			$newValue = 1;
		}
		
		//update value in db
		utility::addLog('setze WindowsHelloStatus:'.$newValue.' der alte Wert war: '.$oldValue);
		$db = new db();
		$tableName = 'SETTINGS';
		$id = 'WINDOWS_HELLO_STATUS';
		$sql = "UPDATE $tableName SET $id = $newValue";
		$db->executeSQL($sql,true);
	}
	
	
	/**
	 * Returns a selectbox with all the available policies for selection
	 * @params {string} $tableName
	 * @params {int} $id
	 * @params {int} $value
	 */
	function createSelectPolicy($tableName, $id, $value)
	{
		$rs = utility::getPolicyList();
		$result = utility::createSelect($tableName, $id, $value, $rs);
	}
	
	
	/**
	 * Get all policys
	 * @param {void}
	 * @retrun {array}
	 */
	function getPolicyList()
	{
		$rs = array(
				array( $id => 0, $value => 'Password only'),
				array( $id => 1, $value => '2-FA'),
				array( $id => 2, $value => 'Passwordless'),
		);
		return $rs;
	}
	
	
	/**
	 * Get the publickey from a username and keyid from the PUBLICKEYS table
	 * @param {string} $username
	 * @param {string} $keyID
	 * @param {string} $wholeEntry
	 * @return {mixed|boolean|string} string KEYVALUE per default or an array
	 */
	function getPublicKey($username, $keyID,$wholeEntry = false)
	{
		utility::addLog('Aufruf getPublicKey() mit $username = '.$username.' und $keyID = '.$keyID.'');
		$userid = utility::getUseridFromUsername($username);
		
		//check
		if(!is_numeric($userid)){
			utility::addLog('Userid '.$userid.' ist nicht numerisch. Abbruch.');
			return false;
		}
		
		$db = new db();
		$isActive = -1;
		$sql = "SELECT * FROM PUBLICKEYS WHERE USERID = $userid AND KEYIDENTIFIER = '$keyID' AND AKTIV = $isActive ";
		$rs = $db->executeSQL($sql,true);
		
		if($rs){
			if($wholeEntry){
				return $rs[0];
			}else{
				return $rs[0]['KEYVALUE'];
			}
		}else{
			return false;
		}
	}
	
}//end utility class


class db{
	
	public $Datasource;
	public $connection = NULL;
	
	/**
	 * Constructor for the database connection
	 *
	 * @param {void}
	 * @return {object} database
	 */
	public function __construct() {
		return self::dbconnect();
	}
	
	
	/**
	 * build a connection with db for local testing purpose
	 * @param {void}
	 * @return {object} database
	 */
	function dbconnect(){
		//utility::addLog(__METHOD__.' : Verbindungsaufbau mit dbconnect() gestartet');
		//connect to mysql DB
		$host="localhost";
		$user="webflow1";
		$password="1234";
		$database="thesis";
		$connection = mysqli_connect($host,$user,$password,$database);
	
		if ( $connection )
		{
			//utility::addLog(__METHOD__.' : Verbindung erfolgreich.');
		}else{
			utility::addLog('keine Verbindung möglich:');
			utility::addLog(''.mysqli_error());
			//die('keine Verbindung möglich: ' . mysqli_error());
		}
	
		if ($connection->connect_error) {
			utility::addLog('Connection failed:');
			utility::addLog($connection->connect_error);
		}
	
		//set charset
		mysqli_set_charset($connection, 'utf8');

		return $connection;
	}
	
	/**
	 * Executes the given sql. similar like in the production call from FIVE Webflow,
	 * so the real one could be used.
	 * Führt das mitgegebene SQL aus. Orientiert sich vom Aufruf her am FIVE Webflow,
	 * damit die Funktionen gleich genutzt werden können.
	 * @param string $sql
	 * @param boolean $asArray
	 * @return {array|boolean}
	 */
	function executeSQL($sql, $asArray=false){
		$db = new db();
		$connection = $db->dbconnect();
		
		// make difference between object and array return type like in production, not used in poc
		if($asArray){
			$result = $connection->query($sql);
		}else{
			$result = $connection->query($sql);
		}
		if($result){
//var_dump($result);
			if($result === false OR $result === true){
				return $result; 
			}
			
			if ($result->num_rows > 0) {
				$resultArray = array();
				while($row = $result->fetch_assoc()) {
					$resultArray[] = $row;
					//echo "USERID: " . $row["USERID"]. " - NAME: " . $row["NAME"]. " - FULLNAME: " . $row["FULLNAME"]. "<br>";
				}
				return $resultArray;
			} else {
				return array();
				//echo "Sorry, 0 results found";
			}
		}else{
			return array();
			//echo "Sorry, 0 results found";
		}
		
	}
	
}//end class db