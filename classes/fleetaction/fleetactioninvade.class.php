<?PHP

	class FleetActionInvade extends FleetAction
	{

		function FleetActionInvade()
		{
			$this->code = "invade";
			$this->name = "Invasion";
			$this->desc = "Greift das Ziel an und versucht den Planeten zu &uuml;bernehmen.";
			$this->longDesc = "Wenn die unbewohnten Planeten rar werden oder man keine Lust hat einen neuen Planeten m&uuml;hsam aufzubauen, gibt es die Option sich einen Planeten eines anderen Spielers unter den Nagel zu reissen.
			Dies kann man aber nur mit speziellen Schiffen, die für Invasionen ausgerichtet sind, machen.
			Eine Invasion kann nur erfolgreich sein, wenn mindestens ein invasionsf&auml;higes Schiff den Kampf überlebt und auch dann liegt die Chance nur bei einem gewissen Prozentsatz. Je gr&ouml;sser der Punkteunterschied zwischen den beiden Spielern ist, desto h&ouml;her, beziehungsweise tiefer ist die Invasionschance. Mit mehreren Invasionsschiffen erhöht sich die Chance nicht.
			Ausserdem ist zu beachten, dass die Hauptplaneten (Die Planeten, welche die Spieler bei ihrer Anmeldung als erste erhalten) nicht invadiert werden k&ouml;nnen.";
			$this->visible = true;
			$this->exclusive = false;						
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
			$this->allianceAction = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>