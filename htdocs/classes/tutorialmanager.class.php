<?PHP
class TutorialManager {

	function getTextById($id) {
		$res = dbQuerySave('
			SELECT
				text_tutorial_id,
				text_title,
				text_content,
				text_step
			FROM
				tutorial_texts
			WHERE
				text_id=?;',
			array($id));
		if ($arr = mysql_fetch_assoc($res)) {
			$t = new TutorialText();
			$t->id = $id;
			$t->tutorialId = $arr['text_tutorial_id'];
			$t->title = $arr['text_title'];
			$t->content = $arr['text_content'];
			$t->step = $arr['text_step'];
			
			$pres = dbQuerySave('
				SELECT
					text_step
				FROM
					tutorial_texts
				WHERE
					text_tutorial_id=?
					AND text_step<?
				ORDER BY
					text_step DESC
				LIMIT 1;',
				array($t->tutorialId, $t->step));
			if ($parr = mysql_fetch_row($pres)) {
				$t->prev = $parr[0];
			}
			
			$nres = dbQuerySave('
				SELECT
					text_step
				FROM
					tutorial_texts
				WHERE
					text_tutorial_id=?
					AND text_step>?
				ORDER BY
					text_step
				LIMIT 1;',
				array($t->tutorialId, $t->step));
			if ($narr = mysql_fetch_row($nres)) {
				$t->next = $narr[0];
			}
			
			return $t;
		}
		return null;
	}
	
	function getText($tutorialId, $step=0) {
		$res = dbQuerySave('
			SELECT
				text_id
			FROM
				tutorial_texts
			WHERE
				text_tutorial_id=?
				AND text_step<=?
			ORDER BY
				text_step DESC
			LIMIT 1;',
			array($tutorialId, $step));
		if ($arr = mysql_fetch_row($res)) {
			return $this->getTextById($arr[0]);
		}
		return null;
	}
	
	function setUserProgess($userId, $tutorialId, $textStep) {
		dbQuerySave('
			REPLACE INTO
				tutorial_user_progress
			(tup_user_id, tup_tutorial_id, tup_text_step)
			VALUES (?,?,?);',
			array($userId, $tutorialId, $textStep));
	}
	
	function getUserProgess($userId, $tutorialId) {
		$res = dbQuerySave('
			SELECT
				tup_text_step
			FROM
				tutorial_user_progress
			WHERE
				tup_user_id=?
				AND tup_tutorial_id=?;',
			array($userId, $tutorialId));
		if ($arr = mysql_fetch_row($res)) {
			return $arr[0];
		}
		return 0;
	}
	
	function hasReadTutorial($userId, $tutorialId) {
		$res = dbQuerySave('
			SELECT
				tup_closed
			FROM
				tutorial_user_progress
			WHERE
				tup_user_id=?
				AND tup_tutorial_id=?;',
			array($userId, $tutorialId));
		if ($arr = mysql_fetch_row($res)) {
			return $arr[0] == 1;
		}
		return false;
	}
	
	function closeTutorial($userId, $tutorialId) {
		dbQuerySave('
			UPDATE
				tutorial_user_progress
			SET
				tup_closed=1
			WHERE
				tup_user_id=?
				AND tup_tutorial_id=?;',
			array($userId, $tutorialId));
	}
	
	function reopenTutorial($userId, $tutorialId) {
		dbQuerySave('
			UPDATE
				tutorial_user_progress
			SET
				tup_closed=0
			WHERE
				tup_user_id=?
				AND tup_tutorial_id=?;',
			array($userId, $tutorialId));
	}

	function reopenAllTutorials($userId) {
		dbquery('
			UPDATE
				tutorial_user_progress
			SET
				tup_closed=0
			WHERE
				tup_user_id='.$userId);
				
	}

}
?>
