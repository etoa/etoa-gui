<?PHP
class TextManager {

	private $textDef;

	function __construct() {
		$this->textDef = fetchJsonConfig("texts.conf");
	}

	function isValidTextId($id) {
		return isset($this->textDef[$id]);
	}

	function getLabel($id) {
		return $this->textDef[$id]['label'];
	}

	function getAllTextIDs() {
		return array_keys($this->textDef);
	}

	function getText($id) {
		if (!$this->isValidTextId($id)) {
			return null;
		}
		$res = dbQuerySave('
			SELECT
				*
			FROM
				texts
			WHERE
				text_id=?;',
			array($id));
		if ($arr = mysql_fetch_assoc($res)) {
			$t = new Text($id, $arr['text_content']);
			$t->updated = $arr['text_updated'];
			$t->enabled = ($arr['text_enabled'] > 0);
			$t->label = $this->textDef[$id]['label'];
			$t->description = $this->textDef[$id]['description'];
			$t->isOriginal = ($arr['text_content'] == $this->textDef[$id]['default']);
			return $t;
		}
		$t = new Text($id, $this->textDef[$id]['default']);
		$t->label = $this->textDef[$id]['label'];
		$t->description = $this->textDef[$id]['description'];
		return $t;
	}
	
	function updateText($text) {
		dbQuerySave('
			REPLACE INTO
				texts
			(text_id, text_content, text_updated, text_enabled)
			VALUES (?, ?, UNIX_TIMESTAMP(), ?);', 
			array($text->id, $text->content, $text->enabled ? 1 : 0));
	}

	function enableText($id) {
		dbQuerySave('
			UPDATE
				texts
			SET
				text_enabled=1
			WHERE
				text_id=?;',
			array($id));
	}

	function disableText($id) {
		dbQuerySave('
			UPDATE
				texts
			SET
				text_enabled=0
			WHERE
				text_id=?;',
			array($id));
	}

	function resetText($id) {
		dbQuerySave('
			DELETE FROM
				texts
			WHERE
				text_id=?;',
			array($id));
	}
}
?>
