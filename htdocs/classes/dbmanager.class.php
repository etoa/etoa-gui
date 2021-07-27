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
    private $isOpen = false;

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

        if ($result = mysql_query($string, $this->handle)) {
            return $result;
        } elseif ($fehler == 1) {
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

    public function setDatabaseConfig(array $config)
    {
        $this->dbCfg = $config;
    }
}
