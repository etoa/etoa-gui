Escape to Andromeda Changelog
=============================

Version 3.4
------------

### Features ###

 * Added boost system for resource production and building speed (#8)
 * Crypto center (alliance building) cooldown is now individual for each user (#10)
 * Number of alliance members can now be limited (defaults to 7) (#16)
 * New mysticum feat: Reduce launch and landing time (Readiness) (#9)
 * Added tutorial system (#18)
 * bbcode-[url]-links open in a new tab (#13)
 * Noob protection minimal attackable threshold points implemented (#21)
 * [ADMIN] Improved text management (#20)
 * [ADMIN] Changelogs page added
 * [ADMIN] User- and game stats page combined

### Bugfixes ###

 * Fixed "Query error" message if holiday mode gets enabled (#2)
 * Minbari mysticum no longer hides whole alliance fleet (#11)
 * Fixed "msg_send fail" on specialist activation (#5)
 * Fixed a DB query bug with single quotes in alliance names in the alliance founding process
 * Fixed possible SQL injection vulnerabilities in notepad class
 * Fixed race detail page in help
 * Fixed broken link in admin ticket system (#25)
 * Fixed fuel/food doubling on support abort (#7)
 * User and allinace profile image directories are now being created created if they not exist
 * [ADMIN] Fixed coordinate selector (position) on galaxy screen 

Version 3.3
-----------

### Features ###

 * Keybinds hinzugefügt
 * Cleanup der Konfiguration (defaults.xml)
 * PNs lesen und schreiben ist nun auch während dem Urlaubsmodus möglich
 * Spezialist wird nun während den Urlaubsmodus pausiert
 * User werden nun benachrichtigt bei Nachrichten von Admins im Ticketsystem
 * Kryptobericht wird gespeichert
 * Accounts im Urlaubsmodus werden nach einer Maximaldauer inaktik
 * Accounts im Urlaubsmodus erscheinen nicht mehr in der Statistik
 * Wings können ausgeschaltet werden


### Bugfixes ###

  * *#49* Flottenfavoriten anlegen unter IE nicht möglich
  * *#63* Galaxiekarte im tool aufdecken
  * *#83* Allianzplaneten / Galaxieübersicht: fehler bei allianzlosen spielern
  * *#87* Fehlermeldung beim Senden von Nachrichten
  * *#91* Dollarzeichen wird von Statistik-Suchfunktion ignoriert
  * *#93* Chatadmin-Funktionen gehen nicht, wenn der User ein Leerzeichen im Namen ha
  * *#95* Inaktive User werden nicht automatisch gelöscht, gleiches bei Löschantrag
  * *#97* Distanz unterschiedlich bei Hin- und Rückflug
  * *#109* Admintool: Flotten verlieren Ladung
  * *#113* Beim Forschen werden die Bewohner fürs Lab auf allen Planeten blockiert
  * *#122* Umlaut wird nicht korrekt dargestellt
  * *#857* xajax-generierte Inputs und keybinds vertragen sich nicht
  * *#119* Ressourcenmenge in Berichten übersteigt uint16_max-Grenze
  * *#858* Datenbankfehler bei Allianztag mit Backslash 
