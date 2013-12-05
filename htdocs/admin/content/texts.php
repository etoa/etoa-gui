<?PHP
	$tpl->assign("title", "Texte");

	$tm = new TextManager();
	$textDef = fetchJsonConfig("texts.conf");

	// Edit text
	if (!empty($_GET['id'])) {
		$tpl->setView("admin/texts/edit");
		$id = $_GET['id'];
		if (isset($textDef[$id])) {
			if (isset($_POST['save'])) {
				$tm->updateText($id, $_POST['content']);
			}
			$tpl->assign("subtitle", 'Text bearbeiten: ' . $textDef[$id]['label']);
			$tpl->assign("text", $tm->getText($id, ""));
		} else {
			$tpl->assign("subtitle", 'Text bearbeiten');
		}
	}
	
	// Preview text
	elseif (!empty($_GET['preview'])) {
		$tpl->setView("admin/texts/preview");
		$id = $_GET['preview'];
		if (isset($textDef[$id])) {
			$tpl->assign("subtitle", 'Textvorschau: ' . $textDef[$id]['label']);
			$tpl->assign("text", $tm->getText($id, ""));
		} else {
			$tpl->assign("subtitle", 'Textvorschau');
		}
	}
	
	// Overview
	else {
		$tpl->setView("admin/texts/overview");
		$tpl->assign("subtitle", 'Übersicht');		
		$texts = array();
		foreach ($textDef as $tk => $td) {
			$t = $tm->getText($tk, "");
			$t->label = $td['label'];
			$texts[] = $t;
		}
		$tpl->assign("texts", $texts);
	}
?>
