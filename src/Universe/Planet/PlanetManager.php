<?PHP

namespace  EtoA\Universe\Planet;

//TODO: refactor

class PlanetManager
{
    private array $items;
    private array $itemObjects;
    private bool $loaded;
    private int $num;
    private PlanetRepository $planetRepository;
    public function __construct(array $i, PlanetRepository $planetRepository)
    {
        $this->items = $i;
        $this->loaded = false;
        $this->itemObjects = array();
        $this->num = count($i);
        $this->planetRepository = $planetRepository;
    }

    public function prevId($currendId)
    {
        for ($x = 0; $x < $this->num; $x++) {
            if ($this->items[$x] == $currendId) {
                return $this->items[($x + $this->num - 1) % $this->num];
            }
        }
        echo ($x - 1) % $this->num;
    }

    public function nextId($currendId)
    {
        for ($x = 0; $x < $this->num; $x++) {
            if ($this->items[$x] == $currendId) {
                return $this->items[($x + 1) % $this->num];
            }
        }
    }

    private function load(): void
    {
        if (!$this->loaded) {
            foreach ($this->items as $i) {
                $this->itemObjects[] = $this->planetRepository->find($i);
            }
            $this->loaded = true;
        }
    }

    function getSelectField($currendId): string
    {
        $req = "change_entity=";

        $this->load();

        $str = "<select name=\"nav_mode_select\" id=\"nav_mode_select\" onchange=\"document.location='?" . $req . "'+this.options[this.selectedIndex].value;\">";
        foreach ($this->itemObjects as $i) {
            $str .= "<option value=\"" . $i->getEntity()->getId() . "\"";
            if ($currendId == $i->getEntity()->getId())
                $str .= " selected=\"selected\"";
            $str.= ">" . $i->getEntity()->toString() . "</option>\n";
        }
        $str .= "</select>";
        return $str;
    }

    function getLinkList($currendId): array
    {

        $req = "change_entity=";
        $this->load();
        $list = [];
        foreach ($this->itemObjects as $i) {
            $list[] = [
                "url" => "?" . $req . $i->getEntity()->getId(),
                "label" => $i->getEntity()->toString(),
                "current" => $currendId == $i->getEntity()->getId(),
                "image" => $i->getImagePath()
            ];
        }
        return $list;
    }
}
