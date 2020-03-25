<?PHP

//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// Programmiert von Nicolas Perrenoud				 		//
// www.nicu.ch | mail@nicu.ch								 		//
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
//////////////////////////////////////////////////
//
// 	Dateiname: config.php
// 	Topic: Generelle Konfigurationseinstellungen
// 	Autor: Nicolas Perrenoud alias MrCage
// 	Erstellt: 01.12.2004
// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
// 	Bearbeitet am: 31.03.2006
// 	Kommentar:
//

$conf_type['text']="Textfeld";
$conf_type['textarea']="Textblock";
$conf_type['timedate']="Zeit/Datum-Feld";
$conf_type['onoff']="Ein/Aus-Schalter";

//
// Restore
//
if ($sub === 'restoredefaults') {
    require("config/restoredefaults.inc.php");
}

//
// Check
//
if ($sub === 'check') {
    require("config/check.inc.php");
}

//
// Config-Editor
//
if ($sub === 'editor') {
    require("config/editor.inc.php");
}

$successMessage = null;
if (isset($_POST['submit'])) {
    foreach ($cfg->getBaseItems() as $i) {
        $v = isset($i->v) ? create_sql_value((string)$i->v['type'],(string)$i['name'],"v",$_POST) : "";
        $p1 = isset($i->p1) ? create_sql_value((string)$i->p1['type'],(string)$i['name'],"p1",$_POST) : "";
        $p2 = isset($i->p2) ? create_sql_value((string)$i->p2['type'],(string)$i['name'],"p2",$_POST) : "";
        $cfg->add((string)$i['name'],$v,$p1,$p2);
    }
    BackendMessage::reloadConfig();
    $successMessage = 'Änderungen wurden übernommen!';
}
$items = [];
foreach ( $cfg->getBaseItems() as $i) {
    if (isset($i->v)) {
        $items[] = [
            'label' => $i->v['comment'],
            'name' => $i['name'],
            'field' => display_field((string)$i->v['type'], (string)$i['name'], "v"),
        ];
    }
    if (isset($i->p1)) {
        $items[] = [
            'label' => $i->p1['comment'],
            'name' => $i['name'],
            'field' => display_field((string)$i->p1['type'], (string)$i['name'], "p1"),
        ];
    }
    if (isset($i->p2)) {
        $items[] = [
            'label' => $i->p2['comment'],
            'name' => $i['name'],
            'field' => display_field((string)$i->p2['type'], (string)$i['name'], "p2"),
        ];
    }
}

echo $twig->render('admin/config/index.html.twig', [
	'successMessage' => $successMessage,
	'items' => $items,
]);
exit();

