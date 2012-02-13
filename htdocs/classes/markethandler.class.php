<?php
/**
 * Description of MarketHandler
 *
 * @author Nicolas
 */
class MarketHandler
{
	




	static function calcRate($ts=null)
	{
		// Resulting rates
		$rates = array_fill(0,NUM_RESOURCES,1);
		$res_rates = array_fill(0,NUM_RESOURCES,1);

		$where = $ts!= null ? ' WHERE timestamp<='.$ts.' ' : '';

		// Load previous rates
		$res = dbquery("
		SELECT
			*
		FROM
			market_rates
		".$where."
		ORDER BY
			id DESC
		LIMIT ".MARKET_RATES_COUNT.";");
		$nr = mysql_num_rows($res);
		$rc = 0;
		while ($arr = mysql_fetch_assoc($res))
		{
			// Weight factor calculation. Insert any other formula if you desire so.
			// Newer values should weight more than older values
			$factor = $nr-$rc;
			//$factor = log($nr-$rc);

			// For every resource calculate the ration and multiply with the weight factor
			for ($i=0;$i<NUM_RESOURCES;$i++)
			{
				if ($arr['supply_'.$i]>0)
				{
					$r = ($arr['demand_'.$i] / $arr['supply_'.$i]);
					if ($r > MARKET_RATE_MAX)
						$rates[$i] += MARKET_RATE_MAX * $factor;
					if ($r < MARKET_RATE_MIN)
						$rates[$i] += MARKET_RATE_MIN * $factor;
					else
						$rates[$i] += $r * $factor;
				}
				else
				{
					if ($arr['demand_'.$i]>0)
					{
						$rates[$i] += MARKET_RATE_MAX * $factor;
					}
					else
					{
						$rates[$i] += 1 * $factor;
					}
				}
			}
			$rc++;
		}
		// Normalize the resulting values
		$normalizer = array_sum($rates) / NUM_RESOURCES;
		for ($i=0;$i<NUM_RESOURCES;$i++)
		{
			$rates[$i] = round($rates[$i]/$normalizer,2);
		}
		
		//Adding planet/fleet res in universe
		$pres = dbquery("
					   SELECT
					   		SUM(planet_res_metal) as metal,
							SUM(planet_res_crystal) as crystal,
							SUM(planet_res_plastic) as plastic,
							SUM(planet_res_fuel) as fuel,
							SUM(planet_res_food) as food
						FROM
							planets
						INNER JOIN
							users
	       				ON
                    		planet_user_id=user_id
                    		AND user_ghost=0
						;");
		$fres = dbquery("
						SELECT
							SUM(res_metal) as metal,
							SUM(res_crystal) as crystal,
							SUM(res_plastic) as plastic,
							SUM(res_fuel) as fuel,
							SUM(res_food) as food
						FROM
							fleet
						");
		$parr = mysql_fetch_array($pres);
		$farr = mysql_fetch_array($fres);
		
		if ($parr!=null && $farr!=null && isset($parr[0]) && isset($farr[0]))
		{

			for ($i=0;$i<NUM_RESOURCES;$i++)
			{
				$res_rates[$i] = 1/($parr[$i]+$farr[$i]);
			}
		
			$normalizer = array_sum($res_rates) / NUM_RESOURCES;
			for ($i=0;$i<NUM_RESOURCES;$i++)
			{
				$res_rates[$i] = round($res_rates[$i]/$normalizer,2);
			}
		
			for ($i=0;$i<NUM_RESOURCES;$i++)
			{
				$rates[$i] = $res_rates[$i]+$rates[$i];
			}
		
			$normalizer = array_sum($rates) / NUM_RESOURCES;
			for ($i=0;$i<NUM_RESOURCES;$i++)
			{
				$rates[$i] = round($rates[$i]/$normalizer,2);
			}
		}
		else
		{
                        for ($i=0;$i<NUM_RESOURCES;$i++)
                        {
				$rates[$i] = 1;
			}
		}

		return $rates;
	}

	/**
	 * Update market resource rates basen on previous demand and supply
	 */
	static function updateRates()
	{		
		// Load config
		$cfg = Config::getInstance();

		$rates = self::calcRate();

		$sf = $sv = "";
		for ($i=0;$i<NUM_RESOURCES;$i++)
		{		
			$cfg->set('market_rate_'.$i,$rates[$i]);
			$sf .="rate_".$i.",";
			$sv .=$rates[$i].",";
		}

		// Add a new row to the rates table. This row gets filled from now on with buy results
		dbquery("
		INSERT INTO
			market_rates
		(
			$sf
			timestamp
		)
		VALUES
		(
			$sv
			".time()."
		)
		");

		// Remove old values
		$res = dbquery("SELECT id FROM market_rates ORDER BY id DESC LIMIT ".(MARKET_RATES_COUNT*2).", 1");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			dbquery("
			DELETE FROM
				market_rates
			WHERE
				id < ".$arr[0]."
			");
		}
	}

	/**
	* Add resources when a transaction is made
	*/
	static function addResToRate($supply,$demand)
	{
		global $resNames;
		$res = dbquery("
		SELECT
			id
		FROM
			market_rates
		ORDER BY
			id DESC
		LIMIT 1");
		$arr = mysql_fetch_row($res);
		$id = $arr[0];
		$ssql="";
		foreach ($resNames as $rk => $rn)
		{
			if ($ssql!="")
				$ssql.=",";
			$ssql.= "supply_".$rk."=".$supply[$rk].",demand_".$rk."=".$demand[$rk]."";
		}
		dbquery("
		UPDATE
			market_rates
		SET
			$ssql
		WHERE
			id=".$id."
		");
	}

/*
	static function randomRates($n=MARKET_RATES_COUNT)
	{
		for ($i=0;$i<$n;$i++)
		{
			dbquery("
			UPDATE
				market_rates
			SET
					supply_0=".mt_rand(0,99999).",
					supply_1=".mt_rand(0,99999).",
					supply_2=".mt_rand(0,99999).",
					supply_3=".mt_rand(0,99999).",
					supply_4=".mt_rand(0,99999).",
					supply_5=".mt_rand(0,99999).",
					demand_0=".mt_rand(0,99999).",
					demand_1=".mt_rand(0,99999).",
					demand_2=".mt_rand(0,99999).",
					demand_3=".mt_rand(0,99999).",
					demand_4=".mt_rand(0,99999).",
					demand_5=".mt_rand(0,99999)."
			ORDER BY
				id DESC
			LIMIT 1;");
			self::updateRates();
		}
	}*/


}
?>
