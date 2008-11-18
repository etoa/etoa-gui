<?PHP
	class TabControl
	{
		private $name;
		private $count;
		private $current;
		
		function TabControl($name,$elements,$default=0,$width="670px",$tabWidth="140px")
		{
			echo "<div class=\"tabOuter\" style=\"width:".$width."\">";
			$cnt=0;
			if ($tabWidth !=0)
			{
				$tWidthStyle = " style=\"width:".$tabWidth."\"";
			}
			else
				$tWidthStyle = "";
			foreach ($elements as $e)
			{
				if ($cnt==$default)
					$class = "tabTabActive";
				else         
					$class = "tabTab";
				if (is_array($e))	
				{
					echo "<div $tWidthStyle onclick=\"tabActivate('".$name."',".$cnt.");".$e['js']."\" id=\"".$name."Nav".$cnt."\" class=\"".$class."\">".$e['name']."</div>";
				}
				else
					echo "<div $tWidthStyle onclick=\"tabActivate('".$name."',".$cnt.")\" id=\"".$name."Nav".$cnt."\" class=\"".$class."\">".$e."</div>";
				$cnt++;
			}
			echo "<br style=\"clear:both;\" />";
			
			$this->name = $name;
			$this->count = count($elements);
			$this->current = 0;
		}
		
		function open()
		{
			echo "<div id =\"".$this->name."Content".$this->current."\" class=\"tabContent\"";
			if ($this->current > 0)
				echo "style=\"display:none;\"";
			echo ">";
			$this->current++;
		}
		
		function close()
		{
			echo "</div>";
		}
		
		function end()
		{
			echo "</div>";			
		}
		
	}

?>