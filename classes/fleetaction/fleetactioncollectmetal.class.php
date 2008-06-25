<?PHP

	class FleetActionCollectMetal extends FleetAction
	{

		function FleetActionCollectMetal()
		{
			$this->code = "collectmetal";
			$this->name = RES_METAL."erz sammeln";
			$this->desc = RES_METAL." von Asteroiden sammeln";
			$this->longDesc = "Im Weltraum tummeln sich viele kleinere Asteroidenfelder. Viele Jahre lang waren sie nur eine Bedrohung für die Zivilisation doch heute hat man gelernt einen Nutzen daraus zu ziehen. Mit speziell gebauten Schiffen ist es möglich Ressourcen aus den Asteroidenfelder zu schöpfen und zu verwerten!
Diese Moderne Form von Ressourcengewinnung birgt aber noch ein grosses Risiko. In den Asteroidenfelder kann es vorkommen, dass die Schiffe von den Gesteinsbrocken getroffen und zerstört werden. In diesem Fall sind die Schiffe kaputt und werden nie mehr wieder gesehen!
Asteroidenfelder sind aber nicht unbegrenzt verfügbar. Wenn man sie aufgebraucht hat verschwinden sie, aber keine Angst, es werden immer wieder neue erscheinen.";
			$this->visible = false;
			$this->exclusive = false;					
			$this->attitude = 0;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>