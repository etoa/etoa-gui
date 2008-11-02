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
	// 	File: planetstats.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows economy information about all planets
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo '<h1>Wirtschaftsübersicht</h1>';


	$planets=$pm->itemObjects();

	$cnt_res=0;
	$max_res=array(0,0,0,0,0,0);
	$min_res=array(9999999999,9999999999,9999999999,9999999999,9999999999,9999999999);
	$tot_res=array(0,0,0,0,0,0);

	$cnt_prod=0;
	$max_prod=array(0,0,0,0,0,0);
	$min_prod=array(9999999999,9999999999,9999999999,9999999999,9999999999,9999999999);
	$tot_prod=array(0,0,0,0,0,0);
	foreach ($planets as $p)
	{
		//Speichert die aktuellen Rohstoffe in ein Array
		$val_res[$p->id][0]=floor($p->resMetal);
		$val_res[$p->id][1]=floor($p->resCrystal);
		$val_res[$p->id][2]=floor($p->resPlastic);
		$val_res[$p->id][3]=floor($p->resFuel);
		$val_res[$p->id][4]=floor($p->resFood);
		$val_res[$p->id][5]=floor($p->people);

		for ($x=0;$x<6;$x++)
		{
			$max_res[$x]=max($max_res[$x],$val_res[$p->id][$x]);
			$min_res[$x]=min($min_res[$x],$val_res[$p->id][$x]);
			$tot_res[$x]+=$val_res[$p->id][$x];
		}

		//Speichert die aktuellen Rohstoffproduktionen in ein Array
		$val_prod[$p->id][0]=floor($p->prodMetal);
		$val_prod[$p->id][1]=floor($p->prodCrystal);
		$val_prod[$p->id][2]=floor($p->prodPlastic);
		$val_prod[$p->id][3]=floor($p->prodFuel);
		$val_prod[$p->id][4]=floor($p->prodFood);
		$val_prod[$p->id][5]=floor($p->prodPeople);

		for ($x=0;$x<6;$x++)
		{
			$max_prod[$x]=max($max_prod[$x],$val_prod[$p->id][$x]);
			$min_prod[$x]=min($min_prod[$x],$val_prod[$p->id][$x]);
			$tot_prod[$x]+=$val_prod[$p->id][$x];
		}

		//Speichert die aktuellen Speicher in ein Array
		$val_store[$p->id][0]=floor($p->storeMetal);
		$val_store[$p->id][1]=floor($p->storeCrystal);
		$val_store[$p->id][2]=floor($p->storePlastic);
		$val_store[$p->id][3]=floor($p->storeFuel);
		$val_store[$p->id][4]=floor($p->storeFood);
		$val_store[$p->id][5]=floor($p->people_place);

		//Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

		//Titan
		if($p->prodMetal>0)
		{
      if ($p->storeMetal - $p->resMetal > 0)
      {
      	$val_time[$p->id][0]=ceil(($p->storeMetal-$p->resMetal)/$p->prodMetal*3600);
      }
      else
      {
        $val_time[$p->id][0]=0;
      }
    }
    else
    {
    	$val_time[$p->id][0]=0;
    }
    
		//Silizium
		if($p->prodCrystal>0)
		{
      if ($p->storeCrystal - $p->resCrystal > 0)
      {
      	$val_time[$p->id][1]=ceil(($p->storeCrystal-$p->resCrystal)/$p->prodCrystal*3600);
      }
      else
      {
      	$val_time[$p->id][1]=0;
      }
    }
    else
    {
    	$val_time[$p->id][1]=0;
    }
    
		//PVC
		if($p->prodPlastic>0)
		{
      if ($p->storePlastic - $p->resPlastic > 0)
      {
        $val_time[$p->id][2]=ceil(($p->storePlastic-$p->resPlastic)/$p->prodPlastic*3600);
      }
      else
      {
      	$val_time[$p->id][2]=0;
      }
    }
    else
    {
    	$val_time[$p->id][2]=0;
    }
    
		//Tritium
		if($p->prodFuel>0)
		{
      if ($p->storeFuel - $p->resFuel > 0)
      {
       	$val_time[$p->id][3]=ceil(($p->storeFuel-$p->resFuel)/$p->prodFuel*3600);
      }
      else
      {
      	$val_time[$p->id][3]=0;
      }
    }
    else
    {
    	$val_time[$p->id][3]=0;
    }
    
		//Nahrung
		if($p->prodFood>0)
		{
	    if ($p->storeFood - $p->resFood > 0)
	    {
	      $val_time[$p->id][4]=ceil(($p->storeFood-$p->resFood)/$p->prodFood*3600);
	    }
	    else
	   	{
	    	$val_time[$p->id][4]=0;
	    }
    }
    else
    {
    	$val_time[$p->id][4]=0;
    }

		//Bewohner
		if($p->prodPeople>0)
		{
      if ($p->people_place - $p->people > 0)
      {
        $val_time[$p->id][5]=ceil(($p->people_place-$p->people)/$p->prodPeople*3600);
      }
      else
      {
      	$val_time[$p->id][5]=0;
      }
    }
    else
    {
    	$val_time[$p->id][5]=0;
    }
	}


	//
	// Rohstoffe/Bewohner und Speicher
	//

	tableStart("Rohstoffe und Bewohner");
	echo "<tr><th>Name:</th>
	<th>".RES_METAL."</th>
	<th>".RES_CRYSTAL."</th>
	<th>".RES_PLASTIC."</th>
	<th>".RES_FUEL."</th>
	<th>".RES_FOOD."</th>
	<th>Bewohner</th></tr>";
	foreach ($planets as $p)
	{
		echo "<tr><td><a href=\"?page=economy&amp;planet_id=".$p->id."\">".$p->name."</a></td>";
		for ($x=0;$x<6;$x++)
		{
			echo "<td";
			if ($max_res[$x]==$val_res[$p->id][$x])
			{
				echo " style=\"color:#0f0\"";
			}
			elseif ($min_res[$x]==$val_res[$p->id][$x])
			{
				 echo " style=\"color:#f00\"";
			}
			else
			{
				 echo " ";
			}


			//Der Speicher ist noch nicht gefüllt
			if($val_res[$p->id][$x]<$val_store[$p->id][$x] && $val_time[$p->id][$x]!=0)
			{
				echo " ".tm("Speicher","Speicher voll in ".tf($val_time[$p->id][$x])."")." ";
				if ($val_time[$p->id][$x]<43200)
				{
					echo " style=\"font-style:italic;\" ";
				}
				echo ">".nf($val_res[$p->id][$x])."</td>";
			}
			//Speicher Gefüllt
			else
			{
				echo " ".tm("Speicher","Speicher voll!")."";
				echo " style=\"\" ";
				echo "><b>".nf($val_res[$p->id][$x])."</b></td>";
			}

		}
		echo "</tr>";
		$cnt_res++;
	}
	echo "<tr><td colspan=\"7\"></td></tr>";
	echo "<tr><th>Total</th>";
	for ($x=0;$x<6;$x++)
		echo "<td>".nf($tot_res[$x])."</td>";
	echo "</tr><tr><th>Durchschnitt</th>";
	for ($x=0;$x<6;$x++)
		echo "<th>".nf($tot_res[$x]/$cnt_res)."</th>";
	echo "</tr></table>";



	//
	// Rohstoffproduktion inkl. Energie
	//

	// Ersetzt Bewohnerwerte durch Energiewerte
	$max_prod[5] = 0;
	$min_prod[5] = 9999999999;
	$tot_prod[5] = 0;
	foreach ($planets as $p)
	{
		//Speichert die aktuellen Energieproduktionen in ein Array (Bewohnerproduktion [5] wird überschrieben)
		$val_prod[$p->id][5]=floor($p->prodPower);
		
		// Gibt Min. / Max. aus
		$max_prod[5]=max($max_prod[5],$val_prod[$p->id][5]);
		$min_prod[5]=min($min_prod[5],$val_prod[$p->id][5]);
		$tot_prod[5]+=$val_prod[$p->id][5];	
	}




	tableStart("Produktion");
	echo "<tr><th>Name:</th>
	<th>".RES_METAL."</th>
	<th>".RES_CRYSTAL."</th>
	<th>".RES_PLASTIC."</th>
	<th>".RES_FUEL."</th>
	<th>".RES_FOOD."</th>
	<th>Energie</th></tr>";
	foreach ($planets as $p)
	{
		echo "<tr><td><a href=\"?page=economy&amp;planet_id=".$p->id."\">".$p->name."</a></td>";
		for ($x=0;$x<6;$x++)
		{
			echo "<td";
			if ($max_prod[$x]==$val_prod[$p->id][$x])
			{
				echo "  style=\"color:#0f0\"";
			}
			elseif ($min_prod[$x]==$val_prod[$p->id][$x])
			{
				 echo "  style=\"color:#f00\"";
			}
			else
			{
				 echo "";
			}
			echo ">".nf($val_prod[$p->id][$x])."</td>";
		}
		echo "</tr>";
		$cnt_prod++;
	}
	echo "<tr><td colspan=\"7\"></td></tr>";
	echo "<tr><th>Total</th>";
	for ($x=0;$x<6;$x++)
		echo "<td>".nf($tot_prod[$x])."</td>";
	echo "</tr><tr><th>Durchschnitt</th>";
	for ($x=0;$x<6;$x++)
		echo "<th>".nf($tot_prod[$x]/$cnt_prod)."</th>";
	echo "</tr></table>";
	
	tableStart("Legende");
	echo "<tr>
	<td style=\"color:#f00\">Minimum</td>
	<td style=\"color:#0f0\">Maximum</td>
	<td style=\"font-style:italic\">Speicher bald voll</td>
	<td style=\"font-weight:bold\">Speicher voll</td>
	</tr>";
	echo "</table>";

	echo "<div><br/>
	<input type=\"button\" onclick=\"document.location='?page=economy'\" value=\"Wirtschaft des aktuellen Planeten anzeigen\" />
	</div>";


?>