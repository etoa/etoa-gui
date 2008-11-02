<?PHP
	class TabControl
	{
		private $name;
		private $count;
		private $current;
		
		function TabControl($name,$elements,$width="670px")
		{
			echo "<div class=\"tabOuter\" style=\"width:".$width."\">";
			$cnt=0;
			foreach ($elements as $e)
			{
				if ($cnt==0)
					echo "<div onclick=\"tabActivate('".$name."',".$cnt.")\" id=\"".$name."Nav".$cnt."\" class=\"tabTabActive\">".$e."</div>";
				else
					echo "<div onclick=\"tabActivate('".$name."',".$cnt.")\" id=\"".$name."Nav".$cnt."\" class=\"tabTab\">".$e."</div>";
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