<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\Universe\Entity\EntitySort;

/**
 * Space cells class
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class Cell
{
    private $id;
    private $isValid;
    private $entities;

    public $sx;
    public $sy;
    public $cx;
    public $cy;

    private ConfigurationService $config;

    public function __construct($id = 0)
    {
        // TODO
        global $app;

        $this->config = $app[ConfigurationService::class];

        $this->isValid = false;
        $this->entities = null;

        /** @var EntityRepository $entityRepository */
        $entityRepository = $app[EntityRepository::class];
        $entity = $entityRepository->getEntity($id);
        if ($entity !== null) {
            $this->id = $entity->id;
            $this->sx = $entity->sx;
            $this->sy = $entity->sy;
            $this->cx = $entity->cx;
            $this->cy = $entity->cy;
            $this->isValid = true;
        }
    }

    public function id()
    {
        return $this->id;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    function getEntities()
    {
        if ($this->entities == null) {
            $this->entities = array();
            global $app;

            /** @var EntityRepository $entityRepository */
            $entityRepository = $app[EntityRepository::class];
            $entities = $entityRepository->searchEntities(EntitySearch::create()->cellId($this->id), EntitySort::pos());
            foreach ($entities as $entity) {
                $this->entities[] = Entity::createFactory($entity->code, $entity->id);
            }
        }
        return $this->entities;
    }

    function __toString()
    {
        return $this->sx . "/" . $this->sy . " : " . $this->cx . "/" . $this->cy;
    }

    function absX()
    {
        $cx_num = $this->config->param1Int('num_of_cells');
        return (($this->sx - 1) * $cx_num) + $this->cx;
    }

    function absY()
    {
        $cy_num = $this->config->param2Int('num_of_cells');
        return (($this->sy - 1) * $cy_num) + $this->cy;
    }

    function getSX()
    {
        return $this->sx;
    }

    function getSY()
    {
        return $this->sy;
    }

    function getCX()
    {
        return $this->cx;
    }

    function getCY()
    {
        return $this->cy;
    }
}
