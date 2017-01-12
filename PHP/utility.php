<?php
/*
 * !!! IMPORTANT !!! Enter the right configuration in "_plugin_utility::getConfiguration(xxx)
 * so the include only is done if you are in the webflow demo environment
 */
$localTesting = 1;
$webflowDemo = 2;
_plugin_utility::getConfiguration($webflowDemo);

//only load these files if its the frontend of the webflow application that is calling
if( $_SESSION['importWebflowClasses'] AND strpos(__FILE__, 'Addon') === false ){

	$__configObject = new stdClass();
	$__configObject->DocumentRoot = "/var/www";
	require_once ($__configObject->DocumentRoot . "/modules/BerkeWebflowConnection/connect.conf.php");

    LoadModuleLib("libraries/core", "BerkeDataSource");
    LoadModuleLib("libraries/sqlparser", "BerkeDataSource");
    LoadModuleLib("libraries/datasource.if.php", "BerkeDataSource", false);
    LoadModuleLib("libraries/WebAppAPI", "BerkeDataSource");
    //                       Demo Thesis App ID                Version
    WebAppAPI::SetActiveApp("34c06e57de4fc1a7d0da37e744cfe7c9", "1");
	
	require_once ($__configObject->DocumentRoot . "/" . WebAppAPI::GetVar ( "sysvar", "AppPlugin" ) . "/KrediWF/common/common.lib.php");
	require_once ($__configObject->DocumentRoot . "/" . WebAppAPI::GetVar ( "sysvar", "AppPlugin" ) . "/KrediWF/common/language.lib.php");
	require_once ($__configObject->DocumentRoot . "/" . WebAppAPI::GetVar ( "sysvar", "AppPlugin" ) . "/KrediWF/common/db.lib.php");
	require_once ($__configObject->DocumentRoot . "/" . WebAppAPI::GetVar ( "sysvar", "AppPlugin" ) . "/KrediWF/Model/version/System/wf.authentication.functions.php");

	_plugin_utility::getConfiguration($webflowDemo); //webflow resets session, get the config values again

}

//allow to catch GET Calls
_plugin_utility::catchResponse($_REQUEST);

/**
* Here are all functions for the serverside, that are used in different files of the thesis project.
* If you want to enable these functions, use require_once('utility.php'); in the first lines of your php
* script.
* because of the webflow installation, wie have to use the classname prefix '_plugin_'
* @autor MT
*/
class _plugin_utility {

	function test(){
		return 'calling Test function';
	}
	
	/**
	 * Set configuration dependig on the use case
	 * @param number $configSetting
	 */
	function getConfiguration($configSetting = 1)
	{
		$localTesting = 1;
		$webflowDemo = 2;
		if( !isset($_SESSION['redirectToAfterSuccess'])){
			$_SESSION['redirectToAfterSuccess'] = '';
		}
		
		$_SESSION['webApiLoginDone'] = false;
		$_SESSION['webApiConfigSettingId'] = $configSetting;
		$_SESSION['webApiConfigSettingName'] = _plugin_utility::getNameOfConfigSetting($configSetting);
		
		
		if($configSetting == $webflowDemo){
			//config for webflow thesis prototype
			$_SESSION['useWebflowConfig'] = true;
			$_SESSION['importWebflowClasses'] = true;
			$_SESSION['redirectToAfterSuccess'] = '/exec/webflow/Application/Aufgabenliste/';
			$_SESSION['redirectToLogin'] = '/exec/webflow/Start/Login/';

		}else{
			//Default is local testing
			$_SESSION['useWebflowConfig'] = false;
			$_SESSION['importWebflowClasses'] = false;
			$_SESSION['redirectToAfterSuccess'] ="../PHP/originalWebflowStartPage.php";
			$_SESSION['redirectToLogin'] = '../PHP/originalWebflowStartPage.php';
		}
	}
	
