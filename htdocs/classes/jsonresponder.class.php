<?PHP
abstract class JsonResponder
{
  static public function createFactory($action) {
  
    $className = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper("$1")', $action)).'JsonResponder';
    $file = 'classes/responder/'.$action.".class.php";
    if (file_exists($file))
    {
      include_once($file);
      if (class_exists($className, false)) {
        return new $className();
      }      
    }
    throw new Exception('Action handler not found');
  }
  
  public function validateParams($params) {
    foreach ($this->getRequiredParams() as $r) {
      if (!isset($params[$r])) {
        return false;
      }
    }
    return true;
  }
  
  abstract public function getRequiredParams();
  abstract public function getResponse($params);
}
?>