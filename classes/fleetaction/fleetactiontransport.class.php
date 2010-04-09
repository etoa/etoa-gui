<?PHP

	class FleetActionTransport extends FleetAction
	{

		function FleetActionTransport()
		{
			$this->code = "transport";
			$this->name = "Waren transportieren";
			$this->desc = "Bringt Waren zum Ziel und l&auml;dt sie dort ab.";
			$this->longDesc = "Die grundlegendste Aktion &uuml;berhaupt, bei der Ressourcen und Bewohner transportiert werden.
So gut wie jedes Schiff kann Waren transportieren, die Frage ist nur immer wie viel.
Baue spezielle Transporter, um kostengünstig zu transportieren.";
			$this->visible = true;
			$this->exclusive = false;					
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = true;
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