	/**
	 * react to all get- or post-information
	 * @param unknown $response
	 */
	function catchResponse($response)
	{

		//Policy Case
		if($response['createPolicy'] == 1 && isset($response['userid']) && isset($response['policyid']) ){
			_plugin_utility::addLog('called  createPolicy: userid = '.$response['userid'].' and policyid = '.$response['policyid']);
			_plugin_utility::changePolicy($response['userid'],$response['policyid']);
		}
		
		if($response['deletePolicy'] == 1 && isset($response['ptid']) ){
			_plugin_utility::addLog('called  deletePolicy: ptid = '.$response['ptid']);
			_plugin_utility::deletePolicy($response['ptid']);
		}
		
		if($response['deletePublicKey'] == 1 && isset($response['keyid']) ){
			_plugin_utility::addLog('called  deletePublicKey: keyid = '.$response['keyid']);
			_plugin_utility::deleteCredentials(false,false,false,$response['keyid']);
		}
		
		if($response['changeWindowsHelloStatus'] == 1 ){
			_plugin_utility::addLog('called  changeWindowsHelloStatus');
			_plugin_utility::setWindowsHelloStatus();
		}
		
		if( isset($response['successUrl']) && isset($response['userName']) ){
			_plugin_utility::addLog('called  successUrl');
			$originalUrl = str_replace("|", "/", $response['successUrl']);
			_plugin_authentication::doThesisLogin($response['userName']);
			header( 'Location: '.$originalUrl.'' ) ;
		}
		

	}
	
	
	/**
	 * Change policy or create a new one if none is set
	 * @param {int} $userid
	 * @param {int} $policy
	 */
	function changePolicy($userid,$policy)
	{
		_plugin_utility::addLog('change Policy, Userid = '.$userid.' und Policy = '.$policy.'');
		$db = _plugin_utility::loadDb();
		$timeNow = time();
		$isActive = -1;
		
		$sql = "SELECT * FROM PT_USER WHERE USERID = $userid AND AKTIV = $isActive";
		$rs = $db->executeSQL($sql,true);
		if($rs){
			_plugin_utility::addLog('Update Policy');
			$sql = "UPDATE PT_USER SET POLICY = '$policy', CHANGEDTIME = $timeNow WHERE USERID = $userid AND AKTIV = $isActive";
		}else{
			_plugin_utility::addLog('insert Policy');
			$sql = "INSERT INTO PT_USER (USERID,POLICY,AKTIV,CREATEDTIME,CHANGEDTIME) VALUES ($userid,'$policy',$isActive,$timeNow,$timeNow)";
		}
		$rs = $db->executeSQL($sql,true);
		$htmlOutput = '';
	}

	
	/**
	 * Get a policy record as array from the given userid
	 * @param {int} $userid
	 * @param {boolean} $wholeEntry, if false, only return the policy value
	 * @param {boolean} $useridIsUsername, if the  first value is a username, uses here true, default is false
	 * @return  {array|integer} $rs
	 */
	function getPolicyFromUser($userid,$wholeEntry = true, $useridIsUsername = false)
	{
		if($useridIsUsername){
			$userid = _plugin_utility::getUseridFromUsername($userid);
		}
		_plugin_utility::addLog('Aufruf getPolicyFromUser() mit USERID = '.$userid.'');
		$db = _plugin_utility::loadDb();
		$isActive = -1;
		$sql = "SELECT * FROM PT_USER WHERE USERID = $userid AND AKTIV = $isActive";
		$rs = $db->executeSQL($sql,true);

		if($rs){
			if($wholeEntry){
				return $rs[0];
			}else{
				return (int)$rs[0]['POLICY'];
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
		_plugin_utility::addLog('Aufruf getUseridFromPtid() mit PTID = '.$ptid.'');
		$db = _plugin_utility::loadDb();
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
		$a = debug_backtrace();
 		if(isset($a['1'])){
 			$b = $a['1'];
 		}else{
 			$b = $a['0'];
 		}
		$lastSign = '\\';
		$c = substr(strrchr ( $b['file'],$lastSign),1);
		
		if( !is_array($_SESSION['log']) ){
			$_SESSION['log'] = array();
		}
		array_push($_SESSION['log'],$c.' : '.$log);
	}
	
	/**
	 * clear all log entrys
	 * @param {void}
	 * @return {void}
	 */
	function resetLog()
	{
		$_SESSION['log']=array();
	}

	/**
	 * loads the database connection depending on isWebflowInstallation()
	 * @param {void}
	 * @return {db object}
	 */
	function loadDb(){
		if( _plugin_utility::isWebflowInstallation() ){
			$db = new db();
		}else{
			$db = new _plugin_db();
		}
		return $db;
	}

	/**
	 * Get information about the given username
	 * @param {string} $userInfo
	 * @param {int} $infoValue
	 * @return {array|boolean}
	 */
	function getWfUser($username, $infoValue = 1)
	{
		$usersSQL = "SELECT * FROM WF_USER WHERE NAME = '$username'";
		$db = _plugin_utility::loadDb();
		$rs = $db->executeSQL($usersSQL,true);
		if($rs){
			return $rs;
		}else{
			return false;
		}
	}
	
	
	/**
	 * Get Userid from the given username
	 * @param {string} username
	 * @return void
	 */
	function getUseridFromUsername($username)
	{
		$usersSQL = "SELECT USERID FROM WF_USER WHERE NAME = '$username'";
		$db = _plugin_utility::loadDb();
		$rs = $db->executeSQL($usersSQL,true);
		if($rs){
			return $rs[0]['USERID'];
		}else{
			_plugin_utility::addLog('Error in getUseridFromUsername: Username = '.$username.' not found');
			return false;
		}
	}
	
	/**
	 * check if 
	 *  - the user exists
	 *  - is active
	 * @param {string}
	 * @return {boolean}
	 */
	function checkUsername($username)
	{
		$isActive = -1;
		$sql = "SELECT * FROM WF_USER WHERE NAME = '$username' AND AKTIV = $isActive";
		$db = _plugin_utility::loadDb();
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
		
		if( _plugin_utility::isWebflowInstallation() ){
			$result = _plugin_Authentication::doThesisCheckPassword($username,$password);
		}else{
			$userEntry = _plugin_utility::getWfUser($username);
			if($userEntry){
				if($userEntry['0']['USERPASSWORD'] == $password){
					$result = true;
				}
				$result = false;
			}else{
				$result = false;
			}
		}
		return $result;
	}
	
	
	/**
	 * deletes a PT_USER depending on input
	 * @param {int|string} $value
	 * @param {boolean} $valueIsUsername
	 * @param {boolean} $valueIsUserid
	 */
	function deletePolicy($value,$valueIsUsername = false,$valueIsUserid = false)
	{
		$userid = false;
		$ptid = false;
		
		//fill correct values for later use
		if($value){
			if($valueIsUserid){
				$userid = $value;
			}elseif($valueIsUsername){
				$userid = _plugin_utility::getUseridFromUsername($value);
			}else{
				$ptid = $value; 
			}
		}
		
		$isActive = -1;
		$db = _plugin_utility::loadDb();
		$sqlWhere = '';
		
		if($userid !== false){
			$sqlWhere .= " AND USERID = $userid ";
		}
		if($ptid  !== false){
			$sqlWhere .= " AND PTID = $ptid ";
		}
		
		$sql = "DELETE FROM PT_USER WHERE AKTIV = $isActive $sqlWhere ";
		$db->executeSQL($sql,true);
	}

	/**
	 * Check, if the given username has policy keys already saved in PUBLICKEYS table
	 * TSCM: 20161016 renamed to hasKeys(), because that is what the function does
	 * @param {string} $username
	 * @return {boolean}
	 */
	function hasKeys($username) {
		//get userid
		$userid = _plugin_utility::getUseridFromUsername($username);
		//check userid
		$rs = _plugin_utility::getCredentials($userid);
		if($rs){
			if($rs[0]){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}
	
	
	/**
	 * Save the credatial information from the client in the database
	 * @return: boolean
	 * Es werden die vom Client übertragenen Credentials in der DB gespeichert
	 * USERID KEYVALUE KEYIDENTIFIER von Tabelle PUBLICKEYS
	 */
	function saveCredentials($username, $id, $pubKey)
	{
		_plugin_utility::addLog('save credentials, Username = '.$username.' und KEYIDENTIFIER = '.$id.' und $KEYVALUE = '.$pubKey.'');
		
		//Username zu USERID wandeln
		$userid = _plugin_utility::getUseridFromUsername($username);
		$db = _plugin_utility::loadDb();
		$isActive = -1;
		$timeNow = time();
		_plugin_utility::addLog('New Credential, insert it');
		$sql = "INSERT INTO PUBLICKEYS (USERID,KEYVALUE,KEYIDENTIFIER, AKTIV,CREATEDTIME,CHANGEDTIME) VALUES ($userid,'$pubKey','$id',$isActive,$timeNow,$timeNow)";
		$rs = $db->executeSQL($sql,true);
		$htmlOutput = '';
		//Build HTML Table around result
		//select for check if the credential now exists
		$rs = _plugin_utility::getCredentials($userid,$id,$pubKey);
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
		$db = _plugin_utility::loadDb();
		$isActive = -1;
		_plugin_utility::addLog('getCredentials, $userid = '.$userid.' und $keyIdentifier = '.$keyIdentifier.' und $pubKey = '.$pubKey.'');
		if($userid === false AND $pubKey === false AND $keyIdentifier === false){
			_plugin_utility::addLog('getCredentials wurde ohne parameter aufgerufen');
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
		$rs = $db->executeSQL($sql,true);
		return $rs;
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
	function deleteCredentials($userid = false,$keyIdentifier = false,$pubKey = false,$keyid = false)
	{
		$isActive = -1;
		$db = _plugin_utility::loadDb();
		_plugin_utility::addLog('deleteCredentials, $userid = '.$userid.' und $keyIdentifier = '.$keyIdentifier.' und $pubKey = '.$pubKey.' und $keyid = '.$keyid.'');
		if($userid === false AND $pubKey === false AND $keyIdentifier === false AND $keyid === false){
			_plugin_utility::addLog('getCredentials wurde ohne parameter aufgerufen');
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
		if($keyid){
			$sqlWhere .= " AND KEYID = $keyid ";
		}
		
		$sql = "DELETE FROM PUBLICKEYS WHERE AKTIV = $isActive $sqlWhere ";
		$db->executeSQL($sql,true);
	}
	
	
	/**
	 * Generates and returns a random md5 hash challenge string
	 * @param {void}
	 * @return string
	 */
	function getChallenge() {
		return md5(mt_rand(0,100000));
		//return md5(openssl_random_pseudo_bytes(16));
	}
	
	/**
	 * erstellt eine Tabelle, param muss der Tabellenname sein
	 * @param string Tabellenname z.B. 'WF_USER'. If you add "|COLUMNNAME" the selection is limited to this column
	 */
	function createTable($param)
	{

		list($tableName,$select) = explode('|',$param);
		
		if( trim($select) != '' ){
			$select = $select;
		}else{
			$select = '*';
		}
		
		_plugin_utility::addLog('erstelle Tabelle: '.$tableName);
		$db = _plugin_utility::loadDb();
		$sql = "SELECT $select FROM $tableName";
		$rs = $db->executeSQL($sql,true);

		$htmlOutput = '';
		//Build HTML Table around result TableMainContainer tableMainSub
		if($rs){
			$htmlOutputTable .= '
				<table id="table_'.$tableName.'" class="tableMainSub" style="width:100%;">
					';
			$i = 1;
			$htmlOutputHeads .='<tr>';
			foreach($rs as $rowKey => $rowValue)
			{
				if($i === 1){
					$class = '';
				}elseif( $i % 2 != 0 ){
					$class = 'odd';
				}elseif( $i % 2 == 0 ){
					$class = 'even';
				}
				$htmlOutput .= '<tr id="'.$rowKey.'"  class="'.$class.'" >';
				
				foreach($rowValue as $columnKey => $columnValue)
				{
					//create head row
					if($i === 1){
						$htmlOutputHeads .= '<th>'.$columnKey.'</th>';
					}
					$htmlOutput .= '<td id="'.$columnKey.'" class="'.$class.'" >'.$columnValue.'</td>';
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
		
		return $htmlOutputFinal;
	}
	
	
	/**
	 * Erstellt ein Selectfeld mit gegebener ID und Wert
	 * @param unknown $id
	 * @param unknown $value
	 * @return string
	 */
	function createSelect($tableName,$id, $value, $rs = false)
	{
		_plugin_utility::addLog('erstelle Select zu: '.$tableName);
		if(!is_array($rs)){
			$db = _plugin_utility::loadDb();
			$sql = "SELECT $id, $value FROM $tableName";
			$rs = $db->executeSQL($sql,true);
		}

		if($rs){
			$htmlOutputFinal .='<select id=select_'.$tableName.'_'.$id.'_'.$value.'>';
			foreach($rs as $rowKey)
			{
				$htmlOutputFinal .= '<option value='.$rowKey[$id].'>'.$rowKey[$value].'</option>';
			}
			$htmlOutputFinal .='</select>';				
		}
		return $htmlOutputFinal;
	}
	
	/**
	 * get the current state of WINDOWS_HELLO_STATUS
	 * @param {void}
	 * @return string} value of WINDOWS_HELLO_STATUS
	 */
	function getWindowsHelloStatus()
	{
		_plugin_utility::addLog('hole WindowsHelloStatus');
		$db = _plugin_utility::loadDb();
		$tableName = 'SETTINGS';
		$id = 'WINDOWS_HELLO_STATUS';
		$sql = "SELECT $id FROM $tableName";
		$rs = $db->executeSQL($sql,true);
		if($rs){
			
			$result = $rs[0][$id];
		}else{
			$result = '0';
		}
		$_SESSION['windowsHelloStatus'] = $result;
		return $result;
	}
	
	/**
	 * Toggles the state of WINDOWS_HELLO_STATUS in table SETTINGS
	 * @param {void}
	 * @return {void}
	 */
	function setWindowsHelloStatus()
	{
		$oldValue = _plugin_utility::getWindowsHelloStatus();
		if($oldValue){
			$newValue = 0;
		}else{
			$newValue = 1;
		}
		//update value in db
		_plugin_utility::addLog('setze WindowsHelloStatus: '.$newValue.' der alte Wert war: '.$oldValue);
		$db = _plugin_utility::loadDb();
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
		$rs = _plugin_utility::getPolicyList($id, $value);
		$result = _plugin_utility::createSelect($tableName, $id, $value, $rs);
		return $result;
	}
	
	
	/**
	 * Get all policys in an array
	 * @param {void}
	 * @return {array}
	 */
	function getPolicyList($id, $value)
	{
		$list = Array(
					Array( $id => 0, $value => _plugin_utility::getPolicyName(0)),
					Array( $id => 1, $value => _plugin_utility::getPolicyName(1)),
					Array( $id => 2, $value => _plugin_utility::getPolicyName(2)),
		);
		return $list;
	}
	
	
	/**
	 * Get a policy name for an given id
	 * @param {int} $id
	 * @return {string}
	 */
	function getPolicyName($id)
	{
		if($id == 0){
			$result = 'Password only';
		}elseif($id == 1){
			$result = '2-FA';
		}elseif($id == 2){
			$result = 'Passwordless';
		}else{
			$result = '_unknown_id_'.$id;
		}
		return $result;
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
		_plugin_utility::addLog('Aufruf getPublicKey() mit $username = '.$username.' und $keyID = '.$keyID.'');
		$userid = _plugin_utility::getUseridFromUsername($username);
		
		//check
		if(!is_numeric($userid)){
			_plugin_utility::addLog('Userid '.$userid.' ist nicht numerisch. Abbruch.');
			return false;
		}
		
		$db = _plugin_utility::loadDb();
		$isActive = -1;
		$sql = "SELECT * FROM PUBLICKEYS WHERE USERID = $userid AND KEYVALUE = '$keyID' AND AKTIV = $isActive ";
		$rs = $db->executeSQL($sql,true);
		
		if($rs){
			if($wholeEntry){
				return $rs[0];
			}else{
				return $rs[0]['KEYIDENTIFIER'];
			}
		}else{
			return false;
		}
	}
	
	
	/**
	 * check if webflow installation is active or not (then it is local content)
	 * @param void
	 * @return {boolean} 
	 */
	function isWebflowInstallation()
	{
		if( $_SESSION['useWebflowConfig'] === true){
			return $_SESSION['useWebflowConfig'];
		}else{
			return false;
		}
	}
	
	
	/**
	 * Returns the Name of the given configuration
	 * @param {int} $configId
	 * @return {string} $result
	 */
	function getNameOfConfigSetting($configId)
	{
		if($configId === 1){
			$result = 'Local Testing';
		}elseif($configId === 2){
			$result = 'FIVE Webflow Demo';
		}else{
			$result = 'No Name matched the input: '.$configId;
		}
		return $result;
	}
	
	
	/**
	 * Print out some important session values for HTML view via echo
	 * @param {void}
	 * @return {void}
	 */
	function printImportantSessionValues()
	{
		echo "<span class='logTitel'>Print important session values </span><br /> ";
		echo "webApiConfigSettingId: ".$_SESSION['webApiConfigSettingId']." <br /> ";
		echo "webApiConfigSettingName: ".$_SESSION['webApiConfigSettingName']." <br /> ";
		echo "useWebflowConfig: ".$_SESSION['useWebflowConfig']." <br /> ";
		echo "importWebflowClasses: ".$_SESSION['importWebflowClasses']." <br /> ";
		echo "redirectToAfterSuccess: ".$_SESSION['redirectToAfterSuccess']." <br /> ";
		echo "redirectToLogin: ".$_SESSION['redirectToLogin']." <br /> ";
		echo "windowsHelloStatus: ".$_SESSION['windowsHelloStatus']." <br /> ";
		echo "<br />";
	}
	
}//end utility class



/**
 * Database connection class
 * @author tscm
 */
class _plugin_db{
	
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
		//connect to mysql DB, change the values if you have set up a different database
		$host="localhost";
		$user="webflow";
		$password="1234";
		$database="thesis";
		$connection = mysqli_connect($host,$user,$password,$database);
	
		if ( $connection )
		{
			
		}else{
			_plugin_utility::addLog('keine Verbindung möglich:');
			_plugin_utility::addLog(''.mysqli_error());
		}
	
		if ($connection->connect_error) {
			_plugin_utility::addLog('Connection failed:');
			_plugin_utility::addLog($connection->connect_error);
		}
	
		//set charset
		mysqli_set_charset($connection, 'utf8');

		return $connection;
	}
	
	/**
	 * Executes the given sql. similar like in the production call from FIVE Webflow,
	 * so the real one could be used.
	 * @param string $sql
	 * @param boolean $asArray
	 * @return {array|boolean}
	 */
	function executeSQL($sql, $asArray=false){
		$db = _plugin_utility::loadDb();
		$connection = $db->dbconnect();
		
		// make difference between object and array return type like in production, not used here in prototype
		if($asArray){
			$result = $connection->query($sql);
		}else{
			$result = $connection->query($sql);
		}
		if($result){
			if($result === false OR $result === true){
				return $result; 
			}
			
			if ($result->num_rows > 0) {
				$resultArray = array();
				while($row = $result->fetch_assoc()) {
					$resultArray[] = $row;
				}
				return $resultArray;
			} else {
				return array();
			}
		}else{
			return array();
		}
		
	}
	
}//end class _plugin_db
