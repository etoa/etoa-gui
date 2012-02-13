<?PHP
	class DbMaintenance
	{
		/**
		* Tabellen optimieren
		*/
		function optimizeTables($manual=false)
		{
			$res = dbquery("SHOW TABLES;");
			$n = mysql_num_rows($res);
			$cnt=0;
			$tbls = '';
			while ($arr=mysql_fetch_row($res))
			{
				$tbls.=$arr[0];
				$cnt++;
				if ($cnt<$n)
				{
					$tbls.=',';
				}
			}
			$ores = dbquery("OPTIMIZE TABLE ".$tbls.";");
			if ($manual)
			{
				add_log("4",$n." Tabellen wurden manuell optimiert!",time());
				return $ores;
			}
			else
			{
				add_log("4",$n." Tabellen wurden optimiert!",time());
				return $n;
			}
		}
	
		/**
		* Tabellen reparieren
		*
		*@Todo: outsource, is only for >admins
		*/
		function repairTables($manual=false)
		{
			$res = dbquery("SHOW TABLES;");
			$n = mysql_num_rows($res);
			$cnt=0;
			$tbls = '';
			while ($arr=mysql_fetch_row($res))
			{
				$tbls.=$arr[0];
				$cnt++;
				if ($cnt<$n)
				{
					$tbls.=',';
				}
			}
			$ores = dbquery("REPAIR TABLE ".$tbls.";");
			if ($manual)
			{
				add_log("4",$n." Tabellen wurden manuell repariert!",time());
				return $ores;
			}
			else
			{
				add_log("4",$n." Tabellen wurden repariert!",time());
				return $n;
			}	
		}
	
		/**
		* Tabellen prüfen
		*@Todo: outsource, is only for >admins
		*/
		function checkTables()
		{
			$res = dbquery("SHOW TABLES;");
			$n = mysql_num_rows($res);
			$cnt=0;
			$tbls = '';
			while ($arr=mysql_fetch_row($res))
			{
				$tbls.=$arr[0];
				$cnt++;
				if ($cnt<$n)
				{
					$tbls.=',';
				}
			}
			$ores = dbquery("CHECK TABLE ".$tbls.";");
			return $ores;
		}
		
		/**
		* Tabellen analysieren
		*@Todo: outsource, is only for >admins
		*/
		function analyzeTables()
		{
			$res = dbquery("SHOW TABLES;");
			$n = mysql_num_rows($res);
			$cnt=0;
			$tbls = '';
			while ($arr=mysql_fetch_row($res))
			{
				$tbls.=$arr[0];
				$cnt++;
				if ($cnt<$n)
				{
					$tbls.=',';
				}
			}
			$ores = dbquery("ANALYZE TABLE ".$tbls.";");
			return $ores;
		}	
	
	}
?>