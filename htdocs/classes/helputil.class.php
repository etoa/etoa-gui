<?php

/**
* This class defines static ulitilty
* functions for the help system
*/
class HelpUtil 
{
  /**
  * This function sucks ;) A reimplementation is needed
  */
  static function breadCrumbs($item1=null,$item2=null,$disable2=0)
	{
		global $_GET;
		echo "Du befindest dich hier: ";
		if ($item1!=null)
		{
			if (isset($_GET['page']) && ctype_alnum(str_replace(array('-','_'), '', $_GET['page'])))
				$page = "page";
			else
				$page = "index";
			
			echo "<a href=\"?$page=help\">Hilfe</a> &raquo; ";
			
			if ($item2!=null)
			{
				echo "<a href=\"?$page=help&amp;site=".$item1[1]."\">".$item1[0]."</a> &raquo; ";		
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
	
	
	static function colorizeMarketRate($r)
	{
		$b = " style=\"color:#000;background:";
		
		$e = "\"";
		if ($r<0.5)
			return $b."#0f0".$e;		           
	  if ($r<1)
			return $b."#ff0".$e;		           
	  if ($r>5)
			return $b."#f40".$e;		           
	  if ($r>2.5)
			return $b."#f70".$e;		           
	  if ($r>1)
			return $b."#fa0".$e;		           
	}
		
		
}
?>