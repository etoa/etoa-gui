<?PHP
	class ImageUpload
	{
		private $field;
		private $targetDir;
		private $targetName;
		private $type;

		private $maxSize;
		private $maxDim;
		private $resize;

		private $resultName;

        public function __construct($field,$targetDir,$targetName,$type="")
		{
			$this->field = $field;
			$this->targetDir = $targetDir;
			$this->targetName = $targetName;
			if (in_array($type, ['ping', 'jpg', 'gif'], true))
				$this->type = $type;
			else
				$this->type = "png";

			$this->maxSize = 4194304;
		}

		function setMaxSize($s)
		{
			$this->maxSize = $s;
		}

		function setMaxDim($w,$h)
		{
			$this->maxDim = array($w,$h);
		}

		function enableResizing($w,$h)
		{
			$this->resize = array($w,$h);
		}

		function getResultName()
		{
			return $this->resultName;
		}

		function process()
		{
      if (isset ($_FILES[$this->field]['tmp_name']) && $_FILES[$this->field]['tmp_name']!="")
      {
      	$iarr = $_FILES[$this->field];

      	if ($iarr['size'] <= $this->maxSize)
      	{
          $source = $iarr['tmp_name'];
          if ($ims = getimagesize($source))
          {
	         	$ext = substr($ims['mime'],strrpos($ims['mime'],"/")+1);
	         	if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png")
	         	{
	            //überprüft Bildgrösse
	            if ($ims[0] <= $this->maxDim[0] && $ims[1] <= $this->maxDim[1])
	            {
                $fname = $this->targetName.".".$ext;
                $fpath = $this->targetDir."/".$fname;
				if (!is_dir($this->targetDir)) {
					mkdir($this->targetDir, 0755, true);
				}
                if (file_exists($fpath) && is_file($fpath)) {
                    @unlink($fpath);
				}
                move_uploaded_file($source,$fpath);
                if (UNIX)
                	chmod($fpath,FILE_UPLOAD_PERMS);

                if (is_array($this->resize) && ($ims[0] > $this->resize[0] || $ims[1] > $this->resize[1]))
								{
									if (!resizeImage($fpath,$fpath,$this->resize[0],$this->resize[1],$ext))
									{
										error_msg("Bildgrösse konnte nicht angepasst werden!");
                    @unlink($fpath);
                    return false;
									}
								}
								$this->resultName = $fname;
                return true;
	            }
	            else
	            {
                error_msg("Das Bild ist zu gross (".$ims[0]."*".$ims[1].", max ".$this->maxDim[0]."*".$this->maxDim[1]." erlaubt)!");
	            }
	         	}
	         	else
	         	{
	            error_msg("Das Bild muss vom Typ JPEG, PNG oder GIF sein!");
						}
					}
					else
					{
						error_msg("Ungültige Bilddatei!");
					}
				}
       	else
       	{
          error_msg("Das Bild ist zu gross (".byte_format($iarr['size']).", max ".byte_format($this->maxSize)." erlaubt)!");
				}
      }
      else
      {
      	error_msg("Keine Datei hochgeladen!");
      }
      return false;
		}
	}


?>
