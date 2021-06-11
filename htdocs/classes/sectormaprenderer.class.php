<?PHP
/**
* Draws a map of a galaxy sector
*
* @author Nicolas Perrenoud [mrcage]
*/
class SectorMapRenderer {

  const MapImageDirectory = "images/map";

  const HorizontalCoordinateNumberImagePrefix = "GalaxyFrameCounterBottom";
  const HorizontalCoordinateNumberHighlighImagePrefix = "GalaxyFrameCounterBottomHighlight";

  const VerticalCoordinateNumberImagePrefix = "GalaxyFrameCounterLeft";
  const VerticalCoordinateNumberHighlighImagePrefix = "GalaxyFrameCounterLeftHighlight";

  protected $userCellsIDs = array();

  protected $numberOfCellsX;
  protected $numberOfCellsY;

  protected $selectedCell;
  protected $impersonatedUser;

  protected $rulerEnabled = false;
  protected $tooltipsEnabled = false;

  protected $cellUrl;
  protected $undiscoveredCellUrl;
  protected $undiscoveredCellJavaScript;

  /**
  * Constructor
  */
    public function __construct($numberOfCellsX, $numberOfCellsY) {
    $this->numberOfCellsX = $numberOfCellsX;
    $this->numberOfCellsY = $numberOfCellsY;
  }

  /**
  * Sets an array of cell IDs which will be highlighted
  */
  function setUserCellIDs($ids) {
    $this->userCellsIDs = $ids;
  }

  /**
  * Sets a specific cell to be marked as the active cell
  */
  function setSelectedCell($cell) {
    $this->selectedCell = $cell;
  }

  /**
  * If set, the map will be viewed from the perspective of this user (fog of war)
  */
  function setImpersonatedUser(&$user) {
    $this->impersonatedUser = $user;
  }

  /**
  * Enables ruler (vertical and horizontal numbers)
  */
  function enableRuler($enable) {
    $this->rulerEnabled = $enable;
  }

  /**
  * Enables advanced tooltips when hovering over a cell
  */
  function enableTooltips($enable) {
    $this->tooltipsEnabled = $enable;
  }

  /**
  * Sets the URL when clickong on a cell
  */
  function setCellUrl($cellUrl) {
    $this->cellUrl = $cellUrl;
  }

  /**
  * Sets the URL when clickong on an undiscovered cell
  */
  function setUndiscoveredCellUrl($undiscoveredCellUrl) {
    $this->undiscoveredCellUrl = $undiscoveredCellUrl;
  }

  /**
  * Sets the URL when clickong on an undiscovered cell
  */
  function setUndiscoveredCellJavaScript($undiscoveredCellJavaScript) {
    $this->undiscoveredCellJavaScript = $undiscoveredCellJavaScript;
  }

  /**
  * Renders the sector map
  */
  function render($sx, $sy) {

    ob_start();

    $res = dbquery("
    SELECT
      cx,
      cy,
      cells.id as cid,
      entities.id as eid,
      code
    FROM
      cells
    INNER JOIN
      entities
      ON entities.cell_id=cells.id
      AND entities.pos=0
      AND sx='$sx'
      AND sy='$sy';");
    $cells = array();
    while ($arr = mysql_fetch_assoc($res))
    {
      $cells[$arr['cx']][$arr['cy']]['cid']=$arr['cid'];
      $cells[$arr['cx']][$arr['cy']]['eid']=$arr['eid'];
      $cells[$arr['cx']][$arr['cy']]['code']=$arr['code'];
    }

    for ($y=0; $y < $this->numberOfCellsX; $y++)
    {
      $ycoords = $this->numberOfCellsY - $y;

      // Numbers on the left side
      if ($this->rulerEnabled) {
        echo "<img id=\"counter_left_$ycoords\" alt=\"$ycoords\" src=\"".RELATIVE_ROOT.self::MapImageDirectory."/".self::VerticalCoordinateNumberImagePrefix."$ycoords.gif\" class=\"cell_number_vertical\"/>";
      }

      for ($x=0; $x < $this->numberOfCellsY; $x++)
      {
        $xcoords = $x+1;

        // Cell element classes
        $classes = array();
        if ($xcoords == 1) {
          $classes[] = "sectorborder-left";
        }
        if ($ycoords == 1) {
          $classes[] = "sectorborder-bottom";
        }

        // Overlay image classes
        $overlayClasses = array();
        if ($this->selectedCell != null && $this->selectedCell->getSX() == $sx && $this->selectedCell->getSY() == $sy && $this->selectedCell->getCX() == $xcoords && $this->selectedCell->getCY() == $ycoords) {
          $overlayClasses[] = 'selected';
        } elseif (in_array($cells[$xcoords][$ycoords]['cid'], $this->userCellsIDs)) {
          $overlayClasses[] = 'owned';
        }

	  $js = null;

        // Discovered cell or no user specified
        if ($this->impersonatedUser == null || $this->impersonatedUser->discovered((($sx - 1) * $this->numberOfCellsX) + $xcoords, (($sy - 1) * $this->numberOfCellsY) + $ycoords))
        {
          $ent = Entity::createFactory($cells[$xcoords][$ycoords]['code'],$cells[$xcoords][$ycoords]['eid']);

          if ($this->tooltipsEnabled) {
            $tt = new Tooltip();
            $tt->addTitle($ent->entityCodeString());
            $tt->addText("Position: $sx/$sy : $xcoords/$ycoords");
            if ($ent->entityCode()=='w')
            {
              $tent = new Wormhole($ent->targetId());
              $tt->addComment("Ziel: $tent</a>");
            }
            else
            {
              $tt->addComment($ent->name());
            }
          }

          $url = isset($this->cellUrl) ? $this->cellUrl.$cells[$xcoords][$ycoords]['cid'] : '#';
          $img = RELATIVE_ROOT.$ent->imagePath();
          unset($ent);
        }

        // Undiscovered cell
        else
        {
          $fogCode = 0;
          // Bottom
          $fogCode += $ycoords > 1 && $this->impersonatedUser->discovered((($sx - 1) * $this->numberOfCellsX) + $xcoords  , (($sy - 1) * $this->numberOfCellsY) + $ycoords-1) ? 1 : 0;
          // Left
          $fogCode += $xcoords > 1 && $this->impersonatedUser->discovered((($sx - 1) * $this->numberOfCellsX) + $xcoords-1, (($sy - 1) * $this->numberOfCellsY) + $ycoords  ) ? 2 : 0;
          // Right
          $fogCode += $xcoords < $this->numberOfCellsX && $this->impersonatedUser->discovered((($sx - 1) * $this->numberOfCellsX) + $xcoords+1, (($sy - 1) * $this->numberOfCellsY) + $ycoords  ) ? 4 : 0;
          // Top
          $fogCode += $ycoords < $this->numberOfCellsY && $this->impersonatedUser->discovered((($sx - 1) * $this->numberOfCellsX) + $xcoords  , (($sy - 1) * $this->numberOfCellsY) + $ycoords+1) ? 8 : 0;

          if ($fogCode > 0) {
            $fogImg = "fogborder$fogCode";
          } else {
            $fogImg = "fog".mt_rand(1,6);
          }

          if ($this->tooltipsEnabled) {
            $tt = new Tooltip();
            $tt->addTitle("Unerforschte Raumzelle!");
            $tt->addText("Position: $sx/$sy : $xcoords/$ycoords");
            $tt->addComment("Expedition senden um Zelle sichtbar zu machen.");
          }

          $url = isset($this->undiscoveredCellUrl) ? $this->undiscoveredCellUrl.$cells[$xcoords][$ycoords]['cid'] : '#';
          if (isset($this->undiscoveredCellJavaScript)) {
			$js = preg_replace('/##ID##/', $cells[$xcoords][$ycoords]['cid'], $this->undiscoveredCellJavaScript);
		  }
          $img = IMAGE_PATH."/unexplored/".$fogImg.".png";
        }

        // Title or tooltip
        $title = (isset($tt) ? $tt : "title=\"$sx/$sy : $xcoords/$ycoords\"");

        // Mouseover
        $mouseOver = '';
        if ($this->rulerEnabled) {
          $mouseOver.= " onmouseover=\"$('#counter_left_$ycoords').attr('src','".RELATIVE_ROOT.self::MapImageDirectory."/".self::VerticalCoordinateNumberHighlighImagePrefix."$ycoords.gif');$('#counter_bottom_$xcoords').attr('src','".RELATIVE_ROOT.self::MapImageDirectory."/".self::HorizontalCoordinateNumberHighlighImagePrefix."$xcoords.gif');\"";
          $mouseOver.= " onmouseout=\"$('#counter_left_$ycoords').attr('src','".RELATIVE_ROOT.self::MapImageDirectory."/".self::VerticalCoordinateNumberImagePrefix."$ycoords.gif');$('#counter_bottom_$xcoords').attr('src','".RELATIVE_ROOT.self::MapImageDirectory."/".self::HorizontalCoordinateNumberImagePrefix."$xcoords.gif');\"";
        }

        $class = count($classes) > 0 ? " class=\"".implode(' ', $classes)."\"": '';
        $overlayClass = count($overlayClasses) > 0 ? " class=\"".implode(' ', $overlayClasses)."\"": '';

		if ($js != null) {
			echo "<a href=\"javascript:;\" onclick=\"".$js."\" ";
		} else {
			echo "<a href=\"".$url."\" ";
		}
		echo " style=\"background:url('".$img."');\"$class$mouseOver>";
        echo "<img src=\"".RELATIVE_ROOT."images/blank.gif\" alt=\"Raumzelle\" ".$title." data-id=\"".$cells[$xcoords][$ycoords]['cid']."\" $overlayClass/></a>";

      }
      echo "<br/>";
    }

    if ($this->rulerEnabled) {

      // Linke untere ecke
      echo "<img alt=\"Blank\" src=\"".RELATIVE_ROOT."images/blank.gif\" class=\"cell_number_spacer\"/>";

      // Numbers on the bottom side
      for ($x=0; $x < $this->numberOfCellsY; $x++)
      {
        $xcoords = $x+1;
        echo "<img id=\"counter_bottom_$xcoords\" alt=\"$xcoords\" src=\"".RELATIVE_ROOT.self::MapImageDirectory."/".self::HorizontalCoordinateNumberImagePrefix."$xcoords.gif\" class=\"cell_number_horizontal\"/>";
      }

    }

    return ob_get_clean();
  }

}

?>
