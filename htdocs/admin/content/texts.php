<?PHP
	$tpl->assign("title", "Texte");

	$tm = new TextManager();

	// Edit text
	if (!empty($_GET['id'])) {
		$tpl->setView("admin/texts/edit");
		$id = $_GET['id'];
		if ($tm->isValidTextId($id)) {
			if (isset($_POST['save'])) {
				$t = $tm->getText($id);
				$t->content = $_POST['content'];
				$tm->updateText($t);
			}
			$tpl->assign("subtitle", 'Text bearbeiten: ' . $tm->getLabel($id));
			$tpl->assign("text", $tm->getText($id));
		} else {
			$tpl->assign("subtitle", 'Text bearbeiten');
		}
	}
	
	// Preview text
	elseif (!empty($_GET['preview'])) {
		$tpl->setView("admin/texts/preview");
		$id = $_GET['preview'];
		if ($tm->isValidTextId($id)) {
			$tpl->assign("subtitle", 'Textvorschau: ' . $tm->getLabel($id));
			$tpl->assign("text", $tm->getText($id));
		} else {
			$tpl->assign("subtitle", 'Textvorschau');
		}
	}

	// Enable text
	else if (!empty($_GET['enable'])) {
		$id = $_GET['enable'];
		if ($tm->isValidTextId($id)) {
			$t = $tm->getText($id);
			$t->enabled = true;
			$tm->updateText($t);
		}
		forward("?page=$page");
	}

	// Disable text
	else if (!empty($_GET['disable'])) {
		$id = $_GET['disable'];
		if ($tm->isValidTextId($id)) {
			$t = $tm->getText($id);
			$t->enabled = false;
			$tm->updateText($t);
		}
		forward("?page=$page");
	}
	
	// Overview
	else {
		$tpl->setView("admin/texts/overview");
		$tpl->assign("subtitle", 'Übersicht');		
		$texts = array();
		foreach ($tm->getAllTextIDs() as $id) {
			$texts[] = $tm->getText($id, "");
		}
		$tpl->assign("texts", $texts);
	}
?>
