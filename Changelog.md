Escape to Andromeda Changelog
=============================

Version 3.4.0
-------------

### General ###

#### Features ####

 * #8 Added boost system for resource production and building speed
 * #10 Crypto center (alliance building) cooldown is now individual for each user
 * #16 Number of alliance members can now be limited (defaults to 7)
 * #9 New mysticum feat: Reduce launch and landing time (Readiness)
 * #18 Added tutorial system
 * #13 bbcode-[url]-links open in a new tab
 * #21 Noob protection minimal attackable threshold points implemented
 * Added text format help link to various text fields

#### Bugfixes ####

 * #2 Fixed "Query error" message if holiday mode gets enabled
 * #11 Minbari mysticum no longer hides whole alliance fleet
 * #5 Fixed "msg_send fail" on specialist activation
 * #25 Fixed broken link in admin ticket system
 * #7 Fixed fuel/food doubling on support abort
 * #30 Fixed "banned" instead of "kicked" message in Chat
 * Fixed a DB query bug with single quotes in alliance names in the alliance founding process
 * Fixed possible SQL injection vulnerabilities in notepad class
 * Fixed race detail page in help
 * User and allinace profile image directories are now being created created if they not exist
 
### Administration ###

#### Features ####

 * #20 Improved text management
 * Changelogs page added
 * User- and game stats page combined
 * Alliance battle system can now be disabled in configuration
 * Alliance battle system can now be restricted to alliances at war only in configuration (#19)

#### Bugfixes ####

 * Fixed coordinate selector (position) on galaxy screen


Version 3.3.0
-------------

### General ###

#### Features ####

 * Keybinds hinzugefügt
 * Cleanup der Konfiguration (defaults.xml)
 * PNs lesen und schreiben ist nun auch während dem Urlaubsmodus möglich
 * Spezialist wird nun während den Urlaubsmodus pausiert
 * User werden nun benachrichtigt bei Nachrichten von Admins im Ticketsystem
 * Kryptobericht wird gespeichert
 * Accounts im Urlaubsmodus werden nach einer Maximaldauer inaktiv
 * Accounts im Urlaubsmodus erscheinen nicht mehr in der Statistik

#### Bugfixes ####

  * #49 Flottenfavoriten anlegen unter IE nicht möglich
  * #83 Allianzplaneten / Galaxieübersicht: fehler bei allianzlosen spielern
  * #87 Fehlermeldung beim Senden von Nachrichten
  * #91 Dollarzeichen wird von Statistik-Suchfunktion ignoriert
  * #97 Distanz unterschiedlich bei Hin- und Rückflug
  * #113 Beim Forschen werden die Bewohner fürs Lab auf allen Planeten blockiert
  * #122 Umlaut wird nicht korrekt dargestellt
  * #857 xajax-generierte Inputs und keybinds vertragen sich nicht
  * #119 Ressourcenmenge in Berichten übersteigt uint16_max-Grenze
  * #858 Datenbankfehler bei Allianztag mit Backslash 

### Administration ###

#### Features ####
 
 * Wings können ausgeschaltet werden
 
#### Bugfixes ####
 
 * #63 Galaxiekarte im tool aufdecken
 * #93 Chatadmin-Funktionen gehen nicht, wenn der User ein Leerzeichen im Namen ha
 * #95 Inaktive User werden nicht automatisch gelöscht, gleiches bei Löschantrag
 * #109 Admintool: Flotten verlieren Ladung
