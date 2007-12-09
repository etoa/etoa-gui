<?PHP
	echo "<h2>Galaxie und Sonnensysteme</h2>";
	Help::navi(array("Galaxie und Sonnensysteme","space"));
	infobox_start("Raumkarte");
	echo "Der Raum ist unterteilt in Sektoren und Zellen. Jeder Sektor und jede Zelle haben eine X und eine Y Koordinate, mit denen sie sich identifizieren lassen. Eine Zelle kann entweder ein Sonnensystem mit Planeten, ein Asteroidenfeld, einen interstellaren Nebel oder Wurmloch beinhalten oder kann einfach leer sein.";
	infobox_end();
	
	infobox_start("Sonnensystem");
	echo "In jedem Sonnensystem hat es Planeten, welche besiedelt werden k&ouml;nnen. Planeten haben unterschiedliche Feldermengen (jedes Geb&auml;ude braucht eine gewisse Menge Felder) und unterschiedliche Planetentypen.";
	infobox_end();
?>