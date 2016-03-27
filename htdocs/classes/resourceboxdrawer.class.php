<?PHP
class ResourceBoxDrawer
{
	public static function getHTML(Planet $p, $small = false)
	{
		return $small ? self::getHTMLSmall($p) : self::getHTMLNormal($p);
	}

	/**
	* Displays a box with resources, power and population
	*/
	public static function getHTMLNormal(Planet $p)
	{
		$style0 = "resmetalcolor";
		$style1 = "rescrystalcolor";
		$style2 = "resplasticcolor";
		$style3 = "resfuelcolor";
		$style4 = "resfoodcolor";
		$style5 = "respeoplecolor";

		if ($p->storeMetal<=floor($p->resMetal) && floor($p->resMetal)>0)
		{
			$style0="resfullcolor";
		}
		if ($p->storeCrystal<=floor($p->resCrystal) && floor($p->resCrystal)>0)
		{
			$style1="resfullcolor";
		}
		if ($p->storePlastic<=floor($p->resPlastic) && floor($p->resPlastic)>0)
		{
			$style2="resfullcolor";
		}
		if ($p->storeFuel<=floor($p->resFuel) && floor($p->resFuel)>0)
		{
			$style3="resfullcolor";
		}
		if ($p->storeFood<=floor($p->resFood) && floor($p->resFood)>0)
		{
			$style4="resfullcolor";
		}
		if ($p->people_place<=floor($p->people) && floor($p->people)>0)
		{
			$style5="resfullcolor";
		}
		if(floor($p->prodPower)-floor($p->usePower)<0)
		{
			$style6="resfullcolor";
			$power_rest = floor($p->prodPower)-floor($p->usePower);
		}
		else
		{
			$style6="respowercolor";
			$power_rest = floor($p->prodPower)-floor($p->usePower);
		}

		$rtn = tableStart("Ressourcen")."<tr>
		<th class=\"resBoxTitleCell\"><div class=\"resmetal\">".RES_METAL."</div></th>
		<th class=\"resBoxTitleCell\"><div class=\"rescrystal\">".RES_CRYSTAL."</div></th>
		<th class=\"resBoxTitleCell\"><div class=\"resplastic\">".RES_PLASTIC."</div></th>
		<th class=\"resBoxTitleCell\"><div class=\"resfuel\">".RES_FUEL."</div></th>
		<th class=\"resBoxTitleCell\"><div class=\"resfood\">".RES_FOOD."</div></th>
		<th class=\"resBoxTitleCell\"><div class=\"respeople\">Bewohner</div></th>
		<th class=\"resBoxTitleCell\"><div class=\"respower\">Energie</div></th>
		</tr><tr>"

			. self::getResourceRow($style0, RES_METAL, "images/resources/metal.png", $p->resMetal(), $p->storeMetal, $p->prodMetal)
			. self::getResourceRow($style1, RES_CRYSTAL, "images/resources/crystal.png", $p->resCrystal(), $p->storeCrystal, $p->prodCrystal)
			. self::getResourceRow($style2, RES_PLASTIC, "images/resources/plastic.png", $p->resPlastic(), $p->storePlastic, $p->prodPlastic)
			. self::getResourceRow($style3, RES_FUEL, "images/resources/fuel.png", $p->resFuel(), $p->storeFuel, $p->prodFuel)
			. self::getResourceRow($style4, RES_FOOD, "images/resources/food.png", $p->resFood(), $p->storeFood, $p->prodFood)
			. self::getResourceRow($style5, "Bevölkerung", "images/resources/people.png", $p->people(), $p->people_place, $p->prodPeople)

			. "<td class=\"$style6\" ".mTT(RES_POWER,"<img width=\"40px\" height=\"40px\" src=\"images/resources/power.png\" style=\"float:left;margin-right:5px;\"/> <b>Produktion:</b> ".nf($p->prodPower)."<br/><b>Verfügbar:</b> ".nf($power_rest)."<br/><b>Verbrauch:</b> ".nf($p->usePower)."<br style=\"clear:both;\"/>").">".nf($power_rest)."</td>
		</tr></table>";

		return $rtn;
	}

