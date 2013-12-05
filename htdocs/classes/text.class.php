<?PHP
class Text {
	
	public $id;
	public $label;
	public $content;
	public $updated;
	
	function __construct($id, $content) {
		$this->id = $id;
		$this->content = $content;
	}
}
?>