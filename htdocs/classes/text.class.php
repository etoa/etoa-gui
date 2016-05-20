<?PHP
class Text {
	
	public $id;
	public $label;
	public $description;
	public $content;
	public $updated;
	public $enabled = true;
	public $isOriginal = true;
	
	function __construct($id, $content) {
		$this->id = $id;
		$this->content = $content;
	}
}
?>