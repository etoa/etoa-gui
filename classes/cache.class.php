<?php

class cache 
{
    function cache($path=".") 
    {
    	if (file_exists( $path."/".$this->cacheDir))
    	{
    		$this->cacheDir = $path."/".$this->cacheDir;
    	}
    	else
    	{
    		error_message("Fehlerhafter Pfad $path",1);
    	}
    }
    
  
    static function checkPerm($type="",$prePath=".")
    {
    	if (UNIX)
    	{    	
	    	if ($type!="")
	    	{	
		    	$path = $prePath."/".CACHE_ROOT."/".$type;
		    	if (file_exists($path))
		    	{
		    		$userarr = posix_getpwuid(fileowner($path));
		    		$user = $userarr['name'];
		    		if ($user==UNIX_USER)
		    		{
			    		$userarr = posix_getpwuid(filegroup($path));
			    		$user = $userarr['name'];
			    		if ($user==UNIX_GROUP)
			    		{		    			
			    			$perms = substr(sprintf("%o",fileperms($path)),2,3);
							if (substr($perms,1,1)>=6)
							{
								return true;
							}
							error_msg("Das Cache-Unterverzeichnis [b]".$type."[/b] hat falsche Gruppenrechte ($perms)!\nDies kann mit [i]chmod g+w ".CACHE_ROOT."/".$type." -R[/i] geändert werden!");
				    		return false;    
			    		}
						error_msg("Das Cache-Unterverzeichnis [b]".$type."[/b] hat eine falsche Gruppe! Eingestellt ist [b]".$user."[/b], erwartet wurde [b]".UNIX_GROUP."[/b]!\nDies kann mit [i]chgrp ".UNIX_GROUP." ".CACHE_ROOT."/".$type." -R[/i] geändert werden!");
			    		return false;    			
		    		}
					error_msg("Das Cache-Unterverzeichnis [b]".$type."[/b] hat einen falschen Besitzer! Eingestellt ist [b]".$user."[/b], erwartet wurde [b]".UNIX_USER."[/b]!\nDies kann mit [i]chown ".UNIX_USER." ".CACHE_ROOT."/".$type." -R[/i] geändert werden!");
		    		return false;	
		    	}
		    	error_msg("Das Cache-Unterverzeichnis [b]".$type."[/b] $path wurde nicht gefunden!");
		    	return false;
	    	}
	    	else
	    	{	
		    	$path = $prePath."/".CACHE_ROOT;
		    	if (file_exists($path))
		    	{
		    		$userarr = posix_getpwuid(fileowner($path));
		    		$user = $userarr['name'];
		    		if ($user==UNIX_USER)
		    		{
			    		$userarr = posix_getpwuid(filegroup($path));
			    		$user = $userarr['name'];
			    		if ($user==UNIX_GROUP)
			    		{
			    			$perms = substr(sprintf("%o",fileperms($path)),2,3);
							if (substr($perms,1,1)>=6)
							{
								return true;
							}
							error_msg("Das Cache-Verzeichnis [b]".$type."[/b] hat falsche Gruppenrechte ($perms)!\nDies kann mit [i]chmod g+w ".CACHE_ROOT." -R[/i] geändert werden!");
				    		return false; 
			    		}
						error_msg("Das Cache-Verzeichnis hat eine falsche Gruppe! Eingestellt ist [b]".$user."[/b], erwartet wurde [b]".UNIX_GROUP."[/b]!\nDies kann mit [i]chgrp ".UNIX_GROUP." ".CACHE_ROOT." -R[/i] geändert werden!");
			    		return false;    			
		    		}
					error_msg("Das Cache-Verzeichnis hat einen falschen Besitzer! Eingestellt ist [b]".$user."[/b], erwartet wurde [b]".UNIX_USER."[/b]!\nDies kann mit [i]chown ".UNIX_USER." ".CACHE_ROOT." -R[/i] geändert werden!");
		    		return false;	
		    	}
		    	error_msg("Das Cache-Verzeichnis $path wurde nicht gefunden!");
		    	return false;
	    	}
    	}
    	elseif(WINDOWS)
    	{
    		return true;
    	}
    	else
    	{
    		return false;    		
    	}    	
    }
}
?>