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
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo '<h1>Planetenstatistik</h1>';

	echo "<input type=\"button\" onclick=\"document.location='?page=economy'\" value=\"Wirtschaft des aktuellen Planeten anzeigen\" /><br/><br/>";
	echo "<div align=\"center\">";
	echo "<table>";
	echo "<tr><td class=\"tbldata2\">Minimum</td>
	<td class=\"tbldata3\">Maximum</td>
	<td class=\"tbldata\" style=\"font-style:italic\">Speicher bald voll</td>
	<td class=\"tbldata\" style=\"font-weight:bold\">Speicher voll</td>
	</tr>";
	echo "</table>";
	echo "</div><br><br>";

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
		$val_res[$p->id][0]=floor($p->res->metal);
		$val_res[$p->id][1]=floor($p->res->crystal);
		$val_res[$p->id][2]=floor($p->res->plastic);
		$val_res[$p->id][3]=floor($p->res->fuel);
		$val_res[$p->id][4]=floor($p->res->food);
		$val_res[$p->id][5]=floor($p->people);

		for ($x=0;$x<6;$x++)
		{
			$max_res[$x]=max($max_res[$x],$val_res[$p->id][$x]);
			$min_res[$x]=min($min_res[$x],$val_res[$p->id][$x]);
			$tot_res[$x]+=$val_res[$p->id][$x];
		}

		//Speichert die aktuellen Rohstoffproduktionen in ein Array
		$val_prod[$p->id][0]=floor($p->prod->metal);
		$val_prod[$p->id][1]=floor($p->prod->crystal);
		$val_prod[$p->id][2]=floor($p->prod->plastic);
		$val_prod[$p->id][3]=floor($p->prod->fuel);
		$val_prod[$p->id][4]=floor($p->prod->food);
		$val_prod[$p->id][5]=floor($p->prod->people);

		for ($x=0;$x<6;$x++)
		{
			$max_prod[$x]=max($max_prod[$x],$val_prod[$p->id][$x]);
			$min_prod[$x]=min($min_prod[$x],$val_prod[$p->id][$x]);
			$tot_prod[$x]+=$val_prod[$p->id][$x];
		}

		//Speichert die aktuellen Speicher in ein Array
		$val_store[$p->id][0]=floor($p->store->metal);
		$val_store[$p->id][1]=floor($p->store->crystal);
		$val_store[$p->id][2]=floor($p->store->plastic);
		$val_store[$p->id][3]=floor($p->store->fuel);
		$val_store[$p->id][4]=floor($p->store->food);
		$val_store[$p->id][5]=floor($p->people_place);

		//Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

		//Titan
		if($p->prod->metal>0)
		{
      if ($p->store->metal - $p->res->metal > 0)
      {
      	$val_time[$p->id][0]=ceil(($p->store->metal-$p->res->metal)/$p->prod->metal*3600);
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
		if($p->prod->crystal>0)
		{
      if ($p->store->crystal - $p->res->crystal > 0)
      {
      	$val_time[$p->id][1]=ceil(($p->store->crystal-$p->res->crystal)/$p->prod->crystal*3600);
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
		if($p->prod->plastic>0)
		{
      if ($p->store->plastic - $p->res->plastic > 0)
      {
        $val_time[$p->id][2]=ceil(($p->store->plastic-$p->res->plastic)/$p->prod->plastic*3600);
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
		if($p->prod->fuel>0)
		{
      if ($p->store->fuel - $p->res->fuel > 0)
      {
       	$val_time[$p->id][3]=ceil(($p->store->fuel-$p->res->fuel)/$p->prod->fuel*3600);
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
		if($p->prod->food>0)
		{
	    if ($p->store->food - $p->res->food > 0)
	    {
	      $val_time[$p->id][4]=ceil(($p->store->food-$p->res->food)/$p->prod->food*3600);
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
		if($p->prod->people>0)
		{
      if ($p->people_place - $p->people > 0)
      {
        $val_time[$p->id][5]=ceil(($p->people_place-$p->people)/$p->prod->people*3600);
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

	echo "<h2>Rohstoffe und Bewohner</h2><br>";
	echo "<table class=\"tbl\">";
	echo "<tr><th class=\"tbltitle\">Name:</th>
	<th class=\"tbltitle\">".RES_METAL."</th>
	<th class=\"tbltitle\">".RES_CRYSTAL."</th>
	<th class=\"tbltitle\">".RES_PLASTIC."</th>
	<th class=\"tbltitle\">".RES_FUEL."</th>
	<th class=\"tbltitle\">".RES_FOOD."</th>
	<th class=\"tbltitle\">Bewohner</th></tr>";
	foreach ($planets as $p)
	{
		echo "<tr><td class=\"tbldata\"><a href=\"?page=economy&amp;planet_id=".$p->id."\">".$p->name."</a></td>";
		for ($x=0;$x<6;$x++)
		{
			echo "<td";
			if ($max_res[$x]==$val_res[$p->id][$x])
			{
				echo " class=\"tbldata3\"";
			}
			elseif ($min_res[$x]==$val_res[$p->id][$x])
			{
				 echo " class=\"tbldata2\"";
			}
			else
			{
				 echo " class=\"tbldata\"";
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
	echo "<tr><td colspan=\"6\"></td></tr>";
	echo "<tr><th class=\"tbltitle\">Total</th>";
	for ($x=0;$x<6;$x++)
		echo "<td class=\"tbltitle\">".nf($tot_res[$x])."</td>";
	echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
	for ($x=0;$x<6;$x++)
		echo "<td class=\"tbltitle\">".nf($tot_res[$x]/$cnt_res)."</td>";
	echo "</tr></table><br/><br>";



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
		$val_prod[$p->id][5]=floor($p->prod->power);
		
		// Gibt Min. / Max. aus
		$max_prod[5]=max($max_prod[5],$val_prod[$p->id][5]);
		$min_prod[5]=min($min_prod[5],$val_prod[$p->id][5]);
		$tot_prod[5]+=$val_prod[$p->id][5];	
	}




	echo "<h2>Produktion</h2><br>";
	echo "<table class=\"tbl\">";
	echo "<tr><th class=\"tbltitle\">Name:</th>
	<th class=\"tbltitle\">".RES_METAL."</th>
	<th class=\"tbltitle\">".RES_CRYSTAL."</th>
	<th class=\"tbltitle\">".RES_PLASTIC."</th>
	<th class=\"tbltitle\">".RES_FUEL."</th>
	<th class=\"tbltitle\">".RES_FOOD."</th>
	<th class=\"tbltitle\">Energie</th></tr>";
	foreach ($planets as $p)
	{
		echo "<tr><td class=\"tbldata\"><a href=\"?page=economy&amp;planet_id=".$p->id."\">".$p->name."</a></td>";
		for ($x=0;$x<6;$x++)
		{
			echo "<td";
			if ($max_prod[$x]==$val_prod[$p->id][$x])
			{
				echo " class=\"tbldata3\"";
			}
			elseif ($min_prod[$x]==$val_prod[$p->id][$x])
			{
				 echo " class=\"tbldata2\"";
			}
			else
			{
				 echo " class=\"tbldata\"";
			}
			echo ">".nf($val_prod[$p->id][$x])."</td>";
		}
		echo "</tr>";
		$cnt_prod++;
	}
	echo "<tr><td colspan=\"6\"></td></tr>";
	echo "<tr><th class=\"tbltitle\">Total</th>";
	for ($x=0;$x<6;$x++)
		echo "<td class=\"tbltitle\">".nf($tot_prod[$x])."</td>";
	echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
	for ($x=0;$x<6;$x++)
		echo "<td class=\"tbltitle\">".nf($tot_prod[$x]/$cnt_prod)."</td>";
	echo "</tr></table><br/>";

?>