<?PHP
	/**
	* Base class for random events
	*/
	abstract class RandomEvent
	{
		protected $xml;
		protected $title;
		protected $message;
		protected $probability;
		protected $messageParsed;
		
		function RandomEvent($id,$dir)
		{
			$file = DATA_DIR."/events/".$dir."/".$id.".xml";
			if(file_exists($file))
			{
				$xml = simplexml_load_file($file);
				
				$this->title = $xml->title;
				$this->message = $xml->message;
				$this->probability = $xml->probability;
			
				$this->xml = $xml;

				
			}
			else
			{
				echo "Event $id does not exist in $dir!\n";
			}
		}
		
		public function getTitle()
		{
			return $this->title;
		}
		
		
		public function getProbability()
		{
			return $this->probability;
		}		
		
		static function chooseFromDir($dir)
		{
			$path = DATA_DIR."/events/".$dir;
			$d = opendir($path);			
			$evts = array();
			$cnt=0;
			while($f = readdir($d))
			{
				$file = $path."/".$f;
				if (is_file($file) && substr($f,strrpos($f,".xml"))==".xml")
				{
					$id = substr($f,0,strrpos($f,".xml"));
					$evt = new DefaultRandomEvent($id,$dir);
					for($x=0; $x < $evt->getProbability(); $x++)
					{
						$evts[$cnt] = $id;
						$cnt++;
					}
				}
			}
			return $evts[mt_rand(0,$cnt-1)];
		}
		
		static function getList($dir)
		{
			$path = DATA_DIR."/events/".$dir;
			$d = opendir($path);			
			$evts = array();
			while($f = readdir($d))
			{
				$file = $path."/".$f;
				if (is_file($file) && substr($f,strrpos($f,".xml"))==".xml")
				{
					$id = substr($f,0,strrpos($f,".xml"));
					array_push($evts,$id);
				}
			}
			return $evts;
		}
		
		abstract function run();

		
	}	





?>