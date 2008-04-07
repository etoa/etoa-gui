<?PHP
	
	/**
	* Class for nebula entity
	*/
	class Wormhole extends Entity
	{
		protected $id;
		protected $coordsLoaded;
		public $pos;
		protected $isValid;		
		public $sx;
		public $sy;
		public $cx;
		public $cy;
		protected $cellId;
		private $name;		
		private $targetId;
		private $changed;
		private $dataLoaded;
		
		/**
		* The constructor
		*/
		function Wormhole($id=0)
		{
			$this->isValid = false;
			$this->id = $id;
			$this->pos = 0;
			$this->name = "Unbenannt";
			$this->coordsLoaded=false;
			$this->dataLoaded=false;
			$this->targetId=-1;
			$this->changed=-1;
		}

		/**
		* Returns id
		*/                        
		function id() { return $this->id; }      

		/**
		* Returns id
		*/                        
		function name() { return $this->name; }      


		/**
		* Returns owner
		*/                        
		function owner() { return "Niemand"; }      

		/**
		* Returns owner
		*/                        
		function ownerId() { return 0; }      
	
		/**
		* Returns type string
		*/                        
		function entityCodeString() { return "Wurmloch"; }      
	
		/**
		* Returns owner
		*/                        
		function pos() { return $this->pos; }      
		
		/**
		* Returns type
		*/
		function type()
		{
			return "Wurmloch";
		}							

		function imagePath($opt="")
		{
			$r = mt_rand(1,9);
			return IMAGE_PATH."/wormholes/wormhole1_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() { return "w"; }	      
		
		/**
		* To-String function
		*/
		function __toString() 
		{
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->formatedCoords();
		}
		
		/**
		* Returns the cell id
		*/
		function cellId()
		{
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->cellId;
		}
		
		function loadData()
		{
			if ($this->dataLoaded==false)
			{
				$res=dbquery("
				SELECT
					target_id,
					changed
				FROM
					wormholes
				WHERE
					id=".$this->id.";
				");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_Fetch_array($res);
					$this->targetId=$arr[0];
					$this->changed=$arr[1];
					$this->dataLoaded=true;
				}
			}
		}
		
		function targetId()
		{
			if (!$this->dataLoaded)
			{
				$this->loadData();
			}
			return $this->targetId;
		}

		function changed()
		{
			if (!$this->dataLoaded)
			{
				$this->loadData();
			}
			return $this->changed;
		}		
		
		/**
		* Vertauscht zufällig mehrere Wurmlöcher miteinander
		*
		* @athor MrCage
		*/
		static function randomize()
		{
			$time = time();
			
			// Löschen
			$deleted = array();
			$delcnt=0;
			$res=dbquery("
				SELECT
					id,
					target_id
				FROM
					wormholes
				WHERE
					target_id>'0'
					AND changed<".($time-WH_UPDATE_AFFECT_TIME)."
				ORDER BY
					RAND()
				LIMIT ".WH_UPDATE_AFFECT_CNT.";
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_assoc($res))
				{
					if (!in_array($arr['id'],$deleted) && !in_array($arr['target_id'],$deleted))
					{
						dbquery("
							UPDATE
								entities
							SET
								code='e',
							WHERE
								id='".$arr['id']."'
							OR
								id='".$arr['target_id']."';
						");
						dbquery("
							DELETE FROM	
								wormholes
							WHERE
								id='".$arr['id']."'
							OR
								id='".$arr['target_id']."';
						");
						dbquery("
							INSERT INTO
								space
							(
								id
							)
							VALUES
							('".$arr['id']."'),
							('".$arr['target_id']."');
						");					
						array_push($deleted,$arr['id']);
						array_push($deleted,$arr['target_id']);
						$delcnt++;
					}
				}
			}
	
			// Neue erstellen
			$res=dbquery("
				SELECT
					id
				FROM
					entities
				WHERE
					code='e'
				ORDER BY
					RAND()
				LIMIT ".($delcnt*2).";
			");
			while ($arr1=mysql_fetch_row($res))
			{
				$arr2=mysql_fetch_row($res);
				dbquery("
				UPDATE
					entities
				SET
					code='w'
				WHERE
					id='".$arr1[0]."';");
				dbquery("
				INSERT INTO
					wormholes
				(
					id,
					target_id,
					changed
				)
				VALUES (
					'".$arr1[0]."',
					'".$arr2[0]."',
					'".$time."'
				);");
	
				dbquery("
				UPDATE
					entities
				SET
					code='w'
				WHERE
					id='".$arr2[0]."';");
				dbquery("
				INSERT INTO
					wormholes
				(
					id,
					target_id,
					changed
				)
				VALUES (
					'".$arr2[0]."',
					'".$arr1[0]."',
					'".$time."'
				);");
			}
		}		
			
	}
?>