<?php
	echo "<h2>Ressourcen</h2>";
	HelpUtil::breadCrumbs(array("Ressourcen","resources"));

	tableStart("Grundeinstellungen");
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\"><img src=\"images/resources/metal.png\"></td>
		<td class=\"tbldata\"><b>".RES_METAL." (Marktwert: ".$conf['market_metal_factor']['v'].")</b><br/>
		Titan ist ein Metall, welches leicht, fest, dehnbar, weiß-metallisch glänzend und 
		korrosionsbeständig ist. Es ist besonders für Anwendungen geeignet, bei 
		denen es auf hohe Korrosionsbeständigkeit, Festigkeit und geringes Gewicht ankommt. Titan ist
		ein wichtiger Rohstoff für den Bau von Gebäuden und Raumschiffen.		
		</td>
	</tr>";
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\"><img src=\"images/resources/crystal.png\"></td>
		<td class=\"tbldata\"><b>".RES_CRYSTAL." (Marktwert: ".$conf['market_crystal_factor']['v'].")</b><br/>
		Silicium ist ein klassisches Halbmetall und weist daher sowohl Eigenschaften von Metallen als 
		auch von Nichtmetallen auf. Reines, elementares Silicium besitzt eine grau-schwarze Farbe 
		und weist einen typisch metallischen, oftmals bronzenen bis bläulichen Glanz auf.	
		Siliciumhaltige Verbindungen sind Bestandteile moderner Baumaterialen wie zum Beispiel 
		Zement, Beton oder Glas.
		Heutzutage stellt Silicium das Grundmaterial der meisten Produkte der Halbleiterindustrie da. 
		So dient es auch als Basismaterial für viele Sensoren und andere mikromechanischen Systeme.
		 Silicium ist ebenfalls der elementare Bestandteil der meisten Photovoltaikelemente 
		 wie z.B. Solarkollektoren.
		</td>
	</tr>";	
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\"><img src=\"images/resources/plastic.png\"></td>
		<td class=\"tbldata\"><b>".RES_PLASTIC." (Marktwert: ".$conf['market_plastic_factor']['v'].")</b><br/>
		Polyvinylchlorid (Kurzzeichen PVC) ist ein amorpher thermoplastischer Kunststoff. 
		Es ist hart und spröde, von weißer Farbe und wird erst durch Zugabe von Weichmachern 
		und Stabilisatoren weicher, formbar und für technische Anwendungen geeignet. 
		Bekannt ist PVC vor allem durch seine Verwendung in der Bauwirtschaft.
		</td>
	</tr>";		
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\"><img src=\"images/resources/fuel.png\"></td>
		<td class=\"tbldata\"><b>".RES_FUEL." (Marktwert: ".$conf['market_fuel_factor']['v'].")</b><br/>
		Tritium (von griechisch tritós »der Dritte«) ist neben Protium und Deuterium 
		ein natürliches Isotop des Wasserstoffes. Tritium ist radioaktiv da es leicht zerfällt
		und muss sehr sorgfältig gelagert werden. Es muss meistens künstlich durch einen Synthetisyzer hergestellt 
		werden, entsteht aber auch auf natürliche Weise durch Neutronen-Beschuss auf Stickstoffkerne 
		aus der kosmischen Strahlung in den oberen Schichten der Atmosphäre. Das meiste natürliche Tritium
		befindet sich in oberflächennahen Schichten der Ozeane. Tritium hat einen extrem niederen Schmelz- und
		Siedepunkt weshalb es unter kühlen Bedingungen einfacher hergestellt und gelagert werden kann.
		Tritum wird verwendet um Energie für Gebäude und Produktion und Treibstoff für Raumschiffe zu gewinnen.
		</td>
	</tr>";		
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\"><img src=\"images/resources/food.png\"></td>
		<td class=\"tbldata\"><b>".RES_FOOD." (Marktwert: ".$conf['market_food_factor']['v'].")</b><br/>
		Nahrung wächst in Gewächshäusern und wird gebraucht um deine arbeitende Bevölkerung und deine
		Piloten zu versorgen.
		</td>
	</tr>";		
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\"><img src=\"images/resources/people.png\"></td>
		<td class=\"tbldata\"><b>Bewohner</b><br/>
		Bewohner beschleunigen deine Produktion und Forschung und werden als Piloten in deinen Schiffen benötigt.
		</td>
	</tr>";			
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\"><img src=\"images/resources/power.png\"></td>
		<td class=\"tbldata\"><b>Energie</b><br/>
		Alle deine Rohstoffgebäude benötigen Energie damit sie Rohstoffe herstellen können. Ebenfalls brauchen
		einige Spezialgebäude ein gewisses Mass von Energie, damit sie gebaut und benutzt werden können.
		</td>
	</tr>";			
	
	
	
	tableEnd();
?>

