<?PHP

	class Note
	{
		private $subject,$text,$timestamp;
		
		function Note($subject,$text,$timestamp=0)
		{
			$this->subject = $subject;
			$this->text = $text;
			$this->timestamp = $timestamp==0 ? time() : $timestamp;
		}
		
		function subject() { return $this->subject; } 

		function text() { return $this->text; } 

		function timestamp() { return $this->timestamp; } 

		
	}


?>