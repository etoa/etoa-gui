<?PHP
class TestJsonResponder extends JsonResponder
{
  function getRequiredParams() {
    return array();
  }

  function getResponse($params) {

    $data = array();

    $data['time'] = date("d.m.Y H:i:s");

    $getstr = array();
    foreach ($params as $k=>$v) {
      $getstr[] = "$k=$v";
    }
    $data['get'] = implode(',', $getstr);

    return $data;
  }
}
?>