Escape to Andromeda Changelog
=============================

Version 3.6.1 (unreleased)
--------------

### Bugfixes ###
* replace remaining smarty templates with twig

Version 3.6 (round 20)
--------------
### Features ###

* added info line 
* added the option to switch the main planet once
* added option to mark ships as tradeable
* added work optimize functionality for building defense

### Bugfixes ###
* fixed background images for large screen sizes
* fixed the mouse over for the food cost on the defense page 
* deactivated buildings can no longer be demolished
* emp attack now only targets buildings level 1 or higher
* fixed several compatibility issues with Mysql5.7
* replaced all game and most of the admin smarty templates with twig
 

Version 3.5.24
--------------
### Features ###

* new ship-images

### Bugfixes ###

* fixed wrong production display at overview

Version 3.5.23
--------------
### Features ###

* pumpkin cruiser added

### Bugfixes ###

* no more annoying scrollbar in chat after long messages

Version 3.5.22 (round18)
--------------

### Features ###

* scans don't show bunker ress anymore
* selection filter when choosing planet

Version 3.5.21
--------------

### Bugfixes ###

* Various fixes to stay compatible with PHP 7.2

Version 3.5.20
--------------

### Bugfixes ###

* Various fixes to stay compatible with PHP 7.2

Version 3.5.19
--------------

### Administration ###

 * added aliens/NPC option to tool

### Features ###

 * color for aliens/npc 


Version 3.5.18
--------------

### Features ###

 * boost working with total points


Version 3.5.17
--------------

### Bugfixes ###

 * fix for display of people growth (e.g. "voll in:")
 * fix for too long buildtime
 * fix for early sign up
 * fix for taking over planets when setting up account
 * fix for selecting unhabitable planets as startplanet
 * fix for mp filter
 * fix for fake attacks
    
### Administration ###

 * fix for market->ships
 * option to disable alliances

Version 3.5.16
--------------

### Bugfixes ###

 * fixed mp bug 
 * fixed deff text at help page
 * fixed white page when session expired
 * fixed capacity from ships
 
### Administration ###

 * added createable userlogs
 * fixed fleetlog bug
 * added log for debris splitting


Version 3.5.15.1
----------------

### Bugfixes ###

 * fixed periodic tasks
 * fixed help for planets
 * fixed some error msg at population
 * fixed tutorial close


### Administration ###

 * added two-factor authentication for administrators

Version 3.5.15(round 16)
------------------------

### Bugfixes ###

 * heal-bug fixed where joint defending lead to overhealing
 * fixed allianceattack bug where u could join without timelimit
 * fixed bonuscapacity from myslis for capacity in flightoverview, fetch and collect actions
 * fixed wrong error msg after trying to buy missles without enough ressources

### Features ###

 * Heal limitation added, adjustable in the tool
 * Logistic population growth added
 * reservated ships will be shown at top of market now
 * colorpicker for chatcolor
 * added log for nickchange
 * deleting request will trigger umode now
 
### Administration ###
 
 * fixed editing for already building ships 
 
Version 3.5.14.1
---------------

### Bugfixes ###

 * fixed log-in problems caused by https
 * fixed problem with lvl 1 buildings
 
Version 3.5.14
---------------

### Bugfixes ###

 * fixed krypto distance
 * fixed start/landtime from vorgonia mysli when joining alli atk
 * fixed wrong display of min buildtime in shipyard
 * fixed missle distance
 * fixed gen tech worker cannot be picked up anymore
 * fixed spy attack has no effect on stats/elo anymore

### Administration ###
 
 * adding from alli techs
 * added function to restore messages (reports will follow later)
 * Backend eventhandler PID file can now be stored locally
 * Cronjob can be set up automatically
 * Fixed JavaSCript error on start-objects screen 
 
Version 3.5.13
--------------

### Bugfixes ###

 * Fixed css class name for full crystal stora in small resource box

### Changes ###

 * Replace image filter script on building and research screens with CSS3 filter effects

### Administration ###
 
 * Fixed error related to mysql string escaping on admin overview page

Version 3.5.12
--------------

### Bugfixes ###

 * Fixed database error related to fleet start in haven (using more robust database queries)
 * Fixed database error related to loading user properties for image path and type in haven
 * Added missing ship gif images, fixed image size
 * Fixed an issue with invalid variables which ocurred when a user who had not yet choosen a planet opened the planet selector

### Administration ###

 * Corrected number of users using the default design in the design statistics
 * Default backup file retention time set to 7 days

Version 3.5.11
-------------

### Bugfixes ###

 * fixed ressource symbols for revolution design
 * admins wont be shown in diplomacy rating anymore
 * fixed defense bug 
 * fixed steal bug while gen was researching
 * fixed some typos

### Administration ###
 
 * even if all ships are bunkered they will be still shown in tool	
 * sitting+multi edit
 * search for dual in tool
 * button to add all techs at once for 1 person

