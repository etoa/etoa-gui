<?php

return [
    "Allgemeines" => [
        "page" => "overview",
        "path" => "/admin/overview/",
        "route" => "admin.overview",
        "roles" => "master,super-admin,game-admin,trial-game-admin,chat-admin",
        "children" => [
            "Rangliste" => [
                "sub" => "stats",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "path" => "/admin/stats/users",
                "route" => "admin.stats.users"
            ],
            "Offline nehmen" => [
                "route" => "admin.overview.game-offline",
                "roles" => "master,super-admin,game-admin",
            ],
            "Changelog" => [
                "route" => "admin.overview.changelog",
                "roles" => "master,super-admin,game-admin,trial-game-admin"
            ],
            "Spielstatistiken" => [
                "route" => "admin.overview.gamestats",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
        ]
    ],
    "Administratoren" => [
        "route" => "admin.admin_management",
        "roles" => "master,super-admin,game-admin,trial-game-admin,chat-admin",
        "children" => [
            "Admin-Management" => [
                "route" => "admin.admin_management",
                "roles" => "master,super-admin",
                "additional_routes" => [
                    'admin.admin_management.new',
                    'admin.admin_management.edit',
                ],
            ],
            "Admin-Sessions" => [
                "route" => "admin.admin-sessions",
                "roles" => "master,super-admin",
            ],
        ]
    ],
    "In-Game Hilfe" => [
        "page" => "help",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "children" => [
            "Technikbaum" => [
                "sub" => "techtree",
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/help/techtree",
                "route" => "admin.help.techtree"
            ]
        ]
    ],
    "Spieler" => [
        "page" => "user",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "path" => "/admin/users/",
        "route" => "admin.users",
        "additional_routes" => [
            'admin.users.view',
            'admin.users.edit',
            'admin.users.economy',
            'admin.users.messages',
            'admin.users.comments',
            'admin.users.logs',
            'admin.users.tickets',
            'admin.users.pointProgression',
            'admin.users.user_login_failures',
            'admin.users.user_multi',
            'admin.users.user_sitting',
        ],
        "children" => [
            "Spieler erstellen" => [
                "route" => "admin.users.new",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Sessions" => [
                "route" => "admin.users.sessions",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Multi-Kontrolle" => [
                "route" => "admin.users.multis",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "IP-Suche" => [
                "route" => "admin.users.ips",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Sitting" => [
                "route" => "admin.users.sitting",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Punkteverlauf" => [
                "route" => "admin.users.points",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Sessionlogs" => [
                "route" => "admin.users.session-log",
                "roles" => "master,super-admin,game-admin",
            ],
            "Profilbilder prüfen" => [
                "route" => "admin.users.imagecheck",
                "roles" => "master,super-admin,game-admin"
            ],
            "Spieler-Banner" => [
                "route" => "admin.users.banners",
                "roles" => "master,super-admin,game-admin",
            ],
            "Fehlerhafte Logins" => [
                "route" => "admin.users.login-failures",
                "roles" => "master,super-admin,game-admin",
            ],
            "Beobachter" => [
                "route" => "admin.users.observer",
                "roles" => "master,super-admin,game-admin",
                "additional_routes" => [
                    'admin.users.observer.details',
                    'admin.users.observer.details.session',
                    'admin.users.observer.edit',
                ]
            ],
            "Verwarnungen" => [
                "route" => "admin.users.warnings",
                "roles" => "master,super-admin,game-admin",
                "additional_routes" => [
                    'admin.users.warnings.edit',
                ],
            ],
            "XML-Export/Import" => [
                "route" => "admin.users.xml",
                "roles" => "master,super-admin,game-admin",
                "additional_routes" => [
                    'admin.users.xml.details',
                ],
                "bar" => true
            ],
            "Rassen" => [
                "route" => "admin.races.data",
                "roles" => "master,super-admin"
            ],
            "Spezialisten" => [
                "route" => "admin.specialists.data",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Allianzen" => [
        "page" => "alliances",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "route" => "admin.alliances",
        "path" => "/admin/alliances/",
        "additional_routes" => [
            'admin.alliances.edit',
        ],
        "children" => [
            "Allianz erstellen" => [
                "sub" => "create",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "route" => "admin.alliances.new",
                "path" => "/admin/alliances/new"
            ],
            "Allianz-News (Rathaus)" => [
                "sub" => "news",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "route" => "admin.alliances.news",
                "path" => "/admin/alliances/news"
            ],
            "Fehlerhafte Daten" => [
                "sub" => "crap",
                "roles" => "master,super-admin,game-admin",
                "route" => "admin.alliances.crap",
                "path" => "/admin/alliances/crap"
            ],
            "Bilder prüfen" => [
                "sub" => "imagecheck",
                "roles" => "master,super-admin,game-admin",
                "bar" => true,
                "route" => "admin.alliances.imagecheck",
                "path" => "/admin/alliances/imagecheck"
            ],
            "Gebäude bearbeiten" => [
                "route" => "admin.alliances.buildings.data",
                "roles" => "master,super-admin"
            ],
            "Technologien bearbeiten" => [
                "route" => "admin.alliances.technologies.data",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Flotten" => [
        "page" => "fleets",
        "roles" => "master,super-admin,game-admin",
        "path" => "/admin/fleets",
        "route" => "admin.fleets",
        "children" => [
            "Flotte erstellen" => [
                "path" => "/admin/fleets/new",
                "route" => "admin.fleets.new",
                "roles" => "master,super-admin,game-admin",
                "sub" => "one"
            ],
            "Schiffe senden" => [
                "path" => "/admin/fleets/send-ships",
                "route" => "admin.fleets.send-ships",
                "roles" => "master,super-admin,game-admin",
                "sub" => "one"
            ],
            "Flottenoptionen" => [
                "sub" => "fleetoptions",
                "path" => "/admin/fleets/options",
                "route" => "admin.fleets.options",
                "roles" => "master,super-admin,game-admin"
            ]
        ]
    ],
    "Gebäude" => [
        "page" => "buildings",
        "roles" => "master,super-admin,game-admin",
        "path" => "/admin/buildings/search",
        "route" => "admin.buildings.search",
        "children" => [
            "Preisrechner" => [
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/buildings/cost-calculator",
                "route" => "admin.buildings.cost-calculator"
            ],
            "Gebäude bearbeiten" => [
                "route" => "admin.buildings.data",
                "roles" => "master,super-admin"
            ],
            "Kategorien" => [
                "sub" => "type",
                "roles" => "master,super-admin"
            ],
            "Voraussetzungen" => [
                "sub" => "req",
                "roles" => "master,super-admin",
                "path" => "/admin/buildings/requirements",
                "route" => "admin.buildings.requirements"
            ],
            "Gebäudepunkte" => [
                "sub" => "points",
                "roles" => "master,super-admin",
                "path" => "/admin/buildings/points",
                "route" => "admin.buildings.points"
            ]
        ]
    ],
    "Forschung" => [
        "page" => "techs",
        "roles" => "master,super-admin,game-admin",
        "path" => "/admin/technology/",
        "route" => "admin.technology",
        "children" => [
            "Technologien bearbeiten" => [
                "route" => "admin.technologies.data",
                "roles" => "master,super-admin"
            ],
            "Kategorien" => [
                "sub" => "type",
                "roles" => "master,super-admin"
            ],
            "Voraussetzungen" => [
                "sub" => "req",
                "roles" => "master,super-admin",
                "path" => "/admin/technology/requirements",
                "route" => "admin.technology.requirements"
            ],
            "Forschungspunkte" => [
                "sub" => "points",
                "roles" => "master,super-admin",
                "path" => "/admin/technology/points",
                "route" => "admin.technology.points"
            ]
        ]
    ],
    "Schiffe" => [
        "page" => "ships",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Bauliste" => [
                "sub" => "queue",
                "roles" => "master,super-admin,game-admin"
            ],
            "XP-Rechner" => [
                "sub" => "xpcalc",
                "roles" => "master,super-admin,game-admin",
                "bar" => true
            ],
            "Schiffe bearbeiten" => [
                "route" => "admin.ships.data",
                "roles" => "master,super-admin"
            ],
            "Voraussetzungen" => [
                "sub" => "req",
                "roles" => "master,super-admin",
                "path" => "/admin/ships/requirements",
                "route" => "admin.ships.requirements"
            ],
            "Kategorien" => [
                "sub" => "cat",
                "roles" => "master,super-admin"
            ],
            "Punkte" => [
                "sub" => "battlepoints",
                "roles" => "master,super-admin",
                "path" => "/admin/ships/points",
                "route" => "admin.ships.points"
            ]
        ]
    ],
    "TF-Rechner" => [
        "page" => "tfcalculator",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "path" => "/admin/tf-calculator",
        "route" => "admin.tf-calculator"
    ],
    "Verteidigung" => [
        "page" => "def",
        "roles" => "master,super-admin,game-admin",
        "path" => "/admin/defense/search",
        "route" => "admin.defense.search",
        "children" => [
            "Bauliste" => [
                "sub" => "queue",
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/defense/queue",
                "route" => "admin.defense.queue"
            ],
            "Verteidigung bearbeiten" => [
                "route" => "admin.defense.data",
                "roles" => "master,super-admin,game-admin"
            ],
            "Voraussetzungen" => [
                "sub" => "req",
                "roles" => "master,super-admin",
                "path" => "/admin/defense/requirements",
                "route" => "admin.defense.requirements"
            ],
            "Kategorien" => [
                "sub" => "cat",
                "roles" => "master,super-admin"
            ],
            "Punkte" => [
                "sub" => "battlepoints",
                "roles" => "master,super-admin",
                "path" => "/admin/defense/points",
                "route" => "admin.defense.points"
            ],
            "Transformationen" => [
                "route" => "admin.defense.transforms",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Raketen" => [
        "page" => "missiles",
        "roles" => "master,super-admin,game-admin",
        "path" => "/admin/missiles/",
        "route" => "admin.missiles",
        "children" => [
            "Raketen bearbeiten" => [
                "route" => "admin.missiles.data",
                "roles" => "master,super-admin,game-admin"
            ],
            "Voraussetzungen" => [
                "sub" => "req",
                "roles" => "master,super-admin",
                "path" => "/admin/missiles/requirements",
                "route" => "admin.missiles.requirements"
            ]
        ]
    ],
    "Galaxie" => [
        "page" => "galaxy",
        "roles" => "master,super-admin,game-admin",
        "path" => "/admin/universe/entities",
        "route" => "admin.universe.entities",
        "children" => [
            "Karte" => [
                "sub" => "map",
                "path" => "/admin/universe/map",
                "route" => "admin.universe.map",
                "roles" => "master,super-admin,game-admin"
            ],
            "Integrität prüfen" => [
                "sub" => "galaxycheck",
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/univese/check",
                "route" => "admin.universe.check"
            ],
            "Erkundung" => [
                "sub" => "exploration",
                "path" => "/admin/universe/exploration",
                "route" => "admin.universe.exploration",
                "roles" => "master,super-admin,game-admin",
                "bar" => true
            ],
            "Universum" => [
                "sub" => "uni",
                "roles" => "master",
                "path" => "/admin/univese/edit",
                "route" => "admin.universe.edit"
            ],
            "Planetentypen" => [
                "route" => "admin.universe.planets.data",
                "roles" => "master,super-admin"
            ],
            "Sonnentypen" => [
                "route" => "admin.universe.stars.data",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Nachrichten" => [
        "page" => "messages",
        "roles" => "master,super-admin,game-admin",
        "path" => "/admin/messages/",
        "route" => "admin.messages",
        "children" => [
            "Nachricht senden" => [
                "sub" => "sendmsg",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "path" => "/admin/messages/send",
                "route" => "admin.messages.send"
            ],
            "Berichte verwalten" => [
                "sub" => "reports",
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/messages/reports",
                "route" => "admin.messages.reports"
            ]
        ]
    ],
    "Chat" => [
        "page" => "chat",
        "roles" => "master,super-admin,game-admin,trial-game-admin,chat-admin",
        "path" => "/admin/chat/",
        "route" => "admin.chat",
        "children" => [
            "Log" => [
                "sub" => "log",
                "roles" => "master,super-admin,game-admin,chat-admin",
                "path" => "/admin/chat/log",
                "route" => "admin.chat.log"
            ]
        ]
    ],
    "Marktplatz" => [
        "page" => "market",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "path" => "/admin/market",
        "route" => "admin.market",
        "children" => [
            "Schiffe" => [
                "sub" => "ships",
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/market/ships",
                "route" => "admin.market.ships"
            ],
            "Rohstoffe" => [
                "sub" => "ress",
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/market/resources",
                "route" => "admin.market.resources"
            ],
            "Auktionen" => [
                "sub" => "auction",
                "roles" => "master,super-admin,game-admin",
                "path" => "/admin/market/auctions",
                "route" => "admin.market.auctions"
            ]
        ]
    ],
    "Logs" => [
        "page" => "logs",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "path" => "/admin/logs",
        "route" => "admin.logs.general",
        "children" => [
            "Spiel" => [
                "sub" => "gamelogs",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "path" => "/admin/logs/game",
                "route" => "admin.logs.game"
            ],
            "Flotten" => [
                "sub" => "fleetlogs",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "path" => "/admin/logs/fleets",
                "route" => "admin.logs.fleets"
            ],
            "Angriffsverletzung" => [
                "sub" => "check_fights",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "path" => "/admin/logs/attack-ban",
                "route" => "admin.logs.attack-ban"
            ],
            "Fehler-Log" => [
                "sub" => "errorlog",
                "path" => "/admin/logs/error",
                "route" => "admin.logs.error",
                "roles" => "master,super-admin"
            ],
            "Trümmerfeld-Log" => [
                "sub" => "debrislog",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
                "path" => "/admin/logs/debris",
                "route" => "admin.logs.debris"
            ]
        ]
    ],
    "Eventhandler" => [
        "page" => "eventhandler",
        "path" => "/admin/eventhandler",
        "route" => "admin.eventhandler",
        "roles" => "master,super-admin,game-admin"
    ],
    "Periodische Tasks" => [
        "page" => "cronjob",
        "path" => "/admin/cronjob/",
        "route" => "admin.cronjob",
        "roles" => "master,super-admin,game-admin"
    ],
    "Texte" => [
        "page" => "texts",
        "path" => "/admin/texts",
        "route" => "admin.texts",
        "roles" => "master,super-admin,game-admin",
        "children" => [
        ]
    ],
    "Tutorials" => [
        "route" => "admin.tutorials",
        "roles" => "master,super-admin,game-admin",
        "children" => [
        ]
    ],
    "Konfiguration" => [
        "page" => "config",
        "route" => "admin.config",
        "path" => "/admin/config",
        "roles" => "master,super-admin",
        "children" => [
            "Erweiterte Konfiguration" => [
                "sub" => "editor",
                "route" => "admin.config.editor",
                "path" => "/admin/config/editor",
                "roles" => "master"
            ],
            "Integritätsprüfung" => [
                "sub" => "check",
                "route" => "admin.config.check",
                "path" => "/admin/config/check",
                "roles" => "master"
            ],
            "Zurücksetzen" => [
                "sub" => "restoredefaults",
                "route" => "admin.config.restore",
                "path" => "/admin/config/restore",
                "roles" => "master"
            ]
        ]
    ],
    "Datenbank" => [
        "route" => "admin.db",
        "path" => "/admin/db",
        "page" => "db",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Backups" => [
                "route" => "admin.db.backups",
                "path" => "/admin/db/backups",
                "sub" => "backup",
                "roles" => "master,super-admin"
            ],
            "Clean-Up" => [
                "sub" => "cleanup",
                "roles" => "master,super-admin",
                "route" => "admin.db.cleanup",
                "path" => "/admin/db/cleanup"
            ],
            "Schema-Migrationen" => [
                "route" => "admin.db.migration",
                "path" => "/admin/db/migration",
                "sub" => "migrations",
                "roles" => "master,super-admin"
            ],
            "Reset" => [
                "route" => "admin.db.reset",
                "path" => "/admin/db/reset",
                "sub" => "reset",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Diverses" => [
        "page" => "misc",
        "roles" => "master",
        "children" => [
            "Designs" => [
                "sub" => "designs",
                "path" => "/admin/designs/",
                "route" => "admin.design",
                "roles" => "master"
            ],
            "Start-Items" => [
                "sub" => "defaultitems",
                "roles" => "master",
                "route" => "admin.default-items",
                "path" => "/admin/default-items"
            ],
            "Tipps" => [
                "route" => "admin.tipps",
                "roles" => "master,super-admin"
            ],
            "Ticket-Kategorien" => [
                "sub" => "ticketcat",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Tools" => [
        "page" => "tools",
        "route" => "admin.tools.index",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Datei-Austausch" => [
                "route" => "admin.tools.filesharing",
                "sub" => "filesharing",
                "roles" => "master,super-admin,game-admin"
            ],
            "Seitenzugriffe" => [
                "route" => "admin.tools.accesslog",
                "sub" => "accesslog",
                "roles" => "master"
            ],
            "IP-Resolver" => [
                "route" => "admin.tools.ipresolver",
                "sub" => "ipresolver",
                "roles" => "master"
            ]
        ]
    ]
];

