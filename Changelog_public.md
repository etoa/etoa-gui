Escape to Andromeda Changelog
=============================

Version 3.5.0
-------------

### Features ###

 * Der Code zum Banner ist nun in einem eigenständigen Tab in den Einstellungen untergebracht
 * Details zur aktuellen Session können nun in den Einstellungen im Tab "Login" angezeigt werden
 * Neuer Spezialist: Der Architekt reduziert die Bauzeit um 10%
 * Beim Erstellen eines Accounts kann nun direkt ein Passwort gewählt werden

### Bugfixes ###

 * Fehler beim Aufrufen der Detailseite eines Gebäudes behoben
 * Session/Logout-Probleme bei Benutzern mit Dual-Stack (IPv6) Verbindungen behoben
 * Es wurde ein Problem behoben, bei dem Arbeiter freigestellt werden konnten obwohl das Gebäude noch im Bau war
 * Mehrere Probleme mit der Sortierung von Tabellen behoben
 * Validierung von Eingaben verbessert
 * Diverse Rechtschreibefehler behoben
 * Probleme mit Encodings behoben
 * Mehrere Sicherheitslücken behoben
 * Mehrere Probleme im Graphite design behoben

### Änderungen ###

 * Der Button zum Abreissen eines Gebäudes wird nun nicht mehr so prominent angezeigt
 * Die extern zugänglichen Seiten Rangliste, Hilfe, Spielstatistiken und Pranger wurden entfernt um die Code-Wartung zu vereinfachen. All diese Daten sind auch ingame verfügbar.

Version 3.4.0
-------------

### Features ###

 * Boost-System für Gebäudebau und Ressourcenproduktion hinzugefügt
 * Der Kryptocenter cooldown (Allianzgebäude) gilt nun für jeden User individuell
 * Die Anzahl der Allianz-Mitglieder kann nun begrenzt werden(defaults to 7)
 * Neue Mysticum-Eigenschaft: Reduzierung der Start- und Landezeit (Bereitschaft)
 * Tutorial-System hinzugefügt
 * BBcode-[url]-Links öffnen nun einem neuen Tab
 * Minimale Anzahl Punkte für Angriff hinzugefügt (Noob-Schutz)
 * Link zur Formatierungshilfe wurde zu verschiedenen Textfeldern hinzugefügt

### Bugfixes ###

 * "Query error" Fehler beim Aktivieren des Urlaubsmodus behoben
 * Das Minbari Mysticum versteckt nicht mehr länger die gesamte Flotte
 * Den Fehler "msg_send fail" beim Aktivieren eines Spezialisten behoben
 * Defekten Link im Admin Ticket System behoben
 * Beim Abbrechen eines Support-Angriffes werden Treibstoff und Nahrung nicht mehr verdoppelt
 * Benachrichtigung beim Verbannen eines Spielers im Chat korrigiert
 * Datenbankfehler beim Gründen einer Allianz mit ' im Namen behoben
 * Die Seite zu den Rassen-Details in der Hilfe funktioniert nun korrekt


Version 3.3.0
-------------

### Features ###

 * Keybinds hinzugefügt
 * Cleanup der Konfiguration (defaults.xml)
 * PNs lesen und schreiben ist nun auch während dem Urlaubsmodus möglich
 * Spezialist wird nun während den Urlaubsmodus pausiert
 * User werden nun benachrichtigt bei Nachrichten von Admins im Ticketsystem
 * Kryptobericht wird gespeichert
 * Accounts im Urlaubsmodus werden nach einer Maximaldauer inaktiv
 * Accounts im Urlaubsmodus erscheinen nicht mehr in der Statistik

### Bugfixes ###

  * Flottenfavoriten anlegen unter IE nicht möglich
  * Allianzplaneten / Galaxieübersicht: fehler bei allianzlosen spielern
  * Fehlermeldung beim Senden von Nachrichten
  * Dollarzeichen wird von Statistik-Suchfunktion ignoriert
  * Distanz unterschiedlich bei Hin- und Rückflug
  * Beim Forschen werden die Bewohner fürs Lab auf allen Planeten blockiert
  * Umlaut wird nicht korrekt dargestellt
  * xajax-generierte Inputs und keybinds vertragen sich nicht
  * Ressourcenmenge in Berichten übersteigt uint16_max-Grenze
  * Datenbankfehler bei Allianztag mit Backslash
