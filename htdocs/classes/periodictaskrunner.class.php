<?PHP
	/**
	* Runs periodic tasks
	*/
	class PeriodicTaskRunner 
	{
		private $totalDuration = 0;
	
		function runTask($taskIdentifier) {
			$klass = $taskIdentifier;
			$reflect = new ReflectionClass($klass);
			if ($reflect->implementsInterface('IPeriodicTask')) {
				$mtx = new Mutex();
				$mtx->acquire();
				$tmr = timerStart();
				$task = new $klass();
				$output = $task->run();
				$mtx->release();
				$duration = timerStop($tmr);
				$this->totalDuration += $duration;
				if (!empty($output)) {
					return $output." (".$duration." sec)\n";
				}
				return '';
			} else {
				throw new Exception("Invalid periodic task identifier");
			}
		}
		
		function getTotalDuration() {
			return $this->totalDuration;
		}
	}
?>