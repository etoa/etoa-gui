<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/**
 * Database Manager
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class DBManager implements ISingleton
{

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
     * @return DBManager
     */
    static public function getInstance()
    {
        if (!self::$instance) {
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
        throw new EException(__CLASS__ . " ist nicht klonbar!");
    }

    /**
     * The constructor (is private so getInstance() must be used)
     */
    private function __construct()
    {
    }

    private function loadConfig()
    {
        $this->dbCfg = fetchJsonConfig(self::configFile);
    }

    function getConfigFile()
    {
        return self::configFile;
    }

    function getHost()
    {
        if ($this->dbCfg == null) {
            $this->loadConfig();
        }

        return explode(':', $this->dbCfg['host'])[0];
    }

    function getPort()
    {
        if ($this->dbCfg == null) {
            $this->loadConfig();
        }

        return explode(':', $this->dbCfg['host'], 2)[1] ?? 3306;
    }

    function getDbName()
    {
        if ($this->dbCfg == null) {
            $this->loadConfig();
        }
        return $this->dbCfg['dbname'];
    }

    private function getUser()
    {
        if ($this->dbCfg == null) {
            $this->loadConfig();
        }
        return $this->dbCfg['user'];
    }

    private function getPassword()
    {
        if ($this->dbCfg == null) {
            $this->loadConfig();
        }
        return $this->dbCfg['password'];
    }

    /**
     * Baut die Datenbankverbindung auf
     */
    function connect($throwError = 1, $tempCfg = null)
    {
        if ($this->dbCfg == null && $tempCfg == null) {
            $this->loadConfig();
        }
        try {
            if (is_array($tempCfg) && count($tempCfg) > 0) {
                $this->dbCfg = $tempCfg;
            }
            $dbCfg = $this->dbCfg;
            if (!$this->handle = @mysql_connect($dbCfg['host'], $dbCfg['user'], $dbCfg['password'], $dbCfg['dbname'])) {
                if ($throwError == 1)
                    throw new DBException("Zum Datenbankserver auf <b>" . $dbCfg['host'] . "</b> kann keine Verbindung hergestellt werden!");
                else
                    return false;
            }
            $this->isOpen = true;
            dbquery("SET NAMES 'utf8';");
            return true;
        } catch (DBException $e) {
            $this->writeMsgToErrorLog($e->getErrStr());
            throw $e;
        }
    }

    /**
     * Trennt die Datenbankverbindung
     */
    function close()
    {
        if ($this->logQueries) {
            echo "Queries done: " . $this->queryCount . "<br/>";
            foreach ($this->queries as $q) {
                echo "<b>" . $q[0] . "</b><br/>" . ($q[1]) . "<br/>";
                $res = mysql_query("EXPLAIN " . $q[0] . "");
                $this->drawQueryResult($res);
                echo "<br/>";
            }
        }
        if (isset($res)) {
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
    function query($string, $fehler = 1)
    {
        if (!$this->isOpen) {
            $this->connect();
        }

        $this->queryCount++;
        if ($this->logQueries && stristr($string, "SELECT")) {
            ob_start();
            debug_print_backtrace();
            $this->queries[] = array($string, ob_get_clean());
        }
        if ($result = mysql_query($string, $this->handle))
            return $result;
        elseif ($fehler == 1) {
            try {
                throw new DBException($string);
            } catch (DBException $e) {
                $this->writeMsgToErrorLog($e->getErrStr());
                throw $e;
            }
        }
    }

    /**
     * Executes an sql query savely and protects agains SQL injections
     *
     * @param string $query SQL-Query
     * @param ?array $params Array of arguments
     */
    function safeQuery($query, $params = array())
    {
        if (is_array($params) && count($params) > 0) {
            foreach ($params as &$v) {
                $v = $this->escapeStr($v);
            }
            # Escaping parameters
            # str_replace - replacing ? -> %s. %s is ugly in raw sql query
            # vsprintf - replacing all %s to parameters
            $sql = vsprintf(str_replace("?", "'%s'", $query), $params);
        } else {
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
        if (get_magic_quotes_gpc())
            $string = stripslashes($string);
        return mysql_real_escape_string($string);
    }

    function drawQueryResult($res)
    {
        if (mysql_num_rows($res) > 0) {
            echo "<table class=\"tb\"><tr>";
            for ($x = 0; $x < mysql_num_fields($res); $x++) {
                echo "<th>" . mysql_field_name($res, $x) . "</th>";
            }
            echo "</tr>";
            while ($arr = mysql_fetch_row($res)) {
                echo "<tr>";
                foreach ($arr as $a) {
                    echo "<td>" . $a . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No result!<br/>";
        }
    }

    function getArrayFromTable($table, $field, $sort = null)
    {
        $r = array();
        $order = $sort ? ' ORDER BY `' . $sort . '` ASC' : '';
        if (is_array($field)) {
            $res = $this->query("
			SELECT
				`" . implode('`,`', $field) . "`
			FROM
				`" . $table . "`
			$order
			");
            if (mysql_num_rows($res) > 0) {
                while ($arr = mysql_fetch_row($res)) {
                    $r[] = $arr;
                }
            }
        } else {
            $res = $this->query("
			SELECT
				`" . $field . "`
			FROM
				`" . $table . "`
			$order
			");
            if (mysql_num_rows($res) > 0) {
                while ($arr = mysql_fetch_row($res)) {
                    $r[] = $arr[0];
                }
            }
        }
        return $r;
    }

    /**
     * Returns the backup directory path, if it exists
     */
    public static function getBackupDir()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $backupDir = $config->get('backup_dir');
        if ($backupDir) {
            if (is_dir($backupDir)) {
                return $backupDir;
            }
        } else {
            $backupDir = RELATIVE_ROOT . '../backup';
            if (is_dir($backupDir)) {
                return $backupDir;
            }
        }
        return null;
    }

    /**
     * Removes old backup files
     * @return int The number of removed files
     */
    public static function removeOldBackups($dir, $days)
    {
        $deleted = 0;
        $time = time();
        $files = array_merge(glob($dir . "/*.sql"), glob($dir . "/*.sql.gz"));
        foreach ($files as $f) {
            if (is_file($f) && $time - filemtime($f) >= 86400 * $days) {
                unlink($f);
                $deleted++;
            }
        }
        return $deleted;
    }

    /**
     * Writes a message to error log
     */
    private function writeMsgToErrorLog($message)
    {
        if (defined('ERROR_LOGFILE')) {
            global $cu;
            if (!file_exists(DBERROR_LOGFILE)) {
                touch(DBERROR_LOGFILE);
                chmod(DBERROR_LOGFILE, 0662);
            }
            $f = fopen(DBERROR_LOGFILE, "a+");

            $cu = $cu instanceof \EtoA\Admin\AdminUser ? $cu->nick : $cu;
            fwrite($f, date("d.m.Y H:i:s") . ", " . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'local') . ", " . $cu . "\n" . $message . "\n\n");
            fclose($f);
        }
    }

    /**
     * Creates a list of all tables
     */
    public function getAllTables()
    {
        $res = $this->query("SHOW TABLES;");
        $tbls = array();
        while ($arr = mysql_fetch_row($res)) {
            $tbls[] = $arr[0];
        }
        return $tbls;
    }

    /**
     * Drops all tables
     */
    public function dropAllTables()
    {
        $tbls = $this->getAllTables();
        if (count($tbls) > 0) {
            dbquery("SET FOREIGN_KEY_CHECKS=0;");
            dbquery("DROP TABLE " . implode(',', $tbls) . ";");
            dbquery("SET FOREIGN_KEY_CHECKS=1;");
        }
        return count($tbls);
    }

    public function setDatabaseConfig(array $config)
    {
        $this->dbCfg = $config;
    }
}
