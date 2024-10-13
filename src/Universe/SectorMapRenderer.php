<?PHP

namespace EtoA\Universe;

use EtoA\Core\ObjectWithImage;
use EtoA\UI\Tooltip;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\User;
use EtoA\User\UserUniverseDiscoveryService;
use EtoA\Universe\Cell\Cell;

/**
 * Draws a map of a galaxy sector
 */
class SectorMapRenderer
{
    const MapImageDirectory = "build/images/map";

    const HorizontalCoordinateNumberImagePrefix = "GalaxyFrameCounterBottom";
    const HorizontalCoordinateNumberHighlighImagePrefix = "GalaxyFrameCounterBottomHighlight";

    const VerticalCoordinateNumberImagePrefix = "GalaxyFrameCounterLeft";
    const VerticalCoordinateNumberHighlighImagePrefix = "GalaxyFrameCounterLeftHighlight";

    /** @var int[] */
    protected array $userCellsIDs = array();

    protected $numberOfCellsX;
    protected $numberOfCellsY;

    protected ?Cell $selectedCell = null;
    protected ?User $impersonatedUser = null;

    protected $rulerEnabled = false;
    protected $tooltipsEnabled = false;

    protected $cellUrl;
    protected $undiscoveredCellUrl;
    protected $undiscoveredCellJavaScript;

    /**
     * Constructor
     */
    public function __construct($numberOfCellsX, $numberOfCellsY)
    {
        $this->numberOfCellsX = $numberOfCellsX;
        $this->numberOfCellsY = $numberOfCellsY;
    }

    /**
     * Sets an array of cell IDs which will be highlighted
     * @param int[] $ids
     */
    function setUserCellIDs(array $ids): void
    {
        $this->userCellsIDs = $ids;
    }

    /**
     * Sets a specific cell to be marked as the active cell
     */
    function setSelectedCell(Cell $cell): void
    {
        $this->selectedCell = $cell;
    }

    /**
     * If set, the map will be viewed from the perspective of this user (fog of war)
     */
    function setImpersonatedUser(User $user): void
    {
        $this->impersonatedUser = $user;
    }

    /**
     * Enables ruler (vertical and horizontal numbers)
     */
    function enableRuler($enable): void
    {
        $this->rulerEnabled = $enable;
    }

    /**
     * Enables advanced tooltips when hovering over a cell
     */
    function enableTooltips($enable): void
    {
        $this->tooltipsEnabled = $enable;
    }

    /**
     * Sets the URL when clicking on a cell
     */
    function setCellUrl($cellUrl): void
    {
        $this->cellUrl = $cellUrl;
    }

    /**
     * Sets the URL when clickong on an undiscovered cell
     */
    function setUndiscoveredCellUrl($undiscoveredCellUrl): void
    {
        $this->undiscoveredCellUrl = $undiscoveredCellUrl;
    }

    /**
     * Sets the URL when clickong on an undiscovered cell
     */
    function setUndiscoveredCellJavaScript($undiscoveredCellJavaScript): void
    {
        $this->undiscoveredCellJavaScript = $undiscoveredCellJavaScript;
    }

