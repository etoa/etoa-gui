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

	const SCHEMA_MIGRATIONS_TABLE = "schema_migrations";

	/**
	* Get instance with this very nice singleton design pattern
     * @return DBManager
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

		return explode(':', $this->dbCfg['host'])[0];
	}

    function getPort() {
        if ($this->dbCfg == null) {
            $this->loadConfig();
        }

        return explode(':', $this->dbCfg['host'], 2)[1] ?? 3306;
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
			if (is_array($tempCfg) && count($tempCfg) > 0) {
				$this->dbCfg = $tempCfg;
			}
			$dbCfg = $this->dbCfg;
			if (!$this->handle = @mysql_connect($dbCfg['host'], $dbCfg['user'], $dbCfg['password'], $dbCfg['dbname']))
			{
				if ($throwError==1)
					throw new DBException("Zum Datenbankserver auf <b>".$dbCfg['host']."</b> kann keine Verbindung hergestellt werden!");
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
		if (!$this->isOpen) {
			$this->connect();
		}
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

	function getArrayFromTable($table,$field,$sort=null)
	{
		$r = array();
		$order = !empty($sort) ? ' ORDER BY `'.$sort.'` ASC' : '';
		if (is_array($field)) {
			$res = $this->query("
			SELECT
				`".implode('`,`', $field)."`
			FROM
				`".$table."`
			$order
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_row($res))
				{
					$r[] = $arr;
				}
			}
		} else {
			$res = $this->query("
			SELECT
				`".$field."`
			FROM
				`".$table."`
			$order
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_row($res))
				{
					$r[] = $arr[0];
				}
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
				$cmd = $mysqldump." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." -P".$this->getPort()." --default-character-set=utf8 ".$this->getDbName()." | gzip > ".$file;
			}
			else
			{
				$cmd = $mysqldump." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." -P".$this->getPort()." --default-character-set=utf8 ".$this->getDbName()." -r ".$file;
			}

			if (WINDOWS && !file_exists($mysqldump) || UNIX && !unix_command_exists($mysqldump))
			{
				$this->dumpIntoFile($file);
			}
			else
			{
				$result = shell_exec($cmd);
				if (!empty($result))
				{
					throw new Exception("Fehler beim Erstellen der Backup-Datei ".$file.": ".$result);
				}
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
		if (is_dir($backupDir))
		{
			$file = $backupDir."/".$this->getDbName()."-".$restorePoint.".sql";
			if (file_exists($file.".gz"))
			{
				$file = $file.".gz";
			}
			$this->restoreDBFromFile($file);
			return "Die Datenbank wurde vom Backup [b]".$restorePoint."[/b] aus dem Verzeichnis [b]".$backupDir."[/b] wiederhergestellt.";
		}
		else
		{
			throw new Exception("Backup directory $backupDir does not exist!");
		}
	}

	public function restoreDBFromFile($file)
	{
		if (file_exists($file))
		{
			try {
				$this->loadFile($file);
				return "Die Datenbank wurde aus der Datei [b]".$file."[/b] wiederhergestellt.";
			}
			catch (Exception $e)
			{
				throw new Exception("Error while restoring backup: ".$e->getMessage());
			}
		}
		else
		{
			throw new Exception("Backup file $file not found!");
		}
	}

	private function loadFile($file) {
		$mysql = WINDOWS ? WINDOWS_MYSQL_PATH : "mysql";
        $mysqldump = WINDOWS ? WINDOWS_MYSQLDUMP_PATH : "mysqldump";
		if (file_exists($file))
		{
			$ext = pathinfo ($file, PATHINFO_EXTENSION);
			if ($ext == "gz")
			{
				if (!UNIX)
				{
					throw new Exception("Das Laden von GZIP SQL Dateien wird nur auf UNIX Systemen unterstützt!");
				}
				$cmd = "gunzip < ".$file.".gz | ".$mysql." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." -P".$this->getPort()." --default-character-set=utf8 ".$this->getDbName();
			}
			else
			{
				$cmd = $mysql." -u".$this->getUser()." -p".$this->getPassword()." -h".$this->getHost()." -P".$this->getPort()." --default-character-set=utf8 ".$this->getDbName()." < ".$file;
			}

			if (WINDOWS && !file_exists($mysql) || UNIX && !unix_command_exists($mysqldump))
			{
				$this->importFromFile($file);
			}
			else
			{
				$result = shell_exec($cmd);
				if (!empty($result))
				{
					throw new Exception("Error while loading file with MySQL: ".$result);
				}
			}
		}
		else
		{
			throw new Exception("File $file not found!");
		}
	}

	/**
	* Import SQL file using PHP functionality only
	*/
	private function importFromFile($file, $delimiter = ';')
	{
		set_time_limit(0);
		if (is_file($file) === true)
		{
			$file = fopen($file, 'r');
			if (is_resource($file) === true)
			{
				$query = array();
				while (feof($file) === false)
				{
					$query[] = fgets($file);
					if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1)
					{
						$query = trim(implode('', $query));
						$this->query($query, 1);
					}
					if (is_string($query) === true)
					{
						$query = array();
					}
				}
				return fclose($file);
			}
		}
		return false;
	}

	/**
	* Import database or tables to SQL file using PHP functionality only
	*/
	private function dumpIntoFile($file, $tables = '*')
	{
		//get all of the tables
		if($tables == '*')
		{
			$tables = array();
			$result = $this->query('SHOW TABLES');
			while($row = mysql_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}

		//cycle through
		foreach($tables as $table)
		{
			$result = $this->query('SELECT * FROM '.$table);
			$num_fields = mysql_num_fields($result);

			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysql_fetch_row($this->query('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";

			for ($i = 0; $i < $num_fields; $i++)
			{
				while($row = mysql_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++)
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = str_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j<($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}

		//save file
		$handle = fopen($file, 'w+');
		fwrite($handle,$return);
		fclose($handle);
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
		$backupDir = $cfg->backup_dir->v;
		if (!empty($backupDir)) {
			if (is_dir($backupDir)) {
				return $backupDir;
			}
		} else {
			$backupDir = RELATIVE_ROOT.'../backup';
			if (is_dir($backupDir)) {
				return $backupDir;
			}
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

	/**
	* Creates a list of all tables
	*/
	public function getAllTables() {
		$res = $this->query("SHOW TABLES;");
		$tbls = array();
		while ($arr = mysql_fetch_row($res))
		{
			$tbls[] = $arr[0];
		}
		return $tbls;
	}

	/**
	* Drops all tables
	*/
	public function dropAllTables() {
		$tbls = $this->getAllTables();
		if (count($tbls) > 0) {
			dbquery("SET FOREIGN_KEY_CHECKS=0;");
			dbquery("DROP TABLE ".implode(',', $tbls).";");
			dbquery("SET FOREIGN_KEY_CHECKS=1;");
		}
		return count($tbls);
	}

	public function migrate() {

		$res = $this->safeQuery("SELECT * FROM information_schema.TABLES WHERE table_schema=? AND table_name=?;", array($this->getDbName(), self::SCHEMA_MIGRATIONS_TABLE));
		if (!($arr = mysql_fetch_row($res))) {
			$this->loadFile(RELATIVE_ROOT.'../db/init_schema_migrations.sql');
		}

		$files = glob(RELATIVE_ROOT.'../db/migrations/*.sql');
		natsort($files);
		$cnt = 0;
		foreach ($files as $f) {
			$pi = pathinfo($f, PATHINFO_FILENAME);
			$res = $this->safeQuery("SELECT date FROM `".self::SCHEMA_MIGRATIONS_TABLE."` WHERE `version`=?;", array($pi));
			if (!($arr = mysql_fetch_row($res))) {
				echo $pi."\n";
				$this->loadFile($f);
				$this->safeQuery("INSERT INTO `".self::SCHEMA_MIGRATIONS_TABLE."` (`version`, `date`) VALUES (?, CURRENT_TIMESTAMP);", array($pi));
				$cnt++;
			}
		}
		return $cnt;
	}

	public function getPendingMigrations()
	{
		$files = glob(RELATIVE_ROOT.'../db/migrations/*.sql');
		natsort($files);
		$migrations = [];
		foreach ($files as $f) {
			$pi = pathinfo($f, PATHINFO_FILENAME);
			$res = $this->safeQuery("SELECT date FROM `".self::SCHEMA_MIGRATIONS_TABLE."` WHERE `version`=?;", array($pi));
			if (!($arr = mysql_fetch_row($res))) {
				$migrations[] = $pi;
			}
		}
		return $migrations;
	}

    public function setDatabaseConfig(array $config)
    {
		$this->dbCfg = $config;
	}
}
