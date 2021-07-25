<?PHP

use EtoA\Core\Configuration\ConfigurationService;

class AllianceTechnology
{
    public $name;

    public $id;

    public $costs, $costsFactor;

    private $bRequirements = null;

    private $isValid = false;

    private ConfigurationService $config;

    public function __construct($id)
    {
        // TODO
        global $app;

        $this->config = $app[ConfigurationService::class];

        try {
            if (is_array($id)) {
                $arr = $id;
            } else {
                $res = dbquery("
                SELECT
                    *
                FROM
                    alliance_technologies
                WHERE
                    alliance_tech_id='" . intval($id) . "'
                LIMIT 1");
                if (mysql_num_rows($res) > 0)
                    $arr = mysql_fetch_assoc($res);
                else {
                    throw new EException("Forschung $id existiert nicht!");
                }
            }

            $this->id = $arr['alliance_tech_id'];
            $this->name = $arr['alliance_tech_name'];
            $this->shortDesc = $arr['alliance_tech_shortcomment'];
            $this->longDesc = $arr['alliance_tech_longcomment'];
            $this->maxLevel = $arr['alliance_tech_last_level'];

            $this->costs = array();
            $this->costs[1] = $arr['alliance_tech_costs_metal'];
            $this->costs[2] = $arr['alliance_tech_costs_crystal'];
            $this->costs[3] = $arr['alliance_tech_costs_plastic'];
            $this->costs[4] = $arr['alliance_tech_costs_fuel'];
            $this->costs[5] = $arr['alliance_tech_costs_food'];
            $this->costsFactor = $arr['alliance_tech_costs_factor'];

            $this->buildTime = $arr['alliance_tech_build_time'];
            $this->show = $arr['alliance_tech_show'];

            $this->tRequirements = array($arr['alliance_tech_needed_id'] => $arr['alliance_tech_needed_level']);

            $this->isValid = true;
        } catch (Exception $e) {
            echo $e;
            return;
        }
    }

    function isValid()
    {
        return $this->isValid;
    }

    function __toString()
    {
        return $this->name;
    }

    function imgPathSmall()
    {
        return IMAGE_PATH . "/" . IMAGE_ALLIANCE_TECHNOLOGY_DIR . "/technology" . $this->id . "_small." . IMAGE_EXT;
    }

    function imgPathMiddle()
    {
        return IMAGE_PATH . "/" . IMAGE_ALLIANCE_TECHNOLOGY_DIR . "/technology" . $this->id . "_middle." . IMAGE_EXT;
    }

    function imgPathBig()
    {
        return IMAGE_PATH . "/" . IMAGE_ALLIANCE_TECHNOLOGY_DIR . "/technology" . $this->id . "." . IMAGE_EXT;
    }

    function imgSmall()
    {
        return "<img src=\"" . $this->imgPathSmall() . "\" style=\"width:40px;height:40px;\" alt=\"" . $this . "\"/>";
    }

    function imgMiddle()
    {
        return "<img src=\"" . $this->imgPathMiddle() . "\" style=\"width:120px;height:120px;\" alt=\"" . $this . "\"/>";
    }

    function imgBig()
    {
        return "<img src=\"" . $this->imgPathBig() . "\" style=\"width:220px;height:220px;\" alt=\"" . $this . "\"/>";
    }

    function getCosts($level = 1, $members = 1)
    {
        $level = max(1, $level);
        $members = max(1, $members);

        $factor = pow($this->costsFactor, $level - 1);
        $memberFactor = 1 + ($members - 1) * $this->config->getFloat('alliance_membercosts_factor');
        $bc = array();
        for ($i = 1; $i < 6; $i++)
            $bc[$i] = ceil($this->costs[$i] * $factor * $memberFactor);
        return $bc;
    }

    function getTechRequirements()
    {
        return $this->tRequirements;
    }

    function getBuildingRequirements()
    {
        return array();
    }
}
