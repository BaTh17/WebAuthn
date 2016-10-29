<?php


/**
 * Here are all functions for the serverside, that are used in different files of the thesis project.
* If you want to enable these functions, use require_once('utility.php'); in the first lines of your php
* script
*/
class utility {
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




	//get Policy ( 0,1,2)

	// create Policy (dropdown)


	//get Keys from WF_USERS
	//input ( string USERNAME)
	// return True / false


	// checkPassword (default)
	//


	//get Public KEy from USER (NAME)


	//log in session


	//create TAble

	/**
	 * erstellt eine Tabelle, param muss der Tabellenname sein
	 */
	function createTable($tableName)
	{
		utility::addLog('erstelle Tabelle: '.$tableName);
		$db = new db();
		$sql = "SELECT * FROM $tableName";
		$rs = $db->executeSQL();
		$htmlOutput = '';
		//Build HTML Table around result
		if($rs){
			$htmlOutput .= '
				<table id=table_"'.$tableName.'" style="width:100%;border=1">
					';
			foreach($rs as $key => $value){
				$htmlOutput .= '<tr>
						<td id='.$key.'>'.$key.'</td><td>'.$value.'</td>
					</tr>';
			}


			$htmlOutput .= '		</table>
				';
		}
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
	
	function executeMySQL($SQL) {
		
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
	
	
	
	
	function dbconnect(){
		//connect to mysql DB
		$host="localhost";
		$user="webflow";
		$password="1234";
		$database="thesis";
		$connection = mysqli_connect($host,$user,$password,$database);
	
		if ( $connection )
		{
			utility::addLog('Verbindung erfolgreich.');
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
	
		$sql = "SELECT * FROM WF_USER";
		mysqli_query($connection, $sql) or die('Error selecting table WF_USER.');
	
	
		// Check connection
		if ($connection->connect_error) {
			die("Connection failed: " . $db_link->connect_error);
		}
	
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
	
	function executeSQL($sql, $asArray=false){
		
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
	
}