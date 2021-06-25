<?PHP

use EtoA\Core\Configuration\ConfigurationService;

class PlanetManager
{
    private $items;
    private $itemObjects;
    private $loaded;
    private $num;

    public function __construct(Array $i)
    {
        $this->items = $i;
        $this->loaded=false;
        $this->itemObjects = array();
        $this->num = count($i);
    }

    public function itemObjects()
    {
        $this->load();
        return $this->itemObjects;
    }

    static function getFreePlanet($sx=0,$sy=0, $fp=0, $fs=0)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app['etoa.config.service'];

        $filter = '';
        if($fp>0) {
            $filter = " AND planets.planet_type_id = $fp";
        }

        if($fs>0) {
            $filter .= " AND entities.cell_id = any (
                    select cell_id FROM entities WHERE id = any (
                        select id from stars where type_id = $fs
                    )
                )";
        }

        $sql = "
            SELECT
                planets.id
            FROM
                cells
            INNER JOIN
            (
                entities
                INNER JOIN
                (
                    planets
                    INNER JOIN
                        planet_types
                        ON planet_type_id=type_id
                        AND type_habitable=1
                )
                ON planets.id=entities.id
                AND planets.planet_fields>'".$config->getInt('user_min_fields')."'
                AND planets.planet_user_id='0'$filter
                )
            ON entities.cell_id=cells.id ";
        if ($sx>0)
            $sql.=" AND cells.sx=".$sx." ";
        if ($sy>0)
            $sql.=" AND cells.sy=".$sy." ";

        $sql.="ORDER BY
                RAND()
        LIMIT 1";
        $tres = dbquery($sql);
        if (mysql_num_rows($tres)==0)
        {
            return false;
        }
        $tarr = mysql_fetch_row($tres);
        return $tarr[0];
    }

    public function prevId($currendId)
    {
        for ($x=0;$x<$this->num;$x++)
        {
            if ($this->items[$x]==$currendId)
            {
                return $this->items[($x+$this->num-1)%$this->num];
            }
        }
        echo ($x-1)%$this->num;
    }

    public function nextId($currendId)
    {
        for ($x=0;$x<$this->num;$x++)
        {
            if ($this->items[$x]==$currendId)
            {
                return $this->items[($x+1)%$this->num];
            }
        }
    }

    private function load()
    {
        if (!$this->loaded)
        {
            foreach ($this->items as $i)
            {
                $this->itemObjects[] = Planet::getById($i);
            }
            $this->loaded=true;
        }
    }

    function getSelectField($currendId)
    {
        global $page, $mode;

        if ($mode!="")
            $req = "&amp;mode=$mode&amp;change_entity=";
        else
            $req = "&amp;change_entity=";

        $this->load();
        ob_start();
        echo "<select name=\"nav_mode_select\" id=\"nav_mode_select\" onchange=\"document.location='?page=".$page.$req."'+this.options[this.selectedIndex].value;\">";
        foreach ($this->itemObjects as $i)
        {
            echo "<option value=\"".$i->id()."\"";
            if ($currendId==$i->id())
                echo " selected=\"selected\"";
            echo ">".$i."</option>\n";
        }
        echo "</select>";
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

    function getLinkList($currendId, $page, $mode)
    {
        if ($mode!="")
        {
            $req = "&amp;mode=$mode&amp;change_entity=";
        }
        else
        {
            $req = "&amp;change_entity=";
        }
        $this->load();
        $list = [];
        foreach ($this->itemObjects as $i)
        {
            $list[] = [
                "url" => "?page=$page".$req.$i->id(),
                "label" => $i,
                "current" => $currendId==$i->id(),
                "image" => $i->imagePath()
            ];
        }
        return $list;
    }
}
