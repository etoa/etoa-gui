<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: get_fleet_action.php													//
	// Topic: Flotten aktion			 									//
	// Version: 0.1																	//
	// Letzte Änderung: 21.05.2005									//
	//////////////////////////////////////////////////


	//INFO: es darf keine aktion mit "c" oder "r" anfangen! (keine aktion wie "co" oder "rc")


function get_fleet_action($action)
{
	switch ($action)
	{
		case "po":
			return "Stationieren";
		break;
		case "poc":
			return "Stationieren (Abgebrochen)";
		break;
		case "mto":
			return "Transport";
		break;
		case "mpo":
			return "Stationieren";
		break;
		case "to":
			return "Transport (Hinflug)";
		break;
		case "tr":
			return "Transport (R&uuml;ckflug)";
		break;
		case "toc":
			return "Transport (Abgebrochen)";
		break;
		case "so":
			return "Spionieren (Hinflug)";
		break;
		case "sr":
			return "Spionieren (R&uuml;ckflug)";
		break;
		case "soc":
			return "Spionieren (Abgebrochen)";
		break;
		case "ao":
			return "Angreifen (Hinflug)";
		break;
		case "ar":
			return "Angreifen (R&uuml;ckflug)";
		break;
		case "aoc":
			return "Angreifen (Abgebrochen)";
		break;
		case "ko":
			return "Kolonie errichten";
		break;
		case "koc":
			return "Kolonie errichten (Abgebrochen)";
		break;
		case "io":
			return "Invasieren";
		break;
		case "ir":
			return "Invasieren (R&uuml;ckflug)";
		break;
		case "ioc":
			return "Invasieren (Abgebrochen)";
		break;
		case "bo":
			return "Bombadieren";
		break;
		case "br":
			return "Bombadieren (R&uuml;ckflug)";
		break;
		case "boc":
			return "Bombadieren (Abgebrochen)";
		break;
		case "do":
			return "EMP";
		break;
		case "dr":
			return "EMP (R&uuml;ckflug)";
		break;
		case "doc":
			return "EMP (Abgebrochen)";
		break;
		case "xo":
			return "Giftgas";
		break;
		case "xr":
			return "Giftgas (R&uuml;ckflug)";
		break;
		case "xoc":
			return "Giftgas (Abgebrochen)";
		break;
		case "ho":
			return "Antrax";
		break;
		case "hr":
			return "Antrax (R&uuml;ckflug)";
		break;
		case "hoc":
			return "Antrax (Abgebrochen)";
		break;
		case "vo":
			return "Tarnangriff";
		break;
		case "vr":
			return "Tarnangriff (R&uuml;ckflug)";
		break;
		case "voc":
			return "Tarnangriff (Abgebrochen)";
		break;
		case "eo":
			return "Angriff (fake)";
		break;
		case "er":
			return "Angriff (fake)(R&uuml;ckflug)";
		break;
		case "eoc":
			return "Angriff (fake)(Abgebrochen)";
		break;
		case "lo":
			return "Spionageangriff";
		break;
		case "lr":
			return "Spionageangriff (R&uuml;ckflug)";
		break;
		case "loc":
			return "Spionageangriff (Abgebrochen)";
		break;
		case "zo":
			return "Tr&uuml;mmerfeld erstellen";
		break;
		case "zr":
			return "Tr&uuml;mmerfeld erstellen (R&uuml;ckflug)";
		break;
		case "zoc":
			return "Tr&uuml;mmerfeld erstellen (Abgebrochen)";
		break;
		case "wo":
			return "Tr&uuml;mmer sammeln";
		break;
		case "wr":
			return "Tr&uuml;mmer sammeln (R&uuml;ckflug)";
		break;
		case "woc":
			return "Tr&uuml;mmer sammeln (Abgebrochen)";
		break;
		case "go":
			return "Gas saugen";
		break;
		case "gr":
			return "Gas saugen (R&uuml;ckflug)";
		break;
		case "goc":
			return "Gas saugen (Abgebrochen)";
		break;
		case "yo":
			return "Asteroiden sammeln";
		break;
		case "yr":
			return "Asteroiden sammeln (R&uuml;ckflug)";
		break;
		case "yoc":
			return "Asteroiden sammeln (Abgebrochen)";
		break;
		case "no":
			return "Nebel erkunden";
		break;
		case "nr":
			return "Nebel erkunden (R&uuml;ckflug)";
		break;
		case "noc":
			return "Nebel erkunden (Abgebrochen)";
		break;
		case "fo":
			return "Flug";
		break;
		case "fr":
			return "R&uuml;ckflug";
		break;
		case "foc":
			return "Flug (Abgebrochen)";
		break;
		default:
			return "Unbekannte Aktion";
	}
}
?>
