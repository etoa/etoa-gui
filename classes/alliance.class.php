<?php

class Alliance 
{
    function Alliance() 
    {
    }
    
    static function checkActionRights($action)
    {
			global $myRight,$isFounder,$page;
			if ($isFounder || $myRight[$action])
			{
				return true;
			}
			error_msg("Keine Berechtigung!");
			echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
			return false;    	
    }
}
?>