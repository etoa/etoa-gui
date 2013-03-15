<?PHP
class GetShipInfoJsonResponder extends JsonResponder 
{
  function getRequiredParams() {
    return array('ship');
  }

  function getResponse($params) {
    
    defineImagePaths();
    
    $data = array();
        
    if (is_numeric($params['ship'])) {
      $sql = " AND ship_id='".$params['ship']."'";
    }
    else {
      $sql = "AND ship_name='".$params['ship']."'";
    }
    $res = dbquery("
          SELECT
            ship_id,
            ship_name,
            special_ship,
            ship_actions,
            ship_shortcomment,
            ship_launchable
          FROM
            ships
          WHERE
            (ship_show=1
              || ship_buildable=1)
            ".$sql."
          LIMIT 1;");
    if (mysql_num_rows($res)>0)
    {
      $arr = mysql_fetch_assoc($res);
      if (!in_array($arr['ship_id'], $_SESSION['bookmarks']['added']))
      {
        $data['id'] = $arr['ship_id'];
        $data['name'] = $arr['ship_name'];
        $data['image'] = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT;
        
        $actions = explode(",",$arr['ship_actions']);
        $accnt=count($actions);
        if ($accnt>0)
        {
          $acstr = "<br/><b>Fägkeiten:</b> ";
          $x=0;
          foreach ($actions as $i)
          {
            if ($ac = FleetAction::createFactory($i))
            {
              $acstr.=$ac;
              if ($x<$accnt-1)
                $acstr.=", ";
            }
            $x++;
          }
          $acstr.="";
        }
        
        $data['tooltip'] = "<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_middle.".IMAGE_EXT."\" style=\"float:left;margin-right:5px;\">".text2html($arr['ship_shortcomment'])."<br/>".$acstr."<br style=\"clear:both;\"/>";
        
        $data['launchable'] = $arr['ship_launchable'];
      }
    } else {
      $data['error'] = "Schiff nicht gefunden!";
    }
    
    return $data;
  }
}
?>