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
					$check=false;
				if (!file_exists($f2))
					$check=false;
      }
    }
    closedir($d);			
    return $check;
	}
	
	function pack()
	{
		$rdir = getcwd();
		
    chdir($this->src);
    $d = opendir(".");
    //exec('find -name ".svn" -type d -print | xargs rm -rf {}');
    ob_start();
    while ($f = readdir($d))
    {
      if (is_dir($f) && file_exists($f."/imagepack.xml"))
      {
      	$f1 = $rdir."/".$this->trg."/".$f.".tar.gz";
      	$f2 = $rdir."/".$this->trg."/".$f.".zip";
				passthru("tar czvf ".$f1." ".$f."/");
        passthru("zip -r ".$f2." ".$f."/");
      }
    }
    $out = ob_get_contents();
  	ob_end_clean();  
    closedir($d);		
    return $out;
	}
}

?>
