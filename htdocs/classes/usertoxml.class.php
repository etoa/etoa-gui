<?php

class UserToXml 
{
	private $userId;
    function UserToXml($userId) 
    {
    	$this->userId = intval($userId);
    }
    
    
	function toCacheFile()
	{
		$filename = $this->userId."_".date("Y-m-d_H-i").".xml";
		$file = CACHE_ROOT."/user_xml/".$filename;
		if ($xml =  $this->__toString())
		{
			if ($d=@fopen($file,"w+"))
			{
				fwrite($d,$xml);
				fclose($d);
				return $filename;
			}
			error_msg("Konnte Datei $file nicht zum XML Export öffnen!");			
			return false;
		}
		error_msg("XML Export fehlgeschlagen. User ".$this->userId." nicht gefunden!");			
		return false;
	}
	
	function __toString()
	{
		$res = dbquery("
		SELECT
			users.*,
			alliance_tag,
			alliance_name,
			race_name
		FROM
			users
		LEFT JOIN
			alliances ON user_alliance_id=alliance_id
		LEFT JOIN
			races ON user_race_id=race_id
		WHERE user_id=".$this->userId."
		;");
		if (mysql_num_rows($res)>0)
		{		
			$arr=mysql_fetch_array($res);
			
$xml = "<userbackup>
	<export date=\"".date("d.m.Y, H:i")."\" timestamp=\"".time()."\" />
	<account>
		<id>".$arr['user_id']."</id>	
		<nick>".$arr['user_nick']."</nick>
		<name>".$arr['user_name']."</name>
		<email>".$arr['user_email']."</email>
		<points>".$arr['user_points']."</points>
		<rank>".$arr['user_rank']."</rank>
		<online>".date("d.m.Y, H:i",$arr['user_logouttime'])."</online>		
		<ip>".$arr['user_ip']."</ip>		
		<host>".$arr['user_hostname']."</host>		
		<alliance id=\"".$arr['user_alliance_id']."\" tag=\"".$arr['alliance_tag']."\">".$arr['alliance_name']."</alliance>
		<race id=\"".$arr['user_race_id']."\">".$arr['race_name']."</race>
	</account>
	<planets>";
			$pres=dbquery("
				SELECT
					id,
					planet_name,
					planet_res_metal,
					planet_res_crystal,
					planet_res_plastic,
					planet_res_fuel,
					planet_res_food,
					planet_people,
					planet_type_id,
					type_name,
					planet_user_main
				FROM
					planets
				INNER JOIN
					planet_types ON type_id=planet_type_id
					AND	planet_user_id='".$this->userId."';
			");
			if (mysql_num_rows($pres)>0)
			{
				while ($parr=mysql_fetch_array($pres))
				{
					if ($parr['planet_user_main']==1)
					{
						$mainPlanet = $parr['id'];
					}
					$xml.= "
		<planet id=\"".$parr['id']."\" name=\"".$parr['planet_name']."\" main=\"".$parr['planet_user_main']."\">
			<type id=\"".$parr['planet_type_id']."\">".$parr['type_name']."</type>					
			<metal>".intval($parr['planet_res_metal'])."</metal>
			<crystal>".intval($parr['planet_res_crystal'])."</crystal>
			<plastic>".intval($parr['planet_res_plastic'])."</plastic>
			<fuel>".intval($parr['planet_res_fuel'])."</fuel>
			<food>".intval($parr['planet_res_food'])."</food>
			<people>".intval($parr['planet_people'])."</people>
		</planet>";
				}
			}
			$xml.="
	</planets>
	<buildings>";	
			//Gebäude
			$bres = dbquery("
				SELECT
					building_name,
					buildlist_current_level,
					buildlist_entity_id,
					building_id
				FROM
					buildings
				INNER JOIN
					buildlist
					ON building_id = buildlist_building_id
					AND buildlist_user_id='".$this->userId."'
				ORDER BY
					buildlist_entity_id;
			");
			if (mysql_num_rows($bres)>0)
			{
				while ($barr=mysql_fetch_array($bres))
				{
					$xml.="
		<building planet=\"".$barr['buildlist_entity_id']."\" id=\"".$barr['building_id']."\" level=\"".$barr['buildlist_current_level']."\">".$barr['building_name']."</building>";
				}
			}
			$xml.="
	</buildings>
	<technologies>";	
			//Technologien
			$tres = dbquery("
				SELECT
					tech_name,
					techlist_current_level,
					tech_id
				FROM
					techlist
				INNER JOIN
					technologies
					ON techlist_tech_id = tech_id
					AND techlist_user_id='".$this->userId."';
			");
			if (mysql_num_rows($tres)>0)
			{
				while ($tarr=mysql_fetch_array($tres))
				{
					$xml.="
		<technology id=\"".$tarr['tech_id']."\" level=\"".$tarr['techlist_current_level']."\">".$tarr['tech_name']."</technology>";
				}
			}

			$xml.="
	</technologies>
	<ships>";	
			//Schiffe
			$sres = dbquery("
				SELECT
					ship_name,
					ship_id,
					shiplist_count,
					shiplist_entity_id
				FROM
					shiplist
				INNER JOIN
					ships
					ON shiplist_ship_id = ship_id
					AND shiplist_user_id='".$this->userId."'
					AND shiplist_count>0
				ORDER BY
					shiplist_entity_id;
			");
			if (mysql_num_rows($sres)>0)
			{
				while ($sarr=mysql_fetch_array($sres))
				{
					$xml.="
		<ship planet=\"".$sarr['shiplist_entity_id']."\" id=\"".$sarr['ship_id']."\" count=\"".$sarr['shiplist_count']."\">".$sarr['ship_name']."</ship>";
				}
			}
			//Flotten und deren Schiffe
			$fres=dbquery("
				SELECT
					id
				FROM
					fleet
				WHERE
					user_id='".$this->userId."';
			");
			if (mysql_num_rows($fres)>0)
			{
				while ($farr=mysql_fetch_array($fres))
				{
					$sres = dbquery("
						SELECT
							s.ship_name,
							s.ship_id,
							fs.fs_ship_cnt
						FROM
							fleet_ships AS fs
							INNER JOIN
							ships AS s
							ON fs.fs_ship_id = s.ship_id
							AND fs.fs_ship_faked=0
							AND fs.fs_fleet_id='".$farr['id']."';
					");
					if (mysql_num_rows($sres)>0)
					{
						while ($sarr=mysql_fetch_array($sres))
						{
					$xml.="
		<ship planet=\"".$mainPlanet."\" id=\"".$sarr['ship_id']."\" count=\"".$sarr['fs_ship_cnt']."\">".$sarr['ship_name']."</ship>";
						}
					}
				}
			}			
			$xml.="
	</ships>
	<defenses>";	
			//Verteidigung
			$dres = dbquery("
				SELECT
					d.def_name,
					d.def_id,
					dl.deflist_count,
					dl.deflist_entity_id
				FROM
					defense AS d
				INNER JOIN
					deflist AS dl
				ON d.def_id = dl.deflist_def_id
					AND dl.deflist_user_id='".$this->userId."'
					AND deflist_count>0
				ORDER BY
					dl.deflist_entity_id;
			");
			if (mysql_num_rows($dres)>0)
			{
				while ($darr=mysql_fetch_array($dres))
				{
					$xml.="
		<defense planet=\"".$darr['deflist_entity_id']."\" id=\"".$darr['def_id']."\" count=\"".$darr['deflist_count']."\">".$darr['def_name']."</defense>";
				}
			}
			$xml.="
	</defenses>
";	
			$xml.="</userbackup>";
			return $xml;
		}
		return false;		
	}    
    
}
?>