	public static function getHTMLSmall(Planet $p)
	{
		$style0 = 'resmetal';
		$style1 = 'rescrystal';
		$style2 = 'resplastic';
		$style3 = 'resfuel';
		$style4 = 'resfood';
		$style5 = 'respeople';
		$style6 = '';

		if ($p->storeMetal<=floor($p->resMetal) && floor($p->resMetal)>0)
		{
			$style0 .= ' resfullcolor';
		}
		if ($p->storeCrystal<=floor($p->resCrystal) && floor($p->resCrystal)>0)
		{
			$style1 .= 'resfullcolor';
		}
		if ($p->storePlastic<=floor($p->resPlastic) && floor($p->resPlastic)>0)
		{
			$style2 .= ' resfullcolor';
		}
		if ($p->storeFuel<=floor($p->resFuel) && floor($p->resFuel)>0)
		{
			$style3 .= ' resfullcolor';
		}
		if ($p->storeFood<=floor($p->resFood) && floor($p->resFood)>0)
		{
			$style4 .= ' resfullcolor';
		}
		if ($p->people_place<=floor($p->people) && floor($p->people)>0)
		{
			$style5 = ' resfullcolor';
		}
		if(floor($p->prodPower)-floor($p->usePower)<0)
		{
			$style6 = 'respower resfullcolor';
			$power_rest = floor($p->prodPower)-floor($p->usePower);
		}
		else
		{
			$power_rest = floor($p->prodPower)-floor($p->usePower);
		}
		$rtn = "<div id=\"resbox\">
		<div id=\"resboxheader\">Resourcen</div>
		<div id=\"resboxcontent\">"

			. self::getResourceRow($style0, RES_METAL, "images/resources/metal.png", $p->resMetal(), $p->storeMetal, $p->prodMetal, true)
			. self::getResourceRow($style1, RES_CRYSTAL, "images/resources/crystal.png", $p->resCrystal(), $p->storeCrystal, $p->prodCrystal, true)
			. self::getResourceRow($style2, RES_PLASTIC, "images/resources/plastic.png", $p->resPlastic(), $p->storePlastic, $p->prodPlastic, true)
			. self::getResourceRow($style3, RES_FUEL, "images/resources/fuel.png", $p->resFuel(), $p->storeFuel, $p->prodFuel, true)
			. self::getResourceRow($style4, RES_FOOD, "images/resources/food.png", $p->resFood(), $p->storeFood, $p->prodFood, true)
			. self::getResourceRow($style5, "Bevölkerung", "images/resources/people.png", $p->people(), $p->people_place, $p->prodPeople, true)

			. "<span class=\"respower ".$style6."\" ".mTT(RES_POWER,"<img src=\"images/resources/power.png\" style=\"float:left;margin-right:5px;\"/> <b>Produktion:</b> ".nf($p->prodPower)."<br/><b>Verfügbar:</b> ".nf($power_rest)."<br/><b>Verbrauch:</b> ".nf($p->usePower)."<br style=\"clear:both;\"/>").">".nf($power_rest,0,1)."</span>
		</div>
		</div>";

		return $rtn;
	}

	private static function getResourceRow($style, $title, $icon, $amount, $store, $production, $shortAmount = false)
	{
		return sprintf(
			$shortAmount ? '<span class="%s" %s>%s</span>' : '<td class="%s" %s>%s</td>',
			$style,
			self::getResourceTootltip($title, $icon, $amount, $store, $production),
			$shortAmount ? nf($amount, 0, 1) : nf(floor($amount))
		);
	}

	private static function getResourceTootltip($title, $icon, $amount, $store, $production)
	{
		$remainingStore = $store - $amount;
		$storeFullMessage = '';
		if ($production > 0 && $remainingStore > 0) {
			$storeFullMessage = sprintf('<br><b>Voll in:</b> %s', tf(($remainingStore / $production) * 3600));
		}

		return mTT(
			$title,
			sprintf(
				'<img width="40px" height="40px" src="%s" style="float:left;margin-right:5px;"/> <b>Vorhanden:</b> %s<br/><b>Speicher:</b> %s%s<br style=\"clear:both;\"/>',
				$icon,
				nf($amount),
				nf($store),
				$storeFullMessage
			)
		);
	}
}
