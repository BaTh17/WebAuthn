
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
		//_plugin_utility::addLog(__METHOD__.' : Verbindungsaufbau mit dbconnect() gestartet');
		//connect to mysql DB
		$host="localhost";
		$user="webflow";
		$password="1234";
		$database="thesis";
		$connection = mysqli_connect($host,$user,$password,$database);
	
		if ( $connection )
		{
			//_plugin_utility::addLog(__METHOD__.' : Verbindung erfolgreich.');
		}else{
			_plugin_utility::addLog('keine Verbindung möglich:');
			_plugin_utility::addLog(''.mysqli_error());
			//die('keine Verbindung möglich: ' . mysqli_error());
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
