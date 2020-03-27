<?PHP

	//
	// Start-Items
	//
	if ($sub=="defaultitems")
	{
		include("config/defaultitems.inc.php");
	}

	//
	// Tipps
	//
	elseif ($sub=="tipps")
	{
		advanced_form("tipps", $twig);
	}

	//
	// Ticket-Cat
	//
	elseif ($sub=="ticketcat")
	{
		advanced_form("ticketcat", $twig);
	}

	//
	// Designs
	//
	elseif ($sub=="designs")
	{
		include("misc/designs.inc.php");
	}

	//
	// Bildpakete
	//
	elseif ($sub=="imagepacks")
	{
		include("misc/imagepacks.inc.php");
	}

	else
	{
		echo "<h1>Diverses</h1>";
		echo "Wähle eine Unterseite aus dem Menü!";

	}

?>
