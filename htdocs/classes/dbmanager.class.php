<?PHP
/**
* Database Manager
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class DBManager implements ISingleton	{
	
	static private $instance;
	const configFile = "db.conf";
	private $dbCfg;
	private $handle;
	private $queryCount = 0;
	private $queries  = array();
	private $isOpen = false;
	private $logQueries = false;
	
	/**
	* Get instance with this very nice singleton design pattern
	*/
	static public function getInstance()
	{
		if (!self::$instance)
		{
			$className = __CLASS__;
			self::$instance = new $className();
		}
		return self::$instance;
	}

	/**
	* No cloning
	*/
	public function __clone()
	{
		throw new EException(__CLASS__." ist nicht klonbar!");
	}

	/**
	* The constructor (is private so getInstance() must be used)
	*/
	private function __construct()
	{
	}

	private function loadConfig() {
		$this->dbCfg = fetchJsonConfig(self::configFile);
	}
	
	function getConfigFile() {
		return self::configFile;
	}
	
	function getHost() {
		if ($this->dbCfg == null) {
			$this->loadConfig();
		}	
		return $this->dbCfg['host'];
	}	

	function getDbName() {
		if ($this->dbCfg == null) {
			$this->loadConfig();
		}	
		return $this->dbCfg['dbname'];
	}	

	private function getUser() {
		if ($this->dbCfg == null) {
			$this->loadConfig();
		}	
		return $this->dbCfg['user'];
	}	
	
	private function getPassword() {
		if ($this->dbCfg == null) {
			$this->loadConfig();
		}	
		return $this->dbCfg['password'];
	}		
	
	/**
	* Baut die Datenbankverbindung auf
	*/
	function connect($throwError = 1, $tempCfg=null)
	{
		if ($this->dbCfg == null && $tempCfg == null) {
			$this->loadConfig();
		}
		try
		{
			if (is_array($tempCfg) && count($tempCfg) > 0)
				$dbCfg = $tempCfg;
			else
				$dbCfg = $this->dbCfg;
			if (!$this->handle = @mysql_connect($dbCfg['host'], $dbCfg['user'], $dbCfg['password']))
			{
				if ($throwError==1)
					throw new DBException("Zum Datenbankserver auf <b>".$dbCfg['host']."</b> kann keine Verbindung hergestellt werden!");
				else
					return false;
			}
			if (!mysql_select_db($dbCfg['dbname']))
			{
				if ($throwError==1)
					throw new DBException("Auf die Datenbank <b>".$dbCfg['dbname']."</b> auf <b>".$dbCfg['host']."</b> kann nicht zugegriffen werden!");
				else
					return false;
			}
			$this->isOpen = true;
			dbquery("SET NAMES 'utf8';"); 
			return true;		
		}
		catch (DBException $e)
		{
			$this->writeMsgToErrorLog($e->getErrStr());
			throw $e;
		}
	}

	/**
	* Trennt die Datenbankverbindung
	*/
	function close()
	{
		if ($this->logQueries)
		{
			echo "Queries done: ".$this->queryCount."<br/>";
			foreach ($this->queries as $q)
			{
				echo "<b>".$q[0]."</b><br/>".($q[1])."<br/>";
				$res = mysql_query("EXPLAIN ".$q[0]."");
				$this->drawQueryResult($res);
				echo "<br/>";
			}
		}
		if (isset($res))
		{
			@mysql_free_result($res);
		}
		@mysql_close($this->handle);
		unset($this->handle);
		$this->isOpen = false;
	}

	/**
	* Führt eine Datenbankabfrage aus
	*
	* @param string $string SQL-Abfrage
	* #param int $fehler Erzwing Fehleranzeige, Standard: 1
	*/
	function query($string, $fehler=1)
	{
		if (!$this->isOpen) {
			$this->connect();			
		}
			
		$this->queryCount++;
		if ($this->logQueries && stristr($string,"SELECT"))
		{
			ob_start();
			debug_print_backtrace();
			$this->queries[] = array($string,ob_get_clean());
		}
		if ($result = mysql_query($string, $this->handle))
			return $result;
		elseif ($fehler==1)
		{
			try
			{
				throw new DBException($string);	
			}
			catch (DBException $e)
			{
				$this->writeMsgToErrorLog($e->getErrStr());
				throw $e;
			}
		}
	}

	/**
	* Executes an sql query savely and protects agains SQL injections
	*
	* @param string $query SQL-Query
	* @param array $params Array of arguments
	*/
	function safeQuery($query, $params=array()) 
	{
		if (is_array($params) && count($params)>0) 
		{
			foreach ($params as &$v) 
			{ 
				$v = $this->escapeStr($v); 
			}    
			# Escaping parameters
			# str_replace - replacing ? -> %s. %s is ugly in raw sql query
			# vsprintf - replacing all %s to parameters
			$sql = vsprintf( str_replace("?","'%s'",$query), $params );   
		} 
		else 
		{
			$sql = $query;    # If no params...
		}
		return $this->query($sql);
	} 

	/**
	 * Prepares a user string for sql queries and
	 * escapes all malicious characters, e.g. '
	 * 
	 * @param string $string
	 * @return string
	 */
	function escapeStr($string)
	{
		$string = trim($string);
		if(get_magic_quotes_gpc())
			$string = stripslashes($string);
		return mysql_real_escape_string($string);
	}

	function drawQueryResult($res)
	{
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\"><tr>";
			for ($x=0;$x<mysql_num_fields($res);$x++)
			{
				echo "<th>".mysql_field_name($res,$x)."</th>";
			}
			echo "</tr>";
			while ($arr=mysql_fetch_row($res))
			{
				echo "<tr>";
				foreach ($arr as $a)
				{
					echo "<td>".$a."</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}
		else
		{
			echo "No result!<br/>";
		}
	}

	function getArrayFromTable($table,$field)
	{
		$r = array();
		$res = $this->query("
		SELECT
			`".$field."`
		FROM
			`".$table."`
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_row($res))
			{
				$r[] = $arr[0];
			}
		}
		return $r;
	}

	function explainQuery($sql)
	{
		echo "Explaining: $sql";
		$res = $this->query("EXPLAIN ".$sql."");
		$this->drawQueryResult($res);
	}
	
	/**
	* Tabellen optimieren
	*/
	function optimizeTables($manual=false)
	{
		$res = $this->query("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = $this->query("OPTIMIZE TABLE ".$tbls.";");
		if ($manual)
		{
			add_log("4",$n." Tabellen wurden manuell optimiert!",time());
			return $ores;
		}
		else
		{
			add_log("4",$n." Tabellen wurden optimiert!",time());
			return $n;
		}
	}

	/**
	* Tabellen reparieren
	*/
	function repairTables($manual=false)
	{
		$res = $this->query("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = $this->query("REPAIR TABLE ".$tbls.";");
		if ($manual)
		{
			add_log("4",$n." Tabellen wurden manuell repariert!",time());
			return $ores;
		}
		else
		{
			add_log("4",$n." Tabellen wurden repariert!",time());
			return $n;
		}	
	}

	/**
	* Tabellen prüfen
	*/
	function checkTables()
	{
		$res = $this->query("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = $this->query("CHECK TABLE ".$tbls.";");
		return $ores;
	}
	
	/**
	* Tabellen analysieren
	*/
	function analyzeTables()
	{
		$res = $this->query("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = $this->query("ANALYZE TABLE ".$tbls.";");
		return $ores;
	}
	
	public function backupDB($backupDir, $gzip)
	{
		$mysqldump = WINDOWS ? WINDOWS_MYSQLDUMP_PATH : "mysqldump";
		
		if (is_dir($backupDir)) 
		{		
			$file = $backupDir."/".$this->getDbName()."-".date("Y-m-d-H-i").".sql";
			
			if ($gzip)
			{
				if (!UNIX)
				{
					throw new Exception("Das Erstellen von GZIP Backups wird nur auf UNIX Systemen unterstützt!");
				}			
				$file.=".gz";
				$cmd = $mysqldump." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." ".$this->getDbName()." | gzip > ".$file;
			}
			else
			{
				$cmd = $mysqldump." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." ".$this->getDbName()." > ".$file;
			}
			$result = shell_exec($cmd);
			if (!empty($result))
			{
				throw new Exception("Fehler beim Erstellen der Backup-Datei ".$file.": ".$result);					
			}
			return "Backup ".$file." erstellt, Dateigrösse: ".byte_format(filesize($file));
		}
		else
		{
			throw new Exception("Das Backup Verzeichnis ".$backupDir." existiert nicht!");
		}
	}
	
	public function restoreDB($backupDir, $restorePoint)
	{
		$mysql = WINDOWS ? WINDOWS_MYSQL_PATH : "mysql";
		
		if (is_dir($backupDir)) 
		{
			$file = $backupDir."/".$this->getDbName()."-".$restorePoint.".sql";
			if (file_exists($file.".gz"))
			{
				if (!UNIX)
				{
					throw new Exception("Das Entpacken von GZIP Backups wird nur auf UNIX Systemen unterstützt!");
				}
				$cmd = "gunzip < ".$file.".gz | ".$mysql." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." ".$this->getDbName();
			}
			elseif (file_exists($file))
			{
				$cmd = $mysql." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." ".$this->getDbName()." < ".$file;
			}
			if (isset($cmd))
			{
				$result = shell_exec($cmd);
				if (!empty($result))
				{
					throw new Exception("Error while restoring backup: ".$result);
				}
				return "Die Datenbank wurde vom Backup [b]".$restorePoint."[/b] aus dem Verzeichnis [b]".$backupDir."[/b] wiederhergestellt.";
			}
			else
			{
				throw new Exception("Backup file $file not found!");	
			}
		}
		else
		{
			throw new Exception("Backup directory $backupDir does not exist!");
		}
	}	
	
	public function getBackupImages($dir, $strip=1)
	{
		$bfiles=array();
		if ($dir != null && is_dir($dir)) 
		{
			if ($d = opendir($dir))
			{
				while ($f = readdir($d))
				{
					if (is_file($dir."/".$f) && stristr($f,".sql") && preg_match('/^'.$this->getDbName().'/i',$f)==1)
					{
						if ($strip == 0)
							array_push($bfiles, $f);
						else
							array_push($bfiles, preg_replace(array('/\.sql(.gz)?$/', '/^'.$this->getDbName().'-/'), array('', ''), $f));
					}
				}
				rsort($bfiles);
			}
		}
		return $bfiles;
	}
	
	/**
	* Returns the backup directory path, if it exists
	*/
	public static function getBackupDir() {
		$cfg = Config::getInstance();
		$backupDir = $cfg->backup_dir;
		if (!empty($backupDir) && is_dir($backupDir)) {
			return $backupDir;
		}
		return null;
	}
	
	/**
	* Removes old backup files
	* @return The number of removed files
	*/
	public static function removeOldBackups($dir, $days) {
		$deleted = 0;
		$time = time();
		$files = array_merge(glob($dir."/*.sql"), glob($dir."/*.sql.gz"));
		foreach ($files as $f) {
			if (is_file($f) && $time - filemtime($f) >= 86400 * $days) {
				unlink($f);
				$deleted++;
			}
		}
		return $deleted;
	}
	
	public function getDbSize() {
		$res = $this->safeQuery("
			SELECT round(sum( data_length + index_length ) / 1024 / 1024,2)
			FROM information_schema.TABLES
			WHERE table_schema=?
			GROUP BY table_schema", array($this->getDbName()));
		$arr = mysql_fetch_row($res);
		return $arr[0];
	}

	/**
	* Writes a message to error log
	*/
	private function writeMsgToErrorLog($message) {
		if (defined('ERROR_LOGFILE'))
		{
			global $cu;
			if (!file_exists(DBERROR_LOGFILE))
			{
				touch(DBERROR_LOGFILE);
				chmod(DBERROR_LOGFILE,0662);
			}
			$f = fopen(DBERROR_LOGFILE,"a+");
			fwrite($f,date("d.m.Y H:i:s").", ".(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']:'local').", ".$cu."\n".$message."\n\n");
			fclose($f);
		}
	}
}
?>