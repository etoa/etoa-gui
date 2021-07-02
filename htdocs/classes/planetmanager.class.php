<?PHP

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
