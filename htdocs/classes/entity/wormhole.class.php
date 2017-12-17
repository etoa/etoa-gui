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
		private $persistent;
		private $changed;
		private $dataLoaded;

		/**
		* The constructor
		*/
        public function __construct($id=0)
		{
			$this->isValid = true;
			$this->id = intval($id);
			$this->pos = 0;
			$this->name = "";
			$this->coordsLoaded=false;
			$this->dataLoaded=false;
			$this->targetId=-1;
			$this->changed=-1;
			$this->isVisible = true;
		}

    public function allowedFleetActions()
    {
    	return array("flight","explore");
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

		function ownerMain() { return false; }


		/**
		* Returns type string
		*/
		function entityCodeString() { return "Wurmloch"; }

		/**
		* Returns type
		*/
		function type()
		{
			if (!$this->dataLoaded)
			{
				$this->loadData();
			}
			return $this->persistent ? "stabil" : "veränderlich";
		}

		function imagePath($opt="")
		{
			defineImagePaths();
			if (!$this->dataLoaded)
			{
				$this->loadData();
			}
			$prefix = $this->persistent ? 'wormhole_persistent' : 'wormhole';
			return IMAGE_PATH."/wormholes/".$prefix."1_small.".IMAGE_EXT;
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
					persistent,
					changed
				FROM
					wormholes
				WHERE
					id=".$this->id.";
				");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_assoc($res);
					$this->targetId=$arr['target_id'];
					$this->persistent=($arr['persistent']==1);
					$this->changed=$arr['changed'];
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

		function isPersistent()
		{
			if (!$this->dataLoaded)
			{
				$this->loadData();
			}
			return $this->persistent;
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
		* Vertauscht zuf?llig mehrere Wurml?cher miteinander
		*
		* @author MrCage
		*/
		static function randomize()
		{
			$time = time();
			$del = array();
			// L?schen
			$res=dbquery("
				SELECT
					id,
					target_id
				FROM
					wormholes
				WHERE
					persistent=0
					AND target_id>'0'
					AND changed<".($time-WH_UPDATE_AFFECT_TIME)."
				ORDER BY
					RAND()
				LIMIT ".WH_UPDATE_AFFECT_CNT.";
			");

			while ($arr=mysql_fetch_row($res))
			{
				if (!in_array($arr[0], $del))
				{
					array_push($del, $arr[0], $arr[1]);
				}
			}

			$delcnt = count($del);

			if ($delcnt > 0)
			{
				foreach($del AS $id)
				{
					dbquery("
						UPDATE
							entities
						SET
							code='e'
						WHERE
							id='".$id."';
					");
					dbquery("
						DELETE FROM	
							wormholes
						WHERE
							id='".$id."'
					");
					dbquery("
						INSERT INTO
							space
						(
							id
						)
						VALUES
						('".$id."')
					;");
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
					AND pos='0'
				ORDER BY
					RAND()
				LIMIT ".($delcnt).";
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
				UPDATE
					entities
				SET
					code='w'
				WHERE
					id='".$arr2[0]."';");

				dbquery("
				DELETE FROM
					space
				WHERE
					id='".$arr1[0]."';");

				dbquery("
				DELETE FROM
					space
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
				VALUES 
				(
					'".$arr1[0]."',
					'".$arr2[0]."',
					'".$time."'
				),
				(
					'".$arr2[0]."',
					'".$arr1[0]."',
					'".$time."'
				);");
			}
		}

		public function getFleetTargetForwarder()
		{
			// Forward in 0 secs to the other end of the wormhole and allow selection of new target
			return array($this->targetId,0,true);
		}

	}
?>