#### Features ####

 * showing time till storage is full
 * message after creating Ticket

#### Others ####

 * increased popup size from rules

Version 3.5.10
-------------

### Bugfixes ###

 * fixed calculation for gen (used ppl for normal lab instead of genlab)
 * fixed bug where u could use more workers than available

Version 3.5.9
-------------

### Bugfixes ###

 * fixed support from ships with 0 fuelcost 
 * fixed marketreservation
 * fixed titles

#### Features ####
 * Changed message when sitting an acc
 * Changed public memberlist to default true

Version 3.5.8
-------------

### Bugfixes ###

 * fixed admiral bug
 * fixed html code at alliance
 * fixed a bug where lab didnt count workers
 * inactivity will now be displayed 7 days after registration

#### Features ####

 * added growing to asterio fields
 * tutorial can now be reopend at configurations

### Hotfix ### 

 * Fixed analyzing and collecting fuel

Version 3.5.7
-------------

### Bugfixes ###

 * Fixed birthrate
 * Fixed typos
 * Fixed bug where u couldnt destroy more than 999 missles at once	 
 * Infra ships now dont give any exp at all anymore
 * Exp gained from alliatk will now be divided by the amount of attackers
 * Removed ' from planetnames to avoid sql problems
 * Fixed problem with stealth from minbari mysli
 * Fixed an exploit where u could see disabled buildings at helppage
 * Fixed problem with readiness-lvl from vorgonia mysli
 * Fixed bug after getting kicked out of umode because of inactivity
 * Fixed wrong message when trying to kick an alliance member during allianceattack

#### Features ####

 * Added field for %-calculation at market
 * Gentech counts as special tech now(allows researching of Gentech+other tech at same time)
 * Crytocenter now shows more infos about target(e.g. distance, name, planet)
 * Reworked 404-page  
 * Added optimizing button for each ship
 * Market reservations for a person will now be displayed at top
 * Players wont be able to join another alliance for x hours after leaving old one
 * Bombing buildings is now only allowed at war 
 * New pictures for supra ships

 ### Administration ###

 * Added function to add all buildings at once
 * Added function to add alliance buildings
 * Added bounty-field to ships

Version 3.5.6
-------------

### Game ###

#### Bugfixes ####
  
 * Fixed several spelling mistakes
 * Optimizing function now uses the correct amount of workers 

### Administration ###

#### Bugfixes ####

 * Fixed shipcreation 

Version 3.5.5
-------------

#### Bugfixes ####
  
 * Missiles can't be bought anymore when the required tech is 0
 * Cardassia Mysticum now displays the correct healvalue

Version 3.5.4
-------------

#### Bugfixes ####
  
 * Architekt can't be discharged anymore after starting a building
 * Admiral can't be discharged anymore after starting a fleet
 * EMP now correctly disables market 


Version 3.5.3
-------------

### Game ###

#### Features ####

 * Added energy technology bonus (5% more per level starting from level 10)
 * Collected resources will now be shown in userinfo 

#### Bugfixes ####
  
 * Planetoverview displaying now right values in defense tab, including mysticum bonus
 * Healing bonus from mysticums will now be included in planet overview
 * Improvements of the chat frame representation in Revolution design
 
### Administration ###

#### Features ####

 * Added the name and email address from dualplayers to the player overview page
 * Active sitters will now be shown in multi detection 
 * Added collected resources from debris fields to user information
 * Public and internal changelog can now be viewed (Overview -> Changelog) 

#### Bugfixes ####
  
 * Fixed bugs in user research entry editor (status, time format)
 
### Framework ### 

#### Features ####

 * Added helper script for DB migration when using Microsoft Windows as development environment

#### Changes ####
 
 * The Revolution design is now being used in the installation wizard


Version 3.5.2
-------------

### Game ###

### Changes ###

 * The email sender address can now be changed in the admin contact form if a player is logged in
 * Changed the colors which indicate an inactive or long inactive user, so that they can now be better distinguished

#### Bugfixes ####

 * Fixed planet circle position in overview screen
 
### Administration ###

#### Bugfixes ####

 * Fixed invalid entity / planet ID in link to planets in user economy tab 
 * Fixed session APM calculation in user surveillance screen
 
### Framework ### 

#### Bugfixes ####

 * Properly regenerate session IDs on login and logout


Version 3.5.1
-------------

### Game ###

### Features ###

 * When clicking on an unknown space cell in the galaxy sector screen, an explorer fleet will be started (if available)

#### Changes ####

 * Mobile defense systems can now be transhipped on the haven screen
 * EMP attacks may deactivate the market instead of the missile silo

### Administration ###

#### Features ####

 * Added recycling tech efficiency parameter to configuration

#### Bugfixes ####

 * Fixed user nick / alliance name autocomplete field in user search screen

Version 3.5.0
-------------

### Game ###

