<?PHP

	class FleetActionCollectCrystal extends FleetAction
	{

		function FleetActionCollectCrystal()
		{
			$this->code = "collectcrystal";
			$this->name = "Sternennebel sammeln";
			$this->desc = RES_CRYSTAL." von Sternennebeln sammeln.";
			$this->longDesc = "Ebenso wie die Asteroiden und die Gasplaneten waren die Intergalaktischen Nebelfelder lange Zeit ein unerforschtes Mysterium. Doch heute bezieht man auch aus ihnen einen Nutzen. Man hat herausgefunden, dass diese Nebelfelder eine extrem siliziumreiche Atmosph&auml;re haben und so ist es nach ein paar Jahren intensiver Forschung gelungen, dieses Silizium zu bergen. Doch wie auch beim Asteroiden sammeln gibt es hier ein gewisses Gefahrenrisiko. Es kann vorkommen, dass die starken Magnetfelder, welche das Nebelfeld ausstrahlt, die Bordelektronik der Schiffe lahmlegt und die Schiffe dann im unendlichen Weltall verschollen bleiben. Genau wie beim Gassaugen k&ouml;nnen nur spezielle Schiffe das Silizium aus den Nebelfeldern zu bergen, deswegen k&ouml;nnen keine normalen Transporter f&uuml;r zus&auml;tzlichen Laderaum mitgeschickt werden.";
			$this->visible = false;
			$this->exclusive = false;					
			$this->attitude = 0;
			
			$this->allowPlayerEntities = false;
			$this->allowActivePlayerEntities = false;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = true;
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