<?PHP
class GetShipListJsonResponder extends JsonResponder 
{
  function getRequiredParams() {
    return array('q');
  }

  function getResponse($params) {
    
    $data = array();
        
		$res=dbquery("
    SELECT 
			ship_id, 
			ship_name 
		FROM 
			ships 
		WHERE 
			(ship_show=1 || ship_buildable=1)
			AND ship_name LIKE '".$params['q']."%'
    ORDER BY
      ship_name
		LIMIT 20;");
    $nr = mysql_num_rows($res);
    $data['count'] = $nr;
		if ($nr > 0)
	  {
      $data['entries'] = array();
			while($arr = mysql_fetch_row($res))
			{
		    $data['entries'][] = array(
          'id' => $arr[0],
          'name' => $arr[1]
        );
	    }
		}
        
    return $data;
  }
}
?>