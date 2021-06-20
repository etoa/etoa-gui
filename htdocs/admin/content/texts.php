<?PHP

use EtoA\Text\TextRepository;
use Twig\Environment;

/** @var TextRepository */
$textRepo = $app['etoa.text.repository'];

if (!empty($_GET['id'])) {
    editText($textRepo, $twig);
}
if (!empty($_GET['preview'])) {
    previewText($textRepo, $twig);
}
if (!empty($_GET['enable'])) {
    enableText($textRepo);
}
if (!empty($_GET['disable'])) {
    disableText($textRepo);
}
textOverview($textRepo, $twig);

function editText(TextRepository $textRepo, Environment $twig)
{
    global $page;

    $id = $_GET['id'];
    if ($textRepo->isValidTextId($id)) {
        if (isset($_POST['save'])) {
            $t = $textRepo->find($id);
            $t->content = $_POST['content'];
            $textRepo->save($t);
            forward("?page=$page&id=$id");
        }

        if (isset($_POST['reset'])) {
            $textRepo->reset($id);
            forward("?page=$page&id=$id");
        }

        echo $twig->render('admin/texts/edit.html.twig', [
            'subtitle' => $textRepo->getLabel($id),
            'text' => $textRepo->find($id),
        ]);
        exit();
    }

    echo $twig->render('admin/texts/edit.html.twig', [
        'subtitle' => 'Text bearbeiten',
    ]);
    exit();
}

function previewText(TextRepository $textRepo, Environment $twig)
{
    $id = $_GET['preview'];
    if ($textRepo->isValidTextId($id)) {
        echo $twig->render('admin/texts/edit.html.twig', [
            'subtitle' => $textRepo->getLabel($id),
            'text' => $textRepo->find($id),
        ]);
        exit();
    }

	echo $twig->render('admin/texts/edit.html.twig', [
        'subtitle' => 'Textvorschau',
    ]);
    exit();
}

function enableText(TextRepository $textRepo)
{
    global $page;

    $id = $_GET['enable'];
	if ($textRepo->isValidTextId($id)) {
        $t = $textRepo->find($id);
        $t->enabled = true;
        $textRepo->save($t);
    }
    forward("?page=$page");
}

function disableText(TextRepository $textRepo)
{
    global $page;

    $id = $_GET['disable'];
    if ($textRepo->isValidTextId($id)) {
        $t = $textRepo->find($id);
        $t->enabled = false;
        $textRepo->save($t);
    }
    forward("?page=$page");
}

function textOverview(TextRepository $textRepo, Environment $twig)
{
    $texts = [];
    foreach ($textRepo->getAllTextIDs() as $id) {
        $texts[] = $textRepo->find($id);
    }
    echo $twig->render('admin/texts/overview.html.twig', [
        'texts' => $texts,
    ]);
    exit();
}