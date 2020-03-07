<?PHP

ob_start();
include('inc/contact.inc.php');
echo $twig->render('external/contact.html.twig', [
    'contactContent' => ob_get_clean(),
]);
