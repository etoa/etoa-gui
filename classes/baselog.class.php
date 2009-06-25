<?PHP

	abstract class BaseLog
	{
		// Severities

		/**
		 * Debug message
		 */
		const DEBUG = 0;
		/**
		 * Information
		 */
		const INFO = 1;
		/**
		 * Warning
		 */
		const WARNING = 2;
		/**
		 * Error
		 */
		const ERROR = 3;
		/**
		 * Critical error
		 */
		const CRIT = 4;

		static public $severities = array("Debug","Information","Warnung","Fehler","Kritisch");

		
	}

?>