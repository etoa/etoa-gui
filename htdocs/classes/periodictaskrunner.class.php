<?PHP
	/**
	* Runs periodic tasks
	*/
	class PeriodicTaskRunner 
	{
		private $totalDuration = 0;
	
		function runTask($taskIdentifier, $mutex=0) {
			$klass = $taskIdentifier;
			$reflect = new ReflectionClass($klass);
			if ($reflect->implementsInterface('IPeriodicTask')) {
				if ($mutex == 1) {
					$mtx = new Mutex();
					$mtx->acquire();
				}
				$tmr = timerStart();
				$task = new $klass();
				$output = $task->run();
				if ($mutex == 1) {
					$mtx->release();
				}
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
		
		/**
		* Tests if a given task schedule matches the supplied timestamp, 
		* meaning that the task can be run at this time
		*/
		static function shouldRun($schedule, $timestamp) {
			
			// Split date into components
			$splittedDate = array(
				'minute' => intval(date("i", $timestamp)),
				'hour' => intval(date("H", $timestamp)),
				'dayofmonth' => intval(date("j", $timestamp)),
				'month' => intval(date("n", $timestamp)),
				'dayofweek' => intval(date("w", $timestamp))
			);
			
			// Aliases
			$aliases = array(
				'@hourly' => '0 * * * *',
				'@daily' => '0 0 * * *',
				'@weekly' => '0 0 * * 0',
				'@monthly' => '0 0 1 * *',
				'@yearly' => '0 0 1 1 *',				
			);
			if (isset($aliases[$schedule])) {
				$schedule = $aliases[$schedule];
			}

			// Split schedule into components
			$elem = preg_split('/\s+/', $schedule);
			if (count($elem) < 5) {
				return false;
			}
			$sched = array(
					'minute' => $elem[0],
					'hour' => $elem[1],
					'dayofmonth' => $elem[2],
					'month' => $elem[3],
					'dayofweek' => $elem[4]
			);

			// Iterate over each element
			foreach ($sched as $k => $v) {
				
				// Match all
				if ($v == "*") {
					continue;
				}
				$dateVal = $splittedDate[$k];
				
				// Match single number
				if (preg_match('/^[0-9]+$/', $v)) {
					if ($k == 'dayofweek' && $v ==7) {
						$v = 0;
					}
					if ($v == $dateVal) {
						continue;
					}
					return false;
				}
				
				// Match multiple numbers
				if (preg_match('/^[0-9][0-9,]+[0-9]$/', $v)) {
					$hasMatch = false;
					foreach(preg_split('/,/', $v) as $mv) {
						if ($k == 'dayofweek' && $mv ==7) {
							$mv = 0;
						}
						if ($mv == $dateVal) {
							$hasMatch = true;
						}
					}
					if ($hasMatch) {
						continue;
					}
					return false;
				}
				
				// Match period
				$m = array();
				if (preg_match('/^\*\/([0-9]+)$/', $v, $m)) {
					$rv = $m[1];
					if ($dateVal % $rv == 0) {
						continue;
					}
					return false;
				}
				
				// Match interval
				$m = array();
				if (preg_match('/^([0-9]+)-([0-9]+)$/', $v, $m)) {
					$min = $m[1];
					$max = $m[2];
					if ($dateVal >= $min && $dateVal <= $max) {
						continue;
					}
					return false;
				}
				
				// No method matches
				return false;
			}
			return true;
		}
	}
?>