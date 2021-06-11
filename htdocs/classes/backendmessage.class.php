<?PHP
class BackendMessage {

	/**
	* Sends a command to the backend to update the given planet
	*/
	public static function updatePlanet($id) {
		self::addMessage('planetupdate', $id);
	}

	/**
	* Sends a message to the backend to reload the config table
	*/
	public static function reloadConfig() {
		self::addMessage('configupdate');
	}

	private static function addMessage($cmd, $arg='') {
		dbQuerySave("INSERT IGNORE INTO backend_message_queue (cmd, arg) VALUES(?, ?);", array($cmd, $arg));
	}

	public static function getMessageQueueSize() {
		$arr = mysql_fetch_row(dbquery("SELECT COUNT(id) FROM backend_message_queue;"));
		return $arr[0];
	}
}
?>