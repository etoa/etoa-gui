<?php

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportTypes;

/**
 * Implements a report management system, replacing some of the
 * automatically generated ingame messages. This is an abstract basic class  based
 * on the table 'reports'. Every report type must implement it's own class inherited
 * from this class (and possibly have own table on an 1:1 relation with 'reports').
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
abstract class Report
{
    protected $valid = false;
    protected $type = ReportTypes::TYPE_OTHER;
    protected $id;
    protected $subject = "1";
    protected $timestamp;
    protected $content;
    protected $read = false;
    protected $deleted = false;
    protected $archived = false;
    protected $userId = 0;
    protected $allianceId = 0;
    protected $entity1Id = 0;
    protected $entity2Id = 0;
    protected $opponent1Id = 0;

    /**
     * Class constructor. To be called from the derived class.
     *
     * @param mixed $id Accepts a record id or an array of already fetched record data
     */
    public function __construct($id)
    {
        if (is_integer($id)) {
            $res = dbquery("
            SELECT
                *
            FROM
                reports
            WHERE
                id=" . intval($id) . "
            LIMIT 1;
            ");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_assoc($res);
            }
        } elseif (is_array($id)) {
            $arr = $id;
        }

        if (isset($arr)) {
            $this->id = intval($arr['id']);
            $this->timestamp = $arr['timestamp'];
            $this->type = $arr['type'];
            $this->read = $arr['read'] == 1;
            $this->deleted = $arr['deleted'] == 1;
            $this->userId = (int) $arr['user_id'];
            $this->allianceId = $arr['alliance_id'];
            $this->content = $arr['content'];
            $this->entity1Id = $arr['entity1_id'];
            $this->entity2Id = $arr['entity2_id'];
            $this->opponent1Id = $arr['opponent1_id'];

            $this->valid = true;
        }
    }

    /**
     * Class property getter
     *
     * @param string $field Property name
     * @return mixed Requested property value
     */
    function __get($field)
    {
        try {
            if ($field == "subject")
                return $this->createSubject();
            if (isset($this->$field))
                return $this->$field;
            throw new EException("Property $field does not exists!");
        } catch (EException $e) {
            echo $e;
        }
        return null;
    }

    function __set($field, $value)
    {
        try {
            if (isset($this->$field)) {
                if ($field == "read") {
                    $this->$field = $value;
                    dbquery("UPDATE reports SET `" . $field . "`=" . ($value ? 1 : 0) . " WHERE id=$this->id;");
                    return true;
                } elseif ($field == "deleted") {
                    $this->$field = $value;
                    dbquery("UPDATE reports SET `" . $field . "`=" . ($value ? 1 : 0) . " WHERE id=$this->id;");
                    return true;
                }
                throw new EException("Property $field is write protected!");
            }
            throw new EException("Property $field does not exists!");
        } catch (EException $e) {
            echo $e;
            return false;
        }
    }


    public abstract function createSubject();

    /**
     * Gets a list of reports
     *
     * @param string|array $where WHERE conditions where $arrayKey is database field name
     * and $arrayValue is database field value
     * @param string $order ORDER query string
     * @return array|int Array containing a list of reports
     */
    static function &find($where = null, $order = null, $limit = "", $count = 0, $admin = false, $join = "")
    {
        if ($order == null)
            $order = " timestamp DESC ";

        $wheres    = $admin ? "WHERE 1 " : " WHERE deleted=0 ";
        $archived = false;

        if (is_array($where)) {
            foreach ($where as $k => $v) {
                $wheres .= " AND `" . $k . "`='" . $v . "'";
                if ($k == "archived") $archived = true;
            }
            if (!$archived) $wheres .= " AND `archived`='false'";
        } elseif ($admin)
            $wheres .= $where;

        if ($count > 0) {
            $sql = "SELECT COUNT(id) FROM reports $join $wheres ORDER BY $order";
            if ($limit != "" || $limit > 0)
                $sql .= " LIMIT $limit";
            $res = dbquery($sql);
            $arr = mysql_fetch_row($res);
            return $arr[0];
        }

        $sql = "SELECT * FROM reports $join $wheres ORDER BY $order";

        if ($limit != "" || $limit > 0)
            $sql .= " LIMIT $limit";
        $res = dbquery($sql);
        $rtn = array();
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                $rtn[$arr['id']] = Report::createFactory($arr);
            }
        }
        return $rtn;
    }

    /**
     * Factory design pattern for getting instances depending on funcion argument
     *
     * @param mixed $args Array containing fetched database record or a record id
     * @return ?Report New report object instance
     */
    static function createFactory($args)
    {
        $type = null;
        if (is_array($args) && isset($args['type'])) {
            $type = $args['type'];
        } elseif (intval($args) > 0) {
            $sql = "SELECT * FROM reports WHERE id=" . $args . " LIMIT 1;";
            $res = dbquery($sql);
            if (mysql_num_rows($res) > 0) {
                $args = mysql_fetch_assoc($res);
                $type = $args['type'];
            }
        }

        try {
            if (isset($type)) {
                switch ($type) {
                    case 'market':
                        return new MarketReport($args);
                    case 'explore':
                        return new ExploreReport($args);
                    case 'spy':
                        return new SpyReport($args);
                    case 'battle':
                        return new BattleReport($args);
                    default:
                        return new OtherReport($args);
                }
            }
            throw new EException("Keine passende Reportklasse für $type gefunden!");
        } catch (EException $e) {
            echo $e;
        }

        return null;
    }

    function typeName()
    {
        return ReportTypes::TYPES[$this->type];
    }

    /**
     * Alte Nachrichten löschen
     */
    static function removeOld($threshold = 0, $onlyDeleted = 0)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];
        /** @var ReportRepository $reportRepository */
        $reportRepository = $app[ReportRepository::class];

        $nr = 0;
        if ($onlyDeleted == 0) {
            // Normal old messages
            $timestamp = $threshold > 0
                ? time() - $threshold
                : time() - (24 * 3600 * $config->getInt('reports_threshold_days'));

            $nr = $reportRepository->removeUnarchivedread($timestamp);
            Log::add("4", Log::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");
        }

        // Deleted
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $config->param1Int('reports_threshold_days'));

        $nr += $reportRepository->removeDeleted($timestamp);
        Log::add("4", Log::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $nr;
    }
}
