<?php

class Help 
{
    function Help() 
    {
    }
    
    static function navi($item1=null,$item2=null,$disable2=0)
		{
			echo "Du befindest dich hier: ";
			if ($item1!=null)
			{
				echo "<a href=\"?page=help\">Hilfe</a> &gt; ";
				if ($item2!=null)
				{
					echo "<a href=\"?page=help&amp;site=".$item1[1]."\">".$item1[0]."</a> &gt; ";		
					if ($disable2==0)
						echo $item2[0]."<br/><br/>";		
				}
				else
				{
					echo $item1[0]."<br/><br/>";		
				}
			}
			else
			{
				echo "Hilfe<br/><br/>";
			}
		}
}
?>