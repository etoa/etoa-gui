<?PHP
	
	class Ship
	{
		static function xpByLevel($base_xp,$factor,$level)
		{
			return $base_xp * intpow($factor,$level-1);
		}
		
		static function levelByXp($base_xp,$factor,$xp)
		{
			return max(0,floor(1 + ((log($xp)-log($base_xp))/log($factor))));
		}
	
	}

?>