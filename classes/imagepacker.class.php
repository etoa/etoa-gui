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
		
	}
	
	function pack()
	{
		$rdir = getcwd();
		
    chdir($this->src);
    $d = opendir(".");
    // Remove svn data
    //exec('find -name ".svn" -type d -print | xargs rm -rf {}');
    // Go through every directory and zip the data
    $files = array();
    $count = 0;
    while ($f = readdir($d))
    {
      if (is_dir($f) && file_exists($f."/imagepack.xml"))
      {
      	$f1 = $f.".tar.gz";
      	$f2 = $f.".zip";
        exec("tar czvf ".$f1" ".$f."/");
        exec("zip -r ".$f2." ".$f."/");
      	array_push($files,$f1);
      	array_push($files,$f2);
      }
    }
    closedir($d);		
    chdir($rdir);
    foreach ($files as $f)
    {
    	$count++;
    	rename($this->src."/".$f, $this->trg."/".$f);
    }
		return $count;
	}
}

?>