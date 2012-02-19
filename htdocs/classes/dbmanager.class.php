<?PHP
/**
* Database Manager
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class DBManager implements ISingleton	{
	
	static private $instance;
	const configFile = "db.conf";
	private $cfg;
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
		$this->cfg = fetchJsonConfig(self::configFile);
	}
	
	function getConfigFile() {
		return self::configFile;
	}
	
	function getHost() {
		if ($this->cfg == null) {
			$this->loadConfig();
		}	
		return $this->cfg['host'];
	}	

	function getDbName() {
		if ($this->cfg == null) {
			$this->loadConfig();
		}	
		return $this->cfg['dbname'];
	}	
	
	/**
	* Baut die Datenbankverbindung auf
	*/
	function connect($throwError = 1, $tempCfg=null)
	{
		if ($this->cfg == null) {
			$this->loadConfig();
		}
		try
		{
			if (is_array($tempCfg) && count($tempCfg) > 0)
				$cfg = $tempCfg;
			else
				$cfg = $this->cfg;
			if (!$this->handle = @mysql_connect($cfg['host'], $cfg['user'], $cfg['password']))
			{
				if ($throwError==1)
					throw new DBException("Zum Datenbankserver auf <b>".$cfg['host']."</b> kann keine Verbindung hergestellt werden!");
				else
					return false;
			}
			if (!mysql_select_db($cfg['dbname']))
			{
				if ($throwError==1)
					throw new DBException("Auf die Datenbank <b>".$cfg['dbname']."</b> auf <b>".$cfg['host']."</b> kann nicht zugegriffen werden!");
				else
					return false;
			}
			$this->isOpen = true;
			dbquery("SET NAMES 'utf8';"); 
			return true;		
		}
		catch (DBException $e)
		{
			echo $e;
			exit;
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
				echo $e;
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
	
	public function backup()
	{
		if ($this->cfg == null) {
			$this->loadConfig();
		}	
		$cfg = Config::getInstance();
		$rtn = false;
					
		if (UNIX)
		{
			$log = "Starte Backup...\n";
			$tmr = timerStart();
			$log .= " Warte auf Mutex...";
			$mtx = new Mutex();
			$mtx->acquire();
			$log .= " Mutex erhalten in ".timerStop($tmr)."s, beginne Backup...\n\n";
			$tmr = timerStart();				
			
			// Alte Backups löschen
			$cmd = "find ".BACKUP_DIR." -name *.sql.gz -mtime +".$cfg->p1('backup')." -exec rm {} \;";
			passthru($cmd);
			$cmd = "find ".BACKUP_DIR." -name *.sql -mtime +".$cfg->p1('backup')." -exec rm {} \;";
			passthru($cmd);
	
			$file = BACKUP_DIR."/".$this->getDbName()."-".date("Y-m-d-H-i");
			$file_wo_path = $this->getDbName()."-".date("Y-m-d-H-i");
			$result = shell_exec("mysqldump -u".$this->cfg['user']." -p".$this->cfg['password']." -h".$this->getHost()." ".$this->getDbName()." > ".$file.".sql");
			if ($result=="")
			{
				if ($cfg->p2('backup')==1)
				{
					$result = shell_exec("gzip ".$file.".sql");
					if ($result!="")
					{
						echo "Error while zipping Backup-Dump $file: $result\n";
					}
					else
					{
						$log.= "GZIP Backup erstellt! Grösse: ".byte_format(filesize($file.".sql.gz"));
						$rtn = true;
					}
				}
				else
				{
					$log.= "Backup erstellt! Grösse: ".byte_format(filesize($file.".sql"));
					$rtn = true;
				}
			}
			else
			{
				echo "Error while creating Backup-Dump $file: $result\n";		
				$log.= "FEHLER beim erstellen der Datei $file: $result";					
			}
			add_log (15,"[b]Backup[/b]\nGesamtdauer: ".timerStop($tmr)."\n\n".$log);			
			$mtx->release();					
		}
		else
		{
			echo "Die Backup-Funktion ist nur auf UNIX-Systemen verfügbar!";
		}
		return $rtn;
	}
	
	public function restore($arg)
	{
		if ($this->cfg == null) {
			$this->loadConfig();
		}	
		$rtn = false;
		if (UNIX)
		{
			$mtx = new Mutex();
			$mtx->acquire();
			$file = BACKUP_DIR."/".$this->getDbName()."-".$arg;
			if (file_exists($file.".sql.gz"))
			{
				$result = shell_exec("gunzip ".$file.".sql.gz");
				if ($result=="")
				{
					$result = shell_exec("mysql -u".$this->cfg['user']." -p".$this->cfg['password']." -h".$this->getHost()." ".$this->getDbName()." < ".$file.".sql");
					if ($result!="")
					{
						echo "Error while restoring backup: $result\n";
					}
					else
						$rtn = true;
					shell_exec("gzip ".$file.".sql");
				}
				else
					echo "Error while unzipping Backup-Dump $file: $result\n";
			}
			elseif (file_exists($file.".sql"))
			{
				$result = shell_exec("mysql -u".$this->cfg['user']." -p".$this->cfg['password']." -h".$this->getHost()." ".$this->getDbName()." < ".$file.".sql");
				if ($result!="")
					echo "Error while restoring backup: $result\n";
				else
					$rtn = true;
			}
			else
			{
				echo "Error: File $file not found!\n";	
			}
			
			add_log (15,"[b]Datenbank-Restore[/b]\n\nDie Datenbank wurde von der Quelle [b]".$file."[/b] wiederhergestellt!\n");			
			$mtx->release();			
		}
		else
		{
			echo "Die Backup-Funktion ist nur auf UNIX-Systemen verfügbar!";
		}
		return $rtn;
	}	
	
	public function getBackupImages()
	{
		if ($this->cfg == null) {
			$this->loadConfig();
		}	
		if (UNIX)
		{
			$cfg = Config::getInstance();
			if ($d = @opendir($cfg->backup))
			{
				$bfiles=array();
				while ($f = readdir($d))
				{
					if (is_file($cfg->backup."/".$f) && stristr($f,".sql") && preg_match('/^'.$this->getDbName().'/i',$f)==1)
					{
						array_push($bfiles, preg_replace('/\.sql$/', '', $f));
					}
				}
				rsort($bfiles);
				return $bfiles;
			}
		}
		else
		{
			echo "Die Backup-Funktion ist nur auf UNIX-Systemen verfügbar!";
		}
	}	
}
?>