<?PHP
class TextManager {

	function getText($id, $default=null) {
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
			return $t;
		}
		if ($default !== null) {
			return new Text($id, $default);
		}
		return null;
	}
	
	function updateText($id, $content) {
		dbQuerySave('
			REPLACE INTO
				texts
			(text_id, text_content, text_updated)
			VALUES (?, ?, UNIX_TIMESTAMP());', 
			array($id, $content));
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
}
?>
