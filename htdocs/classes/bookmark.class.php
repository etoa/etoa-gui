<?PHP
	class Bookmark
	{
		public $target;
		
		function Bookmark($id,$userId,$entityId,$comment)
		{
			$this->id = $id;
			$this->userId = $userId;
			$this->entityId = $entityId;
			$this->comment = $comment;
		}
		
		/**
		* 
		*/
		function loadTarget()
		{
			$this->target = Entity::createFactoryById($this->entityId);
		}
		
	}


?>