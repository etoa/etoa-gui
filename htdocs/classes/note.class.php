<?PHP

	class Note
	{
		private $subject,$text,$timestamp,$id;
		
		function Note($id,$subject,$text,$timestamp=0)
		{
			$this->subject = $subject;
			$this->text = $text;
			$this->id = $id;
			$this->timestamp = $timestamp==0 ? time() : $timestamp;
		}
		
		function subject() { return $this->subject; } 

		function text() { return $this->text; } 

		function timestamp() { return $this->timestamp; } 

		function id() { return $this->id; } 
		
	}


?>