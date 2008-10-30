<?php
    echo '<h2>Spezialpunkte und Titel</h2>';
		Help::navi(array("Spezialpunkte","specialpoints"));
    iBoxStart("Kampfpukte");
    echo '<div align="justify">';
    echo 'Jeder Kampf ergibt bei einem Sieg oder Unentschieden eine gewisse Anzahl Kampfpunkte. Ein Angreifer erhält bei
    einem Sieg mehr Punkte als ein Verteidiger, jedoch verliert er bei einer Niederlage auch einen Punkt; der Verteidiger verliert jedoch bei
    einer Niederlage nichts.<br/>
    <a href="?page=stats&amp;mode=battle">Rangliste</a>';
    echo '</div>';
    iBoxEnd();
    
    iBoxStart("Handelspunkte");
    echo '<div align="justify">';
    echo 'Für jeden abgeschlossenen Handel kriegt man als Verkäufer oder Käufer Punkte. Wenn man als Verkäufer seine Angebote in der
    Beschreibung anpreist, ergibt das zusätzliche Punkte.<br/>
    <a href="?page=stats&amp;mode=trade">Rangliste</a>';
    echo '</div>';
    iBoxEnd();    
    
    iBoxStart("Diplomatiepunkte");
    echo '<div align="justify">';
    echo 'Für jeden im Rathaus verfassten Text gibt es Punkte. Beachte jedoch dass dort nicht gespamt werden darf. Wird ein
    Text von einem Spieler beanstandet und von einem Admin gelöscht, werden dafür keine Punkte vergeben resp. die Punkte wieder abgezogen.
    Ebenfalls gibt es für Bündnisse und Kriegserklärungen Punkte. Werden bei Bündnissen öffentliche Texte hinzugefügt, ergibt das Zusatzpunkte.<br/>
    <a href="?page=stats&amp;mode=diplomacy">Rangliste</a>';
    echo '</div>';
    iBoxEnd();     
    
    iBoxStart("Titel");
    echo '<div align="justify">';
    echo 'Alle Spieler, die eine der Ranglisten auf der Statistikseite anführen, erhalten einen speziellen Titel und eine Medaille. Ebenfalls erhalten
    alle besten Spieler einer jeweiligen Rassen einen besonderen Titel.<br/>
    <a href="?page=stats&amp;mode=titles">Titel und Medaillen</a>';
    echo '</div>';
    iBoxEnd();       
?>