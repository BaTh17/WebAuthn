<?php


/**
 * Here are all functions for the serverside, that are used in different files of the thesis project.
* If you want to enable these functions, use require_once('utility.php'); in the first lines of your php
* script
*/
class utility {
	
	
	function catchResponse($response)
	{
		//TODO
		//print_r(__METHOD__.__LINE__);
		//print_r($response);
		//Policy Case
		if($response['createPolicy'] == 1 && isset($response['userid']) && isset($response['policyid']) ){
			utility::changePolicy($response['userid'],$response['policyid']);
		}

	
	}
	
	function changePolicy($userid,$policy){
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
	
	function getPolicyFromUser($userid){
		utility::addLog('Aufruf getPolicyFromUser() mit USERID = '.$userid.'');
		$db = new db();
		$isActive = -1;
		$sql = "SELECT * FROM  INTO PT_USER ('USERID','POLICYID','AKTIV') VALUES ($userid,'$policy',$isActive) * FROM $tableName";
		$rs = $db->executeSQL($sql,true);
		
	}
	
	/**
	 * Fügt einen neuen Eintrag ins log
	 * @param: string z.B. "Teil eines Logs"
	 * @retrun void
	 */
	function addLog($log)
	{
		if(!is_array($_SESSION['log'])){
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
	 * Leert das Session Log
	 */
	function resetLog()
	{
		if(!isset($_SESSION['log'])){
			session_start();
		}
		$_SESSION['log']=array();
	}


	/**
	 * Get inforation about the given user
	 */
	function getWfUser($userInfo, $infoValue = 1){

		$usersSQL = "SELECT * FROM WF_USER WHERE NAME = '$userInfo'";
		$db = new $db;
		$rs = $db->executeSQL($usersSQL);
		if($rs){
			return $rs;
		}else{
			return false;
		}
	}


	//changeWindowsHelloStatus
	//(in Settings)
	
	


	//get Policy ( 0,1,2)

	// create Policy (dropdown)


	//get Keys from WF_USERS
	//input ( string USERNAME)
	// return True / false


	// checkPassword (default)
	


	//get Public KEy from USER (NAME)


	//create TAble
	
	/**
	 * überprüfen ob ein Benutzername in der DB existiert oder ev. gesperrt ist.
	 * Return: boolean
	 */
	function checkUsername($username) {
	
		//User exists and is activ
	
		if($username=="schf" || $username=="tscm" || $username =="hello")
			return true;
			else
				return false;
	
	}
	
	/**
	 * checkPassword() - PW vom Benutzer überprüfen
	 * Return: boolean
	 *
	 */
	
	function checkPW($username, $pw) {
		if($username=="schf" && $pw=="test")
			return true;
			if($username=="tscm" && $pw=="test")
				return true;
				else
					return false;
	}
	
	/**
	 * @return: Integer
	 * 0 = Password only
	 * 1 = 2-FA
	 * 2 = Passwordless
	 */
	function getPolicy($username) {
	
		//returnieren der Policy des Users - vorher nochmals überprüfen ob es ihn gibt:
	
		if($username=="schf")
			return 0;
			if($username=="tscm")
				return 1;
				if($username=="hello")
					return 2;
	
	
	}
	
	
	/**
	 * Löscht eine Policy aufgrund des Benutzernamens
	 * @param int
	 * @return void
	 */
	function deletePolicy($username) {
	
		
	
	}

	/**
	 * Prüfen ob für einen mitgegebenen Benutzername Public Keys in der Tabelle gespeichert sind
	 * TSCM: 20161016 umbenennt auf hasKeys() , entspricht eher dem, was gemacht wird
	 * Return: boolean
	 *
	 */
	function hasKeys($username) {
		if($username=="schf")
			return true;
			if($username=="tscm")
				return true;
				if($username=="hello")
					return true;
					else
					return false;
	}
	
	/**
	 * @return: boolean
	 * Es werden die vom Client übertragenen Credentials in der DB gespeichert
	 */
	function saveCredentials($username, $id, $pubKey) {
	
		return true;
	
	}
	
	
	/**
	 * Gibt eine Random MD5 Hash Challenge mit
	 * @return string
	 */
	function getChallenge() {
	
		return md5(mt_rand(12,12));
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
				<table id=table_"'.$tableName.'" style="width:100%;border=1">
					';
//print_r($rs);
			$i = 1;
			$htmlOutputHeads .='<tr>';
			foreach($rs as $rowKey => $rowValue)
			{
				//print_r($rs);
				$htmlOutput .= '<tr id='.$rowKey.'>';
				
				foreach($rowValue as $columnKey => $columnValue)
				{
					//create head row
					if($i === 1){
						$htmlOutputHeads .= '<th>'.$columnKey.'</th>';
					}
					$htmlOutput .= '<td id='.$columnKey.'>'.$columnValue.'</td>';
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
	function createSelect($tableName,$id, $value){
		utility::addLog('erstelle Select zu: '.$tableName);
		$db = new db();
		
		$sql = "SELECT $id, $value FROM $tableName";
//print_r($sql);
		$rs = $db->executeSQL($sql,true);
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
	
	function getWindowsHelloStatus(){
		//TODO
	}
	
}//end utility class


class db{
	
	public $Datasource;
	public $connection = NULL;
	
	/**
	 * Constructor
	 *
	 * @param String[Optional] $WFdbSystem = WFDBSOURCE {ORAWFTEST,BERKEDBSOUCEMYSQL}
	 */
	public function __construct() {
		
		return self::dbconnect();
		
		/*
		//$WFdbSystem = WFDBSOURCE;
		global $__configObject;
	
		//$Identifier = base64_encode ( "/modules/BerkeDataSource/drivers/" . WebAppAPI::GetVar ( "sysvar", "AppID" ) . "/" . WebAppAPI::GetVar ( "sysvar", "AppVersion" ) . "/{$WFdbSystem}/config.conf" );
	
		$appID = WebAppAPI::GetVar ( "sysvar", "AppID" );
		$version = WebAppAPI::GetVar ( "sysvar", "AppVersion" );
		$path = $__configObject->DocumentRoot."/modules/BerkeDataSource/drivers/" . WebAppAPI::GetVar ( "sysvar", "AppID" ) . "/" . WebAppAPI::GetVar ( "sysvar", "AppVersion" ) . "/{$WFdbSystem}/config.conf";
		$this->Datasource = new BerkeDataSource ($__configObject->DocumentRoot."/modules/BerkeDataSource/drivers/" . WebAppAPI::GetVar ( "sysvar", "AppID" ) . "/" . WebAppAPI::GetVar ( "sysvar", "AppVersion" ) . "/{$WFdbSystem}/config.conf");
	
		//$this->Datasource = new stdClass();
		//$this->Datasource->DataSourceHandle = WebAppAPI::GetDatasource($WFdbSystem);
	
		//var_dump($Identifier);
		//var_dump($this->Datasource->InstanciateByIdentifier ( $Identifier ));
		// Informationen �bergeben
		//$this->Datasource->InstanciateByIdentifier ( $Identifier );
	
		// Treiber laden
		$this->Datasource->Load ();
	
		if ($WFdbSystem == WFDBSOURCE) {
			switch (DBSYSTEM) {
				case "MSSQL" :
					break;
				case "MYSQL" :
					break;
				case "ORACLE" :
					$SQL = "ALTER SESSION SET nls_territory = 'Switzerland'";
					$this->executeSQL ( $SQL );
					break;
			}
		}
		*/
	}
	
	function executeMySQL($sql,$asArray=false) {
		
		
		var_dump($sql);
		$connection = $this->connection;
		mysqli_query($connection, $sql) or die('Fehler beim Ausführen des SQL Statements');
		
		//TODO
		if($asArray){
			
		}else{
			
		}
		

		$sql = "SELECT USERID, NAME, FULLNAME FROM WF_USER";
		$result = $connection->query($sql);
		
		if ($result->num_rows > 0) {
			return $result;
// 			while($row = $result->fetch_assoc()) {
// 				echo "USERID: " . $row["USERID"]. " - NAME: " . $row["NAME"]. " - FULLNAME: " . $row["FULLNAME"]. "<br>";
// 			}
		} else {
			return 'Sorry, 0 results found';
			//echo "Sorry, 0 results found";
		}
		

		//Example SQL Select
		/*
		 $sql = "SELECT * FROM WF_USER";
		 mysqli_query($connection, $sql) or die('Error selecting table WF_USER.');
		
		
		 // Check connection
		 if ($connection->connect_error) {
		 die("Connection failed: " . $db_link->connect_error);
		 }
		 */
		/*
	
		if ($this->Datasource->IsLoaded) {
	
			$this->Datasource->DataSourceHandle->ExecuteQuery ( $SQL );
			if ($this->Datasource->DataSourceHandle->GetResult ()) {
				return $this->Datasource->DataSourceHandle->GetResult ();
			} else {
				return false;
			}
		}
		*/
	}
	
	
	
	/**
	 * Verbindungsaufbau mit Datenbank
	 * @return unknown
	 */
	function dbconnect(){
		//utility::addLog(__METHOD__.' : Verbindungsaufbau mit dbconnect() gestartet');
		//connect to mysql DB
		$host="localhost";
		$user="webflow";
		$password="1234";
		$database="thesis";
		$connection = mysqli_connect($host,$user,$password,$database);
	
		if ( $connection )
		{
			//utility::addLog(__METHOD__.' : Verbindung erfolgreich.');
			//echo 'Verbindung erfolgreich: ';
			//print_r( $connection);
		}
		else
		{
			utility::addLog('keine Verbindung möglich:');
			utility::addLog(''.mysqli_error());
			//die('keine Verbindung möglich: ' . mysqli_error());
		}
	
	
		if ($connection->connect_error) {
			utility::addLog('Connection failed:');
			utility::addLog($connection->connect_error);
			//die("Connection failed: " . $connection->connect_error);
		}
	
		//set charset
		mysqli_set_charset($connection, 'utf8');
	
		
		
		//Example SQL Select
		/*
		$sql = "SELECT * FROM WF_USER";
		mysqli_query($connection, $sql) or die('Error selecting table WF_USER.');
	
	
		// Check connection
		if ($connection->connect_error) {
			die("Connection failed: " . $db_link->connect_error);
		}
		*/
		return $connection;
		/*
		 $host="localhost";
		 $user="root";
		 $password="";
		 $con=mysql_connect($host,$user,$password);
		 if(!$con) {
		 echo '<h1>Connected to MySQL</h1>';
		 //if connected then Select Database.
		 $db=mysql_select_db("YOUR_DATABASE_NAME",$con);
		 $query=mysql_query("YOUR_MYSQL_QUERY",$db);
		 }
		 else {
		 echo '<h1>MySQL Server is not connected</h1>';
		 }
		 */
	}
	
	/**
	 * Führt das mitgegebene SQL aus. Orientiert sich vom Aufruf her am FIVE Webflow,
	 * damit die Funktionen gleich genutzt werden können.
	 * @param string $sql
	 * @param boolean $asArray
	 * @return array
	 */
	function executeSQL($sql, $asArray=false){

//print_r('hallefluhy iah weisls doch nicht mwehr saish caahewi jwoi');
//print_r($sql);
		$db = new db();
		//$connection = $this->connection;
//print_r($db);
//print_r('<br />');
		$connection = $db->dbconnect();
//print_r($connection);
		//mysqli_query($connection, $sql) or die('Fehler beim Ausführen des SQL Statements');
		
		//TODO Unterscheiden
		if($asArray){
			$result = $connection->query($sql);
		}else{
			$result = $connection->query($sql);
		}
//print_r($result);

		
		if ($result->num_rows > 0) {
//print_r('return this:');
//print_r($result);
			$resultArray = array();
//print_r($result->fetch_assoc());
			while($row = $result->fetch_assoc()) {
				$resultArray[] = $row;
				//echo "USERID: " . $row["USERID"]. " - NAME: " . $row["NAME"]. " - FULLNAME: " . $row["FULLNAME"]. "<br>";
			}
//print_r($resultArray);
			return $resultArray;
		} else {
			return array();
			//echo "Sorry, 0 results found";
		}
		
		
		//Example SQL Select
		/*
		 $sql = "SELECT * FROM WF_USER";
		 mysqli_query($connection, $sql) or die('Error selecting table WF_USER.');
		
		
		 // Check connection
		 if ($connection->connect_error) {
		 die("Connection failed: " . $db_link->connect_error);
		 }
		 */
		/*
		$connection = $this->$connection;
		mysqli_query($connection, $sql) or die('Error executing sql script');
	
	
		$result = $connection->query($sql);
	
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				//echo "USERID: " . $row["USERID"]. " - NAME: " . $row["NAME"]. " - FULLNAME: " . $row["FULLNAME"]. "<br>";
				$rs[] = $row;
			}
		} else {
			$rs = false;
		}
		$connection->close();
		return $rs;
		*/
	}
	
}//end class db