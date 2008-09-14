<?PHP

	class FleetActionInvade extends FleetAction
	{

		function FleetActionInvade()
		{
			$this->code = "invade";
			$this->name = "Invasion";
			$this->desc = "Greift das Ziel an und versucht es zu übernehmen.";
			$this->longDesc = "Wenn die unbewohnten Planeten rar werden oder man keine Lust hat einen neuen Planeten mühsam aufzubauen, gibt es die Option sich einen Planeten eines anderen Spielers unter den Nagel zu reissen.
			Dies kann man aber nur mit speziellen Schiffen, die für Invasionen ausgerichtet sind, machen.
			Eine Invasion kann nur erfolgreich sein, wenn mindestens ein invasionsfähiges Schiff den Kampf überlebt und auch dann liegt die Chance nur bei einem gewissen Prozentsatz. Je grösser der Punkteunterschied zwischen den beiden Spielern ist, desto höher, beziehungsweise tiefer ist die Invasionschance. Die Grundchance beträgt 100% und sie kann 100% nicht übersteigen und 100% nicht unterschreiten. Mit mehreren Invasionsschiffen erhöht sich die Chance nicht.
			Ausserdem ist zu beachten, dass die Hauptplaneten (Die Planeten, die die Spieler bei ihrer Anmeldung als erste erhalten) nicht invasiert werden können.";
			$this->visible = true;
			$this->exclusive = false;						
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>