<?PHP

if (isset($_POST['purgelog_submit'])) {
    file_put_contents(DBERROR_LOGFILE, '');
    forward('?page='.$page.'&sub='.$sub);
}

echo $twig->render('admin/database/errorlog.html.twig', [
    'logFile' => is_file(DBERROR_LOGFILE) ? file_get_contents(DBERROR_LOGFILE) : null,
]);
exit();
