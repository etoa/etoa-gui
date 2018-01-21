<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////

	/**
	* Handles mutual exclusions using UNIX IPC-Semaphores. Thus
	* race conditions in critical sections can be avoided.
	* It uses the semaphore php extension.
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class Mutex
	{
		private $key;
		private $sem;

		private static function isSupported() {
			$os = isset($_SERVER['OS']) ? $_SERVER['OS'] : '';
			return UNIX && !preg_match('/^win/i', $os);
		}

		/**
		* Class constructor. Initializes the semaphore key
		* for this project based on this file's directory name
		* and registers the semaphore with a value of 1
		*/
        public function __construct()
		{
			if (self::isSupported())
			{
				$this->key = ftok(dirname(__FILE__),"e");
				$this->sem =  sem_get($this->key,1);
			}
		}

		/**
		* Aquires the registered semaphores. This method must be called a critical section.
		*/
		function acquire()
		{
			if (self::isSupported())
			{
				sem_acquire($this->sem);
			}
		}

		/**
		* Releases the semaphore. This method must be called after a criticcal section.
		*/
		function release()
		{
			if (self::isSupported())
			{
				sem_release($this->sem);
			}
		}
	}



?>
