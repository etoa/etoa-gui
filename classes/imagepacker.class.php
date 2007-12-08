<?PHP

class ImagePacker	
{
	function ImagePacker($src,$trg)
	{
		$this->src = $src;
		$this->trg = $trg;
	}
	
	function check()
	{
		$check=true;
		$rdir = getcwd();
    chdir($this->src);
    $d = opendir(".");
    while ($f = readdir($d))
    {
      if (is_dir($f) && file_exists($f."/imagepack.xml"))
      {
      	$f1 = $rdir."/".$this->trg."/".$f.".tar.gz";
      	$f2 = $rdir."/".$this->trg."/".$f.".zip";
				if (!file_exists($f1))
				{
					echo "$f1 fehlt!<br/>";
					$check=false;
				}
				if (!file_exists($f2))
				{
					echo "$f2 fehlt!<br/>";
					$check=false;
				}
      }
    }
    closedir($d);
    chdir($rdir);
    return $check;
	}
	
	function pack()
	{
		if (!UNIX)
		{
			error_msg("Bildpakete können nur auf einem Unix System erstellt werden!");
			return false;
		}
				
		$rdir = getcwd();
		$src = $rdir."/".$this->src;
		$trg = $rdir."/".$this->trg;
		
    chdir($trg);
    $d = opendir($src);
    while ($f = readdir($d))
    {
      if (is_dir($src."/".$f) && file_exists($src."/".$f."/imagepack.xml"))
      {
				passthru("cp -r ".$src."/".$f." .");
    		passthru('find '.$f.' -name ".svn" -type d -print | xargs rm -rf {}');

      	$f1 = $f.".tar.gz";
      	echo "Creating <b>$f1</b>...<br/>";
      	$c1 = "tar czvf ".$f1." ".$f."/";
				passthru($c1);

      	$f2 = $f.".zip";
      	echo "Creating <b>$f2</b>...<br/>";
      	$c2 = "zip -r ".$f2." ".$f."/";
        passthru($c2);
        
        passthru("rm -rf $f");
      }
    }
    closedir($d);		
    chdir($rdir);
	}
}

?>
