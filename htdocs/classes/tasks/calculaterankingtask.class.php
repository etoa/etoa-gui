<?PHP
	/**
	* Calculate points and update ranking
	*/
	class CalculateRankingTask implements IPeriodicTask 
	{		
		function run()
		{
			$num = Ranking::calc();
			$d = $num[1]/$num[0];
			return "Die Punkte von ".$num[0]." Spielern wurden aktualisiert; ein Spieler hat durchschnittlich ".nf($d)." Punkte";
		}
		
		function getDescription() {
			return "Punkte berechnen und Rangliste aktualisieren";
		}
	}
?>