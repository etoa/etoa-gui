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

    function connect(?array $tempCfg = null): void
    {
        if ($this->dbCfg == null && $tempCfg == null) {
            $this->loadConfig();
        }

        if (is_array($tempCfg) && count($tempCfg) > 0) {
            $this->dbCfg = $tempCfg;
        }
        $dbCfg = $this->dbCfg;

        if (!$this->handle = @mysql_connect($dbCfg['host'], $dbCfg['user'], $dbCfg['password'], $dbCfg['dbname'])) {
            throw new DBException("Zum Datenbankserver auf <b>" . $dbCfg['host'] . "</b> kann keine Verbindung hergestellt werden!");
        }

        $this->isOpen = true;

        $this->query("SET NAMES 'utf8';");
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
     * #param int $fehler Erzwingt Fehleranzeige, Standard: 1
     */
    function query($string, $fehler = 1)
    {
        if (!$this->isOpen) {
            $this->connect();
        }

        if ($result = mysql_query($string, $this->handle)) {
            return $result;
        }
        if ($fehler == 1) {
            throw new DBException($string);
        }
    }

    public function setDatabaseConfig(array $config)
    {
        $this->dbCfg = $config;
    }
}
