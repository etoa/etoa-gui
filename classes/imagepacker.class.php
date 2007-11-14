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
      	$f1 = $rdir."/".$this->trg."/".$f.".tar.gz";
      	$f2 = $rdir."/".$this->trg."/".$f.".zip";

	passthru("tar czvf ".$f1." ".$f."/");
        passthru("zip -r ".$f2." ".$f."/");
	echo "Packed $f1 and $f2 <br>";
      }
    }
    closedir($d);		
}
}

?>
