<?PHP
	$tpl->setView('config/restoredefaults');
	$tpl->assign('subtitle', 'Konfiguration auf Standardwerte zurücksetzen');

	if (isset($_POST['restoresubmit']))
	{
		if ($cnt = $cfg->restoreDefaults())
		{
			$tpl->assign("msg", "$cnt Einstellungen wurden wiederhergestellt!");
			BackendMessage::reloadConfig();
		}
	}
	
	// Changed values
	foreach ($cfg->categories() as $ck => $cv) 
	{
		foreach ($cfg->itemInCategory($ck) as $i)
		{
			$name = $i['name'];
			if (isset($i->v))
			{
				if ((string)$i->v != $cfg->$name->v)
				{
					$items[] = array(
						'category' => $cv,
						'label' => (string)$i->v['comment'],
						'name' => (string)$i['name'],
						'type' => 'v',
						'value' => $cfg->$name->v,
						'default' => (string)$i->v,
					);
				}
			}
			if (isset($i->p1))
			{
				if ((string)$i->p1 != $cfg->$name->p1)
				{
					$items[] = array(
						'category' => $cv,
						'label' => (string)$i->p1['comment'],
						'name' => (string)$i['name'],
						'type' => 'p1',
						'value' => $cfg->$name->p1,
						'default' => (string)$i->p1,
					);
				}
			}
			if (isset($i->p2))
			{
				if ((string)$i->p2 != $cfg->$name->p2)
				{
					$items[] = array(
						'category' => $cv,
						'label' => (string)$i->p2['comment'],
						'name' => (string)$i['name'],
						'type' => 'p2',
						'value' => $cfg->$name->p2,
						'default' => (string)$i->p2,
					);
				}
			}
		}
    }
	$tpl->assign("changedValues", $items);
?>