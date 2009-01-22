<?PHP
	//
	// Author: MrCage <mrcage@etoa.ch>
	// 
	// Reference implementation of new battle script ideas
	// 
	// Outline:
	// - Fights ship to ship
	// - Target is choosen randomly			(Here we could implement some more strategy)
	// - Shields get reloaded every round
	// - Objects with less thamn 20% structure explode 
	// - Objects with multifire against certains other objects
	//   get {multifire value}times a shoot at other objects. If the choosen target
	//   is no more of the type of the first object fired at, multifire breaks. (Thus 
	//   we can implement rock-paper-scissors (rps) principle)
	// - Healing is not implemented at this time, but should not be a big issue.
	// 	 What we have think of how the healing unit chooses its targets to be healed. One idea is to
	// 	 maintain a list of objects which got damage. The healer once per round chooses one of the id's 
	// 	 and heals it's structure. The questions is if it can pick more than one targets if it has 
	// 	 still some "heal-energy" and if yes, how many it can pick.
	// - Defense is NOT implemented yet
	// - Other ideas (not implemented yet)
	// 		* Ships which fight at different ranges (close, medium, far)
	// 			all ships start at far range: ships with far range weapons can already start to fire,
	// 			ships with medium range need one round to get to medium range, ships with close range
	// 			need to get 2 rounds to get close (given that enemy ships stays far)
	// 			so we have 3 positions ships can reside in. 
	// 		* Target selection: First idea is that player can set tactic: concentrate on fight at most targets
	// 			as possible similarly or concentrate on one target. this influences target selection.
	// 			Second idea that defender can have some sort of	interfering transmitter bonus on ships
	// 			which can make a target selection onto that item to fail. the attacker can have some sort of 
	// 			target computer which opposes this jamming.
	// 		* Different weapons on different shields / hulls: The idea behind that is that every ship
	// 			has some sort of hull/shield/weapon. every weapon is good agains a specific shield/structure type
	// 			and has malus against others. This implements also something of the rps-princiople.
	// 			
	// 			
	//
	// Optimization hints for c++ implementation
	// - When accessing an array value multiple times, save it to a variable; 
	// since variable values coult be kept in registers/cpu cache rather than ram
	// - Take care of the ordering of conditions in if() and while() statemets; they
	// are checked from the left. if first one fails the programm can already continue and does
	// not check the rest of the conditions (given &&-relation)
	// - If an array has to be countet multiple times (and the number elements didn't change) save
	// the counting result to a variable
	// - Always free memory of unused variables
	// - Multifire values coult be kept in memory for some duration (some minutes or so) such that they
	// wouldn't have to be reloaded every battle
	// - We have to test if it's better to store everything in the $aitems arrray and not use the ship array
	// or if it's better separate is as it is done in this implementation

	define('CLASS_ROOT',"../classes");

	require_once("../conf.inc.php");
	require_once("../functions.php");

	dbconnect();

	$conf = get_all_config();
	include("../def.inc.php");
	
	/*
	$afleet = array(
		7=>9,
		14=>7,
		3=>20
	);

	$dfleet = array(
		14=>4,
		2=>20,
		8=>10,
	);
	
	*/	

	$afleet = array(
		6=>9,
	);

	$dfleet = array(
		2=>30,
		3=>40,
	);


	$aitems = array();
	$ditems = array();

	$ships = array();

	$astructure = 0;
	$ashield = 0;
	$aweapon = 0;
	$acount = 0;
	
	$dstructure = 0;
	$dshield = 0;
	$dweapon = 0;
	$dcount = 0;	

	// Load attacker
	foreach ($afleet as $k=>$v)
	{
		$res = dbquery("
		SELECT
			ship_name as n,
			ship_structure as st,
			ship_shield as sh,
			ship_weapon as wp,
			ship_heal as hl
		FROM
			ships
		WHERE
			ship_id=".$k."
		LIMIT 1;
		");
		$arr = mysql_fetch_assoc($res);
		$arr['mf'] = array();
		for ($i=0;$i<$v;$i++)
		{
			// id, shield, structure, exlode
			$aitems[] = array($k,$arr['sh'],$arr['st'],0);
		}
		$ships[$k] = $arr;
		$astructure += $v*$arr['st'];
		$ashield += $v*$arr['sh'];
		$aweapon += $v*$arr['wp'];
		$acount += $v;
	}

	// Load defender
	foreach ($dfleet as $k=>$v)
	{
		$res = dbquery("
		SELECT
			ship_name as n,
			ship_structure as st,
			ship_shield as sh,
			ship_weapon as wp,
			ship_heal as hl
		FROM
			ships
		WHERE
			ship_id=".$k."
		LIMIT 1;
		");
		$arr = mysql_fetch_assoc($res);
		$arr['mf'] = array();
		for ($i=0;$i<$v;$i++)
		{
			$ditems[] = array($k,$arr['sh'],$arr['st'],0);
		}
		$ships[$k] = $arr;
		$dstructure += $v*$arr['st'];
		$dshield += $v*$arr['sh'];
		$dweapon += $v*$arr['wp'];
		$dcount += $v;
	}
	
	
	// Load multifire
	$res = dbquery("
		SELECT
			attacker_id,
			defender_id,
			value
		FROM
			multifire
		");
	while ($arr = mysql_fetch_assoc($res))
	{
		if (isset($ships[$arr['attacker_id']]))
		{
			$ships[$arr['attacker_id']]['mf'][$arr['defender_id']] = $arr['value'];
		}
	}
	
	$stopwatch = microtime(true);



	echo "Attacker: Objects: $acount, ST: $astructure, SH: $ashield, WP: $aweapon<br/>";
	echo "Defender: Objects: $dcount, ST: $dstructure, SH: $dshield, WP: $dweapon<br/>";
	
	$rounds = 5;
	// Loop rounds
	for ($r=0;$r<5;$r++)
	{
		$aicnt = count($aitems);
		$dicnt = count($ditems);
		$adestroy = array();
		$ddestroy = array();

		if ($aicnt == 0 ||$dicnt == 0)
			break;

		echo "<h2>Round $r</h2>";

		// Overview
		status();
		
		// For every attacker item...
		for ($i=0;$i<$aicnt;$i++)
		{
			$mfval = -1;	// Multifire counter
			$mfid = -1; // Saved multifire opponent id
			do
			{
				// Find a target...
				$j = mt_rand(0,$dicnt-1);
				
				// Load item id of opponent
				$did = $ditems[$j][0];
				
				// Look if we have multifire against that opponent
				if ($mfid == -1 && isset($ships[$aitems[$i][0]]['mf'][$did]))
				{
					$mfval = $ships[$aitems[$i][0]]['mf'][$did];
					$mfid = $did;
				}
				// If multifire active and opponent type changed, break
				elseif ($mfid > -1 && $mfid != $did)
				{
					break;
				}
				$mfval--;
	
				// Get damage
				$dmg = $ships[$aitems[$i][0]]['wp'];
				
				// Do Shield dmg
				if ($dmg <= $ditems[$j][1])
				{
					$ditems[$j][1] -= $dmg;
				}
				else
				{
					// Substract dmg which get into shield
					$dmg -= $ditems[$j][1];
					// Set shields to zero
					$ditems[$j][1]= 0;
					// Do structure dmg
					if ($dmg <= $ditems[$j][2])
						$ditems[$j][2] -= $dmg;
					else
					{
						// Destroy
						$ditems[$j][3] = 1;
					}
				}
			// Loop for multifire
			} while ($mfval > 0);
		}

		// For every defender item...
		for ($i=0;$i<$dicnt;$i++)
		{
			$mfval = -1;	// Multifire counter
			$mfid = -1; // Saved multifire opponent id
			do
			{			
				// Find a target...
				$j = mt_rand(0,$aicnt-1);
	
				// Load item id of opponent
				$did = $aitems[$j][0];
				
				// Look if we have multifire against that opponent
				if ($mfid == -1 && isset($ships[$ditems[$i][0]]['mf'][$did]))
				{
					$mfval = $ships[$ditems[$i][0]]['mf'][$did];
					$mfid = $did;
				}
				// If multifire active and opponent type changed, break
				elseif ($mfid > -1 && $mfid != $did)
				{
					break;
				}
				$mfval--;
	
	
				// Shield dmg
				$dmg = $ships[$ditems[$i][0]]['wp'];
				if ($dmg <= $aitems[$j][1])
				{
					$aitems[$j][1] -= $dmg;
				}
				else
				{
					// Substract dmg which get into shield
					$dmg -= $aitems[$j][1]; 
					// Set shields to zero
					$aitems[$j][1] = 0;
					// Struct dmg
					if ($dmg <= $aitems[$j][2])
						$aitems[$j][2] -= $dmg;
					else
					{
						// Destroy
						$aitems[$j][3] = 1;
					}
				}
			// Loop for multifire
			} while ($mfval > 0);			
		}		
		
		//
		// In the cleanup section, we move all remaining items to a new temporary
		// array, delete the old one and set the old to the new one
		//
		
		// Cleanup attacker
		$taitems = array();
		for ($i=0;$i<$aicnt;$i++)
		{
			// Check if destroy flag not set
			if ($aitems[$i][3]==0)
			{
				// Check if at least 20% of the structure exists, otherwise ship explodes
				$hull = $ships[$aitems[$i][0]]['st'];
				if ($aitems[$i][2] > $hull * 0.2)
				{
					// Reload shield
					$aitems[$i][1] = $ships[$aitems[$i][0]]['sh'];
					$taitems[] = $aitems[$i];
				}
				else
					$afleet[$aitems[$i][0]]--;
			}
			else
				$afleet[$aitems[$i][0]]--;
		}
		unset($aitems);
		$aitems = $taitems;
		unset($taitems);

		// Cleanup defender
		$tditems = array();
		for ($i=0;$i<$dicnt;$i++)
		{
			// Check if destroy flag not set
			if ($ditems[$i][3]==0)
			{
				// Check if at least 20% of the structure exists, otherwise ship explodes
				$hull = $ships[$ditems[$i][0]]['st'];
				if ($ditems[$i][2] > $hull * 0.2)
				{				
					// Reload shield
					$ditems[$i][1] = $ships[$ditems[$i][0]]['sh'];
					$tditems[] = $ditems[$i];
				}
				else
					$dfleet[$ditems[$i][0]]--;
			}
			else
				$dfleet[$ditems[$i][0]]--;
		}
		unset($ditems);
		$ditems = $tditems;
		unset($tditems);


	}

	echo "<h2>Result</h2>";
	status();

		$aicnt = count($aitems);
		$dicnt = count($ditems);

	echo "<br/><br/>";
	if ($aicnt == 0)
		echo "<b>Defender wins! $aicnt : $dicnt<br/><br/>";
	elseif ($dicnt == 0)
		echo "<b>Attacker wins! $aicnt : $dicnt<br/><br/>";
	else
		echo "<b>Draw battle! $aicnt : $dicnt<br/><br/>";
		
	
	echo "<b>Duration: ".((microtime(true) - $stopwatch)*1000)." ms</b>";	
	
	
	
	function status()
	{
		global $afleet,$dfleet,$ships;

		echo "<table>
		<tr><th>Objecs</th>";
		foreach ($afleet as $k=>$v)
		{
			echo "<th>".$ships[$k]['n']."</th>";
		}
		echo "<th>TOTAL</th></tr>";
		echo "<tr><th>Count</th>";
		$tot=0;
		foreach ($afleet as $k=>$v)
		{
			echo "<td>$v</td>";
			$tot+=$v;
		}		
		echo "<td>".$tot."</td></tr>";
		echo "<tr><th>Shield</th>";
		$tot=0;
		foreach ($afleet as $k=>$v)
		{
			$i = ($ships[$k]['sh']*$v);
			echo "<td>".$i."</td>";
			$tot+=$i;
		}		
		echo "<td>".$tot."</td></tr>";
		echo "<tr><th>Structure</th>";
		$tot=0;
		foreach ($afleet as $k=>$v)
		{
			$i = ($ships[$k]['st']*$v);
			echo "<td>".$i."</td>";
			$tot+=$i;
		}		
		echo "<td>".$tot."</td></tr>";		
		echo "<tr><th>Weapons</th>";
		$tot=0;
		foreach ($afleet as $k=>$v)
		{
			$i = ($ships[$k]['wp']*$v);
			echo "<td>".$i."</td>";
			$tot+=$i;
		}		
		echo "<td>".$tot."</td></tr>";
		echo "</table>";
		
		
		echo "<table>
		<tr><th>Objecs</th>";
		foreach ($dfleet as $k=>$v)
		{
			echo "<th>".$ships[$k]['n']."</th>";
		}
		echo "<th>TOTAL</th></tr>";
		echo "<tr><th>Count</th>";
		$tot=0;
		foreach ($dfleet as $k=>$v)
		{
			echo "<td>$v</td>";
			$tot+=$v;
		}		
		echo "<td>".$tot."</td></tr>";
		echo "<tr><th>Shield</th>";
		$tot=0;
		foreach ($dfleet as $k=>$v)
		{
			$i = ($ships[$k]['sh']*$v);
			echo "<td>".$i."</td>";
			$tot+=$i;
		}		
		echo "<td>".$tot."</td></tr>";
		echo "<tr><th>Structure</th>";
		$tot=0;
		foreach ($dfleet as $k=>$v)
		{
			$i = ($ships[$k]['st']*$v);
			echo "<td>".$i."</td>";
			$tot+=$i;
		}		
		echo "<td>".$tot."</td></tr>";		
		echo "<tr><th>Weapons</th>";
		$tot=0;
		foreach ($dfleet as $k=>$v)
		{
			$i = ($ships[$k]['wp']*$v);
			echo "<td>".$i."</td>";
			$tot+=$i;
		}		
		echo "<td>".$tot."</td></tr>";
		echo "</table>";

		
	}




?>