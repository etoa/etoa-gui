<?PHP
class TemplateEngine implements ISingleton {

	private $view = "default";
	private $layout = "default";

	static private $instance;
	private $smarty;

	/**
	* Get instance with this very nice singleton design pattern
	*/
	static public function getInstance() {
		if (!self::$instance) {
			self::$instance = new TemplateEngine();
		}
		return self::$instance;
	}

	public function __clone() {
		throw new EException("Config ist nicht klonbar!");
	}	
	
	public function __construct() {
		$this->smarty = new Smarty();
		$this->smarty->template_dir = RELATIVE_ROOT."tpl";
		$this->smarty->compile_dir = SMARTY_COMPILE_DIR;	
		$this->smarty->plugins_dir = array_merge($this->smarty->plugins_dir, array(RELATIVE_ROOT."plugins/smarty"));
	}
	
	function setLayout($value) {
		$this->layout = $value;
	}
	
	function setView($value) {
		$this->view = $value;
	}		
	
	function getChunk($name) {
		return $this->smarty->fetch("chunks/".$name.".html");
	}
	
	function assign($k, $v) {
		return $this->smarty->assign($k, $v);
	}
	
	function render() {
		$this->smarty->assign("content_for_layout", $this->smarty->fetch("views/".$this->view.".html"));
		$this->smarty->display("layouts/".$this->layout.".html");
		exit;
	}
}
?>