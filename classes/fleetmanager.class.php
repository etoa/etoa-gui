<?PHP

	class FleetManager
	{
		private $userId;
		private $allianceId;
		private $count;
		private $fleet;		
		
		function FleetManager($userId,$allianceId=0)
		{
			$this->userId = $userId;
			$this->allianceId = $allianceId;
			$this->count = 0;
			$this->fleet = array();
		}
	
		function countControlledByEntity($entId)
		{
			$res = dbquery("
			SELECT 
				COUNT(id) 
			FROM 
				fleet 
			WHERE 
				user_id='".$this->userId."' 
			AND 
				(
					entity_from='".$entId."' 
					AND status=0
				) 
				OR 
				(
					entity_to='".$entId."' 
					AND status>0  
				);");
			$arr = mysql_fetch_row($res);
			return $arr[0];		
		}
	
		function loadOwn()
		{
			$this->count = 0;
			$this->fleet = array();
			
			//Lädt Flottendaten
			$fres = dbquery("
			SELECT
				id
			FROM
				fleet
			WHERE
				user_id='".$this->userId."'
			ORDER BY
				landtime DESC;");
			if (mysql_num_rows($fres)>0)
			{	
				while ($farr = mysql_fetch_row($fres))
				{
					$this->fleet[$farr[0]] = new Fleet($farr[0]);
					$this->count++;
				}		
			}
		}
		
		function loadForeign()
		{
			$this->count = 0;
			$this->fleet = array();
			
			//Lädt Flottendaten
			// TODO: This is not good query because it needs to know the planet table structure
			$fres = dbquery("
			SELECT
				f.id
			FROM
				fleet f
			INNER JOIN
				planets p
				ON p.id=f.entity_to
				AND p.planet_user_id=".$this->userId."
				AND f.user_id!='".$this->userId."'
			ORDER BY
				landtime DESC;");
			if (mysql_num_rows($fres)>0)
			{	
				while ($farr = mysql_fetch_row($fres))
				{
					$this->fleet[$farr[0]] = new Fleet($farr[0]);
					$this->count++;
				}		
			}
		}
		
		function loadAllianceSupport()
		{
			$this->count = 0;
			$this->fleet = array();
			//Lädt Flottendaten
			// TODO: This is not good query because it needs to know the planet table structure
			$fres = dbquery("
			SELECT
				f.id
			FROM
				fleet f
			INNER JOIN
				users u
				ON u.user_alliance_id='".$this->allianceId."'
				AND u.user_id=f.user_id
				AND action='support'
			ORDER BY
				landtime DESC;");
			if (mysql_num_rows($fres)>0)
			{	
				while ($farr = mysql_fetch_row($fres))
				{
					$this->fleet[$farr[0]] = new Fleet($farr[0]);
					$this->count++;
				}		
			}
		}
		
		function loadAllianceAttacks()
		{
			$this->count = 0;
			$this->fleet = array();
			//Lädt Flottendaten
			// TODO: This is not good query because it needs to know the planet table structure
			$fres = dbquery("
			SELECT
				id
			FROM
				fleet
			WHERE
				next_id='".$this->allianceId."'
				AND leader_id=id
				AND action='alliance'
			ORDER BY
				landtime DESC;");
			if (mysql_num_rows($fres)>0)
			{	
				while ($farr = mysql_fetch_row($fres))
				{
					$this->fleet[$farr[0]] = new Fleet($farr[0]);
					$this->count++;
				}		
			}
		}
		
		


			
		
		function count()
		{
			return $this->count;
		}
		
		function getAll()
		{
			return $this->fleet;
		}
	
	}

?>