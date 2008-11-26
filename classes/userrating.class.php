<?PHP
	class UserRating
	{
		private $id;

		private $battle = 0;
		private $trade = 0;
		private $diplomacy = 0;
		private $battlesFought = 0;
		private $battlesWon = 0;
		private $battlesLost = 0;
		private $tradesSell = 0;
		private $tradesBuy = 0;
		
		public function UserRating($id)
		{
			$this->id = $id;
			$res = dbquery("
			SELECT
				*
			FROM 
				user_ratings 
			WHERE 
				id=".$this->id.";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->battle = $arr['battle_rating'];
				$this->trade = $arr['trade_rating'];
				$this->diplomacy = $arr['diplomacy_rating'];
				$this->battlesFought = $arr['battles_fought'];
				$this->battlesWon = $arr['battles_won'];
				$this->battlesLost = $arr['battles_lost'];
				$this->tradesSell = $arr['trades_sell'];
				$this->tradesBuy = $arr['trades_buy'];
				echo "Yuhu!!";
			}
			else
			{
				dbquery("
				INSERT INTO 
					user_ratings
				(id)
				VALUES
				(".$this->id.")
				");
			}	    
		}
		
		public function __set($key, $val)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				
				
				
				throw new EException("Property $key der Klasse ".__CLASS__." ist nicht änderbar!");
				
				//$this->$key = $val;
				return false;
			}
			catch (EException $e)
			{
				echo $e;
			}
		}
		
		public function __get($key)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in ".__CLASS__);
					
				return $this->$key;
			}
			catch (EException $e)
			{
				echo $e;
				return null;
			}
		}		
		
		/**
		* Add battle rating
		*/
		function addBattleRating($rating,$reason="")
		{
			if ($points!=0)
			{
				dbquery("
				UPDATE
					user_ratings
				SET
					battle_rating=battle_rating+".$rating."
				WHERE
					id=".$this->id.";");
				if ($reason!="")
					add_log(17,"Der Spieler ".$this->id." erhält ".$rating." Kampfpunkte. Grund: ".$reason);
			}
		}
		
		/**
		* Add trade rating
		*/
		function addTradeRating($rating,$reason="")
		{
			dbquery("
			UPDATE
				user_ratings
			SET
				trade_rating=trade_rating+".$rating."
			WHERE
				id=".$this->id.";");			
			if ($reason!="")
				add_log(17,"Der Spieler ".$this->id." erhält ".$rating." Handelspunkte. Grund: ".$reason);
		}
		
		/**
		* Add diplomacy rating
		*/
		function addDiplomacyRating($rating,$reason="")
		{
			dbquery("
			UPDATE
				user_ratings
			SET
				  	diplomacy_rating=diplomacy_rating+".$rating."
			WHERE
				id=".$this->id.";");			
			if ($reason!="")
				add_log(17,"Der Spieler ".$this->id." erhält ".$rating." Diplomatiepunke. Grund: ".$reason);
		}	

		
		
	}

?>