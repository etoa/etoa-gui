<?PHP

	class BookmarkManager
	{
		private $items;
		private $loaded;
		
		function BookmarkManager($userid)
		{
			$this->items = array();
			$this->loaded = false;
			
			$res = dbquery("SELECT
				id,
				entity_id,
				comment
			FROM 
				bookmarks
			WHERE
				user_id=".$userid."");
			if (mysql_num_rows($res)>0)
			{
				$this->loaded = true;
				while ($arr = mysql_fetch_row($res))
				{
					$b = new Bookmark($arr[0],$userid,$arr[1],$arr[2]);				
					$b->loadTarget();
					$this->items[] = $b;
				}
			}
		}
		
		function drawSelector($id,$js="")
		{
			global $pm;
			ob_start();
			echo "<select id=\"".$id."\" onchange=\"".$js."\">";
			echo "<option value=\"\">W&auml;hlen...</option>";
			
			foreach ($pm->itemObjects() as $i)
			{
				echo "<option value=\"".$i->id()."\">".$i."</option>";
			}
			unset($i);			
			
			if ($this->loaded)
			{
				echo "<option value=\"\">-----------------------------</option>";						
				foreach ($this->items as &$i)
				{
					echo "<option value=\"".$i->entityId."\">".$i->target->entityCodeString()." ".$i->target." (".$i->comment.")</option>";
				}
				unset($i);			
			}
			echo "</select>";					
			$rtn = ob_get_contents();
			ob_end_clean();
			return $rtn;
		}
	
		function drawSelectorJavaScript($id,$js)
		{
			global $pm;			
			ob_start();		
			echo "<script type=\"text/javascript\">
			function ".$js."()
			{
				select_id=document.getElementById('".$id."').selectedIndex;
				select_val=document.getElementById('".$id."').options[select_id].value;
				a=1;
				if (select_val!='')
				{
					switch(select_val)
					{
						";
						foreach ($pm->itemObjects() as $i)
						{
							$c = $i->coordsArray();
							echo "case \"".$i->id()."\":\n";
							echo "document.getElementById('sx').value='".$c[0]."';\n";
							echo "document.getElementById('sy').value='".$c[1]."';\n";
							echo "document.getElementById('cx').value='".$c[2]."';\n";
							echo "document.getElementById('cy').value='".$c[3]."';\n";
							echo "document.getElementById('p').value='".$c[4]."';\n";
							echo "break;\n";
							
						}			
						unset($i);			
						foreach ($this->items as &$i)
						{
							$c = $i->target->coordsArray();
							echo "case \"".$i->entityId."\":\n";
							echo "document.getElementById('sx').value='".$c[0]."';\n";
							echo "document.getElementById('sy').value='".$c[1]."';\n";
							echo "document.getElementById('cx').value='".$c[2]."';\n";
							echo "document.getElementById('cy').value='".$c[3]."';\n";
							echo "document.getElementById('p').value='".$c[4]."';\n";
							echo "break;\n";
						}
						unset($i);			
						echo "
					}

				}
			}
			</script>";				
			$rtn = ob_get_contents();
			ob_end_clean();
			return $rtn;
		}	
		
	}
	

?>