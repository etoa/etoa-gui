<?php

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use EtoA\Message\ReportSort;
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
     * @param \EtoA\Message\Report $report Accepts a record id or an array of already fetched record data
     */
    public function __construct(\EtoA\Message\Report $report)
    {
        $this->id = $report->id;
        $this->timestamp = $report->timestamp;
        $this->type = $report->type;
        $this->read = $report->read;
        $this->deleted = $report->deleted;
        $this->userId = $report->userId;
        $this->allianceId = $report->allianceId;
        $this->content = $report->content;
        $this->entity1Id = $report->entity1Id;
        $this->entity2Id = $report->entity2Id;
        $this->opponent1Id = $report->opponentId;

        $this->valid = true;
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


    public abstract function createSubject();

    /**
     * @return \Report[]
     */
    static function find(ReportSearch $search, int $limit = null, int $first = null): array
    {
        global $app;
        /** @var ReportRepository $reportRepository */
        $reportRepository = $app[ReportRepository::class];
        $reports = $reportRepository->searchReports($search, $limit, $first);

        $rtn = [];
        foreach ($reports as $report) {
            $rtn[$report->id] = Report::createFactory($report);
        }
        return $rtn;
    }

    /**
     * Factory design pattern for getting instances depending on funcion argument
     *
     * @return Report New report object instance
     */
    static function createFactory(\EtoA\Message\Report $report)
    {
        switch ($report->type) {
            case 'market':
                return new MarketReport($report);
            case 'explore':
                return new ExploreReport($report);
            case 'spy':
                return new SpyReport($report);
            case 'battle':
                return new BattleReport($report);
            default:
                return new OtherReport($report);
        }
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

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];
        /** @var ReportRepository $reportRepository */
        $reportRepository = $app[ReportRepository::class];
        /** @var LogRepository $logRepository */
        $logRepository = $app[LogRepository::class];

        $nr = 0;
        if ($onlyDeleted == 0) {
            // Normal old messages
            $timestamp = $threshold > 0
                ? time() - $threshold
                : time() - (24 * 3600 * $config->getInt('reports_threshold_days'));

            $nr = $reportRepository->removeUnarchivedread($timestamp);
            $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");
        }

        // Deleted
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $config->param1Int('reports_threshold_days'));

        $nr += $reportRepository->removeDeleted($timestamp);
        $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $nr;
    }

    abstract public function __toString();
}
