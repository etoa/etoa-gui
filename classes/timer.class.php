<?PHP

  /**
  * Timer class
  *
  * Class for measuring time
  *
  * @author Nicolas Perrenoud, mail@dysign.ch
  */  
	class Timer
	{
		private $time;
		
		/**
		* Constructor
		*
		* Initializes and starts time measurement
		*/
		function Timer()
		{
			$render_time = explode(" ",microtime());
			$this->time=$render_time[1]+$render_time[0];
		}
		
		/**
		* Get time since class initialization
		*
		* @return float Time in seconds
		*/
		function getTime()
		{
			$render_time = explode(' ',microtime());
			return $render_time[1]+$render_time[0]-$this->time;			
		}		
		
		/**
		* Get time since class initialization
		*
		* @return float Time in seconds
		*/
		function getRoundedTime($round=3)
		{
			$render_time = explode(' ',microtime());
			return round($render_time[1]+$render_time[0]-$this->time,$round);			
		}		
		
		/**
		* Returns time as string
		*
		* @return string Timer string
		*/
		function __toString()
		{
			return "Timer: ".$this->getRoundedTime(5)." sec";
		}
	}

?>