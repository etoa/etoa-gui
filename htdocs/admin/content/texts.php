<?PHP

$tm = new TextManager();

// Edit text
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    if ($tm->isValidTextId($id)) {
        if (isset($_POST['save'])) {
            $t = $tm->getText($id);
            $t->content = $_POST['content'];
            $tm->updateText($t);
            forward("?page=$page&id=$id");
        }

        if (isset($_POST['reset'])) {
            $tm->resetText($id);
            forward("?page=$page&id=$id");
        }

        echo $twig->render('admin/texts/edit.html.twig', [
            'subtitle' => $tm->getLabel($id),
            'text' => $tm->getText($id),
        ]);
        exit();
    }

    echo $twig->render('admin/texts/edit.html.twig', [
        'subtitle' => 'Text bearbeiten',
    ]);
    exit();
}

// Preview text
if (!empty($_GET['preview'])) {
    $id = $_GET['preview'];
    if ($tm->isValidTextId($id)) {
        echo $twig->render('admin/texts/edit.html.twig', [
            'subtitle' => $tm->getLabel($id),
            'text' => $tm->getText($id),
        ]);
        exit();
    }

	echo $twig->render('admin/texts/edit.html.twig', [
        'subtitle' => 'Textvorschau',
    ]);
    exit();
}

// Enable text
if (!empty($_GET['enable'])) {
    $id = $_GET['enable'];
	if ($tm->isValidTextId($id)) {
        $t = $tm->getText($id);
        $t->enabled = true;
        $tm->updateText($t);
    }
    forward("?page=$page");
}

// Disable text
if (!empty($_GET['disable'])) {
    $id = $_GET['disable'];
    if ($tm->isValidTextId($id)) {
        $t = $tm->getText($id);
        $t->enabled = false;
        $tm->updateText($t);
    }
    forward("?page=$page");
}

// Overview
$texts = [];
foreach ($tm->getAllTextIDs() as $id) {
    $texts[] = $tm->getText($id, '');
}
echo $twig->render('admin/texts/overview.html.twig', [
    'texts' => $texts,
]);
exit();
