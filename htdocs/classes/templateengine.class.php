<?PHP
class TemplateEngine extends Smarty	{

	private $view = "default";
	private $layout = "default";

	function __construct()	{

		$this->template_dir = RELATIVE_ROOT."tpl";
		$this->compile_dir = SMARTY_COMPILE_DIR;	
		$this->plugins_dir[] = RELATIVE_ROOT."plugins/smarty";
		
		parent::__construct();
	}
	
	function setLayout($value)	{
		$this->layout = $value;
	}
	
	function setView($value)	{
		$this->view = $value;
	}		
	
	function render()	{
		$this->assign("content_for_layout", $this->fetch("views/".$this->view.".html"));
		$this->display("layouts/".$this->layout.".html");
		exit;
	}
}
?>