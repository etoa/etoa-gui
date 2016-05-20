<?PHP
	$tpl->setView('errorlog');
	$tpl->assign('subtitle', 'Datenbankfehler');

	if (isset($_POST['purgelog_submit'])) {
		file_put_contents(DBERROR_LOGFILE, '');
		forward('?page='.$page.'&sub='.$sub);
	}
	
	if (is_file(DBERROR_LOGFILE)) {
		$tpl->assign('logfile', file_get_contents(DBERROR_LOGFILE));
	}
?>