#### Features ####

 * Added new default design "Revolution"
 * Added tab for banner in user settings screen
 * Added table of open sessions to login tab in user settings screen.
 * Added architect specialist which reduces build time by 10%
 * A password can now be specified when creating an account
 * Added button to select a random race on first login
 * Market offers can be reserved for a specified user
 * Added persistent wormholes

#### Bugfixes ####

 * Add checks for valid buildlist item (#51)
 * Do not check user's IP in session validation, but record user's IP when updating session. Fixes session problems with IPv6 dual-stack connections (#55)
 * Fixed bug where assigned people could be set to idle while building was still working
 * Fixed various sorting problems
 * Input sanitization of various database queries
 * Fixed various typos
 * Fixed encoding issues
 * Fixed multiple vulnerabilities which could have been exploited by XSS
 * Fixed several issues in the Graphite design
 * Removed BBcode tags in message preview
 * Fixed calculation of available power in planet statistics screen

#### Changes ####

 * Added new template variable ownFleetCount which indicates the number of the player's currently active fleets
 * Added new template variable infoText which shows the ingame info message, if defined
 * The button for demolishing a building is no longer shown so prominently
 * Removed externally accessible pages gamestats, help, ladded and pillory to reduce code maintenance
 * When registering a new account, the e-mail address has to be verified. Until verification is not complete, some features can not be used.
 * Radius discovered by fleet control expands at every second level of the fleet control
 * Minimal build time for ships and defenses is now 1 second
 * Removed the ugly progessbars in building and research screen until a better solution has been found
 
### Administration ###

#### Features ####

 * Added page displaying all user banners
 * Added "Error Log" screen and possibility to remove error log and database error log
 * Added system info page to admin menu
 * Added possibility to discover specific coordinates or the complete map for any user
 * Added screen which shows executed and pending database schema migrations
 * Show changed config values on "restore defaults" page
 * Time clock in header is now also ticking every second
 * A lot of market parameters can be configured in the settings
 * Crypto center cooldown can be configured in the settings
 * Missile silo parameters can be configured in the settings
 * Added possibility to manage backend daemon (start / stop)
 * Allow to select radius when changing user map discovery
 
#### Changes ####

 * Manual updates page moved to "Eventhandler" -> "Periodic tasks" menu item
 * Every periodic task can now be executed on its own
 * Open each user surveillance session table in separate screen
 * Changed layout of detailed config editor
 * If no backup directory is defined, a default path will be used
 * Backend status, current market rates and last statistics update timestamp are stored in new runtime_data storage table
 * Updated layout of imagepacks page and fixed several small bugs
 
#### Bugfixes ####

 * Limited number of entries in user observation log (#57)
 * Fixed error messages appearing in cronjob output generated by admin session cleanup mechanism (#59)
 * Limit number of displayed chat lines in admin log (#56)
 * Check if user or alliance profile image directory exists before listing images for verification
 * Forward to correct page in URL query string after login
 * User banners of non-existent users will now be removed when banners are being updated
 * Truncate user_surveillance table when resetting universe
 * Links to internal pages in messages no longer open new tab (#24)


Version 3.4.0
-------------

### Game ###

#### Features ####

 * Added boost system for resource production and building speed (#8)
 * Crypto center (alliance building) cooldown is now individual for each user (#10)
 * Number of alliance members can now be limited (defaults to 7) (#16)
 * New mysticum feat: Reduce launch and landing time (Readiness) (#9)
 * Added tutorial system (#18)
 * BBcode-[url]-links open in a new tab (#13)
 * Noob protection minimal attackable threshold points implemented (#21)
 * Added text format help link to various text fields

#### Bugfixes ####

 * Fixed "Query error" message if holiday mode gets enabled (#2)
 * Minbari mysticum no longer hides whole alliance fleet (#11)
 * Fixed "msg_send fail" on specialist activation (#5)
 * Fixed broken link in admin ticket system (#25)
 * Fixed fuel/food doubling on support abort (#7)
 * Fixed "banned" instead of "kicked" message in Chat (#30)
 * Fixed a DB query bug with single quotes in alliance names in the alliance founding process
 * Fixed possible SQL injection vulnerabilities in notepad class
 * Fixed race detail page in help
 * User and allinace profile image directories are now being created created if they not exist
 
### Administration ###

#### Features ####

 * Improved text management (#20)
 * Changelogs page added
 * User- and game stats page combined
 * Alliance battle system can now be disabled in configuration
 * Alliance battle system can now be restricted to alliances at war only in configuration (#19)

#### Bugfixes ####

 * Fixed coordinate selector (position) on galaxy screen


Version 3.3.0
-------------

### Game ###

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

### Administration ###

#### Features ####
 
 * Wings können ausgeschaltet werden
 
#### Bugfixes ####
 
 * Galaxiekarte im tool aufdecken
 * Chatadmin-Funktionen gehen nicht, wenn der User ein Leerzeichen im Namen hat
 * Inaktive User werden nicht automatisch gelöscht, gleiches bei Löschantrag
 * Admintool: Flotten verlieren Ladung