    /**
     * Renders the sector map
     */
    function render($sx, $sy, UserUniverseDiscoveryService $userUniverseDiscoveryService = null, EntityRepository $entityRepository = null): bool|string
    {
        if ($userUniverseDiscoveryService === null) {
            // TODO
            global $app;

            /** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
            $userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
            /** @var EntityRepository $entityRepository */
            $entityRepository = $app[EntityRepository::class];
        }

        $entities = $entityRepository->searchEntities(EntitySearch::create()->sx($sx)->sy($sy)->pos(0));
        if (count($entities) === 0) {
            throw new \RuntimeException('Das Universum wurde noch nicht erstellt');
        }

        ob_start();

        echo '<table class="galaxyTableSector">';

        /** @var array<int, array<int, \EtoA\Universe\Entity\Entity>> $cells */
        $cells = [];
        foreach ($entities as $entity) {
            $cells[$entity->cx][$entity->cy] = $entity;
        }

        for ($y = 0; $y < $this->numberOfCellsX; $y++) {
            $ycoords = $this->numberOfCellsY - $y;

            echo "<tr>";

            // Numbers on the left side
            if ($this->rulerEnabled) {
                echo "<td class='galaxyCellNumber'>";
                echo "<img id=\"counter_left_$ycoords\" alt=\"$ycoords\" src=\"/" . self::MapImageDirectory . "/" . self::VerticalCoordinateNumberImagePrefix . "$ycoords.gif\" class=\"cell_number_vertical\"/>";
                echo "</td>";
            }

            for ($x = 0; $x < $this->numberOfCellsY; $x++) {
                $xcoords = $x + 1;

                echo "<td class='galaxyCell'>";

                // Cell element classes
                $classes = array("cell");
                if ($xcoords == 1) {
                    $classes[] = "sectorborder-left";
                }
                if ($ycoords == 1) {
                    $classes[] = "sectorborder-bottom";
                }

                // Overlay image classes
                $overlayClasses = array();
                if ($this->selectedCell != null && $this->selectedCell->sx == $sx && $this->selectedCell->sy == $sy && $this->selectedCell->cx == $xcoords && $this->selectedCell->cy == $ycoords) {
                    $overlayClasses[] = 'selected';
                } elseif (in_array($cells[$xcoords][$ycoords]->cellId, $this->userCellsIDs, true)) {
                    $overlayClasses[] = 'owned';
                }

                $js = null;

                // Discovered cell or no user specified
                if ($this->impersonatedUser == null || $userUniverseDiscoveryService->discovered($this->impersonatedUser, (($sx - 1) * $this->numberOfCellsX) + $xcoords, (($sy - 1) * $this->numberOfCellsY) + $ycoords)) {
                    $entity = $entityRepository->searchEntityLabel(EntitySearch::create()->id($cells[$xcoords][$ycoords]->id));

                    if ($this->tooltipsEnabled) {
                        $tt = new Tooltip();
                        $tt->addTitle($entity->codeString());
                        $tt->addText("Position: $sx/$sy : $xcoords/$ycoords");
                        if ($entity->code === \EtoA\Universe\Entity\EntityType::WORMHOLE && $entity->wormholeTarget !== null) {
                            $tent = $entityRepository->searchEntityLabel(EntitySearch::create()->id($entity->wormholeTarget));
                            $tt->addComment("Ziel: " . $tent->toString() . "</a>");
                        } else {
                            $tt->addComment((string)$entity->displayName());
                        }
                    }

                    $url = isset($this->cellUrl) ? $this->cellUrl . $cells[$xcoords][$ycoords]->cellId : '#';
                    $img = $entity->getImagePath();
                    unset($entity);
                } // Undiscovered cell
                else {
                    $fogCode = 0;
                    // Bottom
                    $fogCode += $ycoords > 1 && $userUniverseDiscoveryService->discovered($this->impersonatedUser, (($sx - 1) * $this->numberOfCellsX) + $xcoords, (($sy - 1) * $this->numberOfCellsY) + $ycoords - 1) ? 1 : 0;
                    // Left
                    $fogCode += $xcoords > 1 && $userUniverseDiscoveryService->discovered($this->impersonatedUser, (($sx - 1) * $this->numberOfCellsX) + $xcoords - 1, (($sy - 1) * $this->numberOfCellsY) + $ycoords) ? 2 : 0;
                    // Right
                    $fogCode += $xcoords < $this->numberOfCellsX && $userUniverseDiscoveryService->discovered($this->impersonatedUser, (($sx - 1) * $this->numberOfCellsX) + $xcoords + 1, (($sy - 1) * $this->numberOfCellsY) + $ycoords) ? 4 : 0;
                    // Top
                    $fogCode += $ycoords < $this->numberOfCellsY && $userUniverseDiscoveryService->discovered($this->impersonatedUser, (($sx - 1) * $this->numberOfCellsX) + $xcoords, (($sy - 1) * $this->numberOfCellsY) + $ycoords + 1) ? 8 : 0;

                    if ($fogCode > 0) {
                        $fogImg = "fogborder$fogCode";
                    } else {
                        $fogImg = "fog" . mt_rand(1, 6);
                    }

                    if ($this->tooltipsEnabled) {
                        $tt = new Tooltip();
                        $tt->addTitle("Unerforschte Raumzelle!");
                        $tt->addText("Position: $sx/$sy : $xcoords/$ycoords");
                        $tt->addComment("Expedition senden um Zelle sichtbar zu machen.");
                    }

                    $url = isset($this->undiscoveredCellUrl) ? $this->undiscoveredCellUrl . $cells[$xcoords][$ycoords]->cellId : '#';
                    if (isset($this->undiscoveredCellJavaScript)) {
                        $js = preg_replace('/##ID##/', (string)$cells[$xcoords][$ycoords]->cellId, $this->undiscoveredCellJavaScript);
                    }
                    $img = ObjectWithImage::BASE_PATH . "/unexplored/" . $fogImg . ".png";
                }

                // Title or tooltip
                $title = (isset($tt) ? $tt->toString() : "title=\"$sx/$sy : $xcoords/$ycoords\"");

                // Mouseover
                $mouseOver = '';
                if ($this->rulerEnabled) {
                    $mouseOver .= " onmouseover=\"$('#counter_left_$ycoords').attr('src','/" . self::MapImageDirectory . "/" . self::VerticalCoordinateNumberHighlighImagePrefix . "$ycoords.gif');$('#counter_bottom_$xcoords').attr('src','/" . self::MapImageDirectory . "/" . self::HorizontalCoordinateNumberHighlighImagePrefix . "$xcoords.gif');\"";
                    $mouseOver .= " onmouseout=\"$('#counter_left_$ycoords').attr('src','/" . self::MapImageDirectory . "/" . self::VerticalCoordinateNumberImagePrefix . "$ycoords.gif');$('#counter_bottom_$xcoords').attr('src','/" . self::MapImageDirectory . "/" . self::HorizontalCoordinateNumberImagePrefix . "$xcoords.gif');\"";
                }

                $class = " class=\"" . implode(' ', $classes);
                $overlayClass = count($overlayClasses) > 0 ? " class=\"" . implode(' ', $overlayClasses) . "\"" : '';

                if ($js != null) {
                    echo "<a href=\"javascript:;\" onclick=\"" . $js . "\" ";
                } else {
                    echo "<a href=\"" . $url . "\" ";
                }
                echo " style=\"background-image:url('" . $img . "');\"$class$mouseOver>";
                echo "<img src=\"/build/images/blank.gif\" alt=\"Raumzelle\" " . $title . " data-id=\"" . $cells[$xcoords][$ycoords]->cellId . "\" $overlayClass/></a>";
                echo "</td>";
            }

            echo "</tr>";
        }

        if ($this->rulerEnabled) {

            echo "<tr>";

            // Linke untere ecke
            echo "<td class='galaxyCellCorner'>";
            echo "<img alt=\"Blank\" src=\"/build/images/blank.gif\" class=\"cell_number_spacer\"/>";
            echo "</td>";

            // Numbers on the bottom side
            for ($x = 0; $x < $this->numberOfCellsY; $x++) {
                $xcoords = $x + 1;
                echo "<td class='galaxyCellNumber'>";
                echo "<img id=\"counter_bottom_$xcoords\" alt=\"$xcoords\" src=\"/" . self::MapImageDirectory . "/" . self::HorizontalCoordinateNumberImagePrefix . "$xcoords.gif\" class=\"cell_number_horizontal\"/>";
                echo "</td>";
            }

            echo "</tr>";
        }

        echo "</table>";

        return ob_get_clean();
    }
}
