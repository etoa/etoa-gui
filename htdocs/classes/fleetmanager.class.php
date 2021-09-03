<?PHP

use EtoA\Fleet\FleetRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyRepository;

class FleetManager
{
    private $userId;
    private $allianceId;
    /** @var int */
    private $userSpyTechLevel;
    private $count;
    private $aggressivCount;
    private $fleet;

    public function __construct($userId, $allianceId = 0)
    {
        $this->userId = $userId;
        $this->allianceId = $allianceId;
        $this->count = 0;
        $this->fleet = array();
    }

    function loadOwn()
    {
        $this->count = 0;
        $this->fleet = array();

        //Lädt Flottendaten
        $fres = dbquery("
			SELECT
				id
			FROM
				fleet
			WHERE
				user_id='" . $this->userId . "'
			ORDER BY
				landtime DESC;");
        if (mysql_num_rows($fres) > 0) {
            while ($farr = mysql_fetch_row($fres)) {
                $this->fleet[$farr[0]] = new Fleet($farr[0]);
                $this->count++;
            }
        }
    }

    function loadForeign()
    {
        global $app;

        /** @var FleetRepository $fleetRepository */
        $fleetRepository = $app[FleetRepository::class];

        /** @var SpecialistService $specialistService */
        $specialistService = $app[SpecialistService::class];

        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];

        $this->count = 0;
        $this->aggressivCount = 0;
        $this->fleet = array();

        $this->userSpyTechLevel = $technologyRepository->getTechnologyLevel((int) $this->userId, TechnologyId::SPY);

        $specialist = $specialistService->getSpecialistOfUser($this->userId);
        if ($specialist !== null) {
            $this->userSpyTechLevel += $specialist->spyLevel;
        }

        if (SPY_TECH_SHOW_ATTITUDE <= $this->userSpyTechLevel) {
            //Lädt Flottendaten
            // TODO: This is not good query because it needs to know the planet table structure

            // Lade Flotten-id und leader-id (für Allianzangriffe)
            // von Flotten, die auf einen Planet des aktuellen Users fliegen
            // und nicht vom aktuellen User stammen
            // und bei Allianzflotten nur von der Leader-Flotte
            $fres = dbquery("
					SELECT
						f.id,
						f.leader_id
					FROM
						fleet f
					INNER JOIN
						planets p
					ON p.id=f.entity_to
						AND p.planet_user_id=" . $this->userId . "
						AND f.user_id!='" . $this->userId . "'
						AND !(f.action='alliance' AND f.leader_id!=f.id)
					ORDER BY
						landtime DESC;");
            if (mysql_num_rows($fres) > 0) {
                while ($farr = mysql_fetch_row($fres)) {
                    // cFleet contains all attached fleets if it
                    // is an alliance fleet.
                    $cFleet = new Fleet($farr[0], -1, $farr[1]);

                    if ($cFleet->getAction()->visible()) {
                        if ($cFleet->getAction()->attitude() == 3) {
                            $opTarnTech = $technologyRepository->getTechnologyLevel((int) $cFleet->ownerId(), TechnologyId::TARN);

                            $opponentSpecialist = $specialistService->getSpecialistOfUser($cFleet->ownerId());
                            if ($opponentSpecialist !== null) {
                                $opTarnTech += $opponentSpecialist->tarnLevel;
                            }

                            $diffTimeFactor = max($opTarnTech - $this->userSpyTechLevel, 0);
                            $specialShipBonusTarn = 0;

                            // Minbari fleet hide ability does not work with alliance attacks
                            // TODO: Improvement would be differentiation between single fleets
                            if ($cFleet->getAction()->code() !== 'alliance') {
                                $specialShipBonusTarn = $fleetRepository->getFleetSpecialTarnBonus($farr[0]);
                            }

                            $diffTimeFactor = 0.1 * min(9, $diffTimeFactor + 10 * $specialShipBonusTarn);

                            if ($cFleet->remainingTime() <  ($cFleet->landTime() - $cFleet->launchTime()) * (1 - $diffTimeFactor)) {
                                $this->fleet[$farr[0]] = $cFleet;
                                $this->count++;
                                $this->aggressivCount++;
                            }
                        } else {
                            $this->fleet[$farr[0]] = $cFleet;
                            $this->count++;
                        }
                    }
                }
            }
        }
    }

    function loadAllianceAttacks()
    {
        $this->count = 0;
        $this->fleet = array();
        //Lädt Flottendaten
        $fres = dbquery("
			SELECT
				id
			FROM
				fleet
			WHERE
				next_id='" . $this->allianceId . "'
				AND leader_id=id
				AND action='alliance'
				AND status='0'
			ORDER BY
				landtime DESC;");
        if (mysql_num_rows($fres) > 0) {
            while ($farr = mysql_fetch_row($fres)) {
                $this->fleet[$farr[0]] = new Fleet($farr[0]);
                $this->count++;
            }
        }
    }






    function count()
    {
        return $this->count;
    }

    function getAll()
    {
        return $this->fleet;
    }

    function spyTech()
    {
        return $this->userSpyTechLevel;
    }

    function attitude()
    {
        if ($this->aggressivCount == $this->count) return "color:#f00";
        elseif ($this->aggressivCount == 0) return "color:#0f0";
        else return "color:orange";
    }

    function loadAggressiv()
    {
        $this->loadForeign();
        return $this->aggressivCount;
    }
}
