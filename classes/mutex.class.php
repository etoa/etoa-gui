<?PHP

	class Mutex
	{
		private $key;
		private $sem;
		
		function Mutex()
		{
			$this->key = ftok(dirname(__FILE__),"e");
			$this->sem =  sem_get($this->key,1);
		}

		function __destruct()
		{

		}

		function acquire()
		{
			sem_acquire($this->sem);
		}
		
		function release()
		{
			sem_release($this->sem);		
		}
	}



?>