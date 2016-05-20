<?PHP
class TemplateEngine {

	private $view = "default";
	private $layout = "default";

	private $smarty;

	public function __clone() {
		throw new EException("Config ist nicht klonbar!");
	}	
	
	public function __construct($tplDir=null) {
		
		// Load smarty template engine
		require_once(SMARTY_DIR.'/Smarty.class.php');

		$this->smarty = new Smarty();
		$this->smarty->template_dir = $tplDir != null ? RELATIVE_ROOT.$tplDir : RELATIVE_ROOT."tpl";
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
	
	function display($file) {
		$this->smarty->display(getcwd().'/'.$file);	
	}
}
?>