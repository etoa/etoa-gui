<?PHP
abstract class JsonResponder
{
    protected \Pimple\Container $app;

    public function __construct(\Pimple\Container $app)
    {
        $this->app = $app;
    }

    static public function createFactory($action, Pimple\Container $app) {

    $className = ucfirst(preg_replace_callback('/_([a-z])/', function ($matches): string {
            return strtoupper($matches[1]);
        }, $action)).'JsonResponder';
    $file = 'classes/responder/'.$action.".class.php";
    if (file_exists($file))
    {
      include_once($file);
      if (class_exists($className, false)) {
        return new $className($app);
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

  // replace with own function in child classes
  public function validateSession()
  {
    global $s;

    if ($s->validate(0))
    {
      $cu = new CurrentUser($s->user_id);
      if ($cu->isValid)
      {
        return true;
      }
    }
    return false;
  }
}
