<?php

return [
    "Allgemeines" => [
        "route" => "admin.overview",
        "roles" => "master,super-admin,game-admin,trial-game-admin,chat-admin",
        "children" => [
            "Rangliste" => [
                "route" => "admin.stats.users",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
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
    "Spieler" => [
        "route" => "admin.users",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "additional_routes" => [
            'admin.users.view',
            'admin.users.edit',
            'admin.users.economy',
            'admin.users.messages',
            'admin.users.comments',
            'admin.users.logs',
            'admin.users.tickets',
            'admin.users.points',
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
            "Sessionlogs" => [
                "route" => "admin.users.session-log",
                "roles" => "master,super-admin,game-admin",
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
        "route" => "admin.alliances",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "additional_routes" => [
            'admin.alliances.edit',
        ],
        "children" => [
            "Allianz erstellen" => [
                "route" => "admin.alliances.new",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Allianz-News (Rathaus)" => [
                "route" => "admin.alliances.news",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Fehlerhafte Daten" => [
                "route" => "admin.alliances.crap",
                "roles" => "master,super-admin,game-admin",
            ],
            "Bilder prüfen" => [
                "route" => "admin.alliances.imagecheck",
                "roles" => "master,super-admin,game-admin",
                "bar" => true,
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
        "route" => "admin.fleets",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Flotte erstellen" => [
                "route" => "admin.fleets.new",
                "roles" => "master,super-admin,game-admin",
            ],
            "Schiffe senden" => [
                "route" => "admin.fleets.send-ships",
                "roles" => "master,super-admin,game-admin",
            ],
            "Flottenoptionen" => [
                "route" => "admin.fleets.options",
                "roles" => "master,super-admin,game-admin"
            ]
        ]
    ],
    "Gebäude" => [
        "route" => "admin.buildings.search",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Preisrechner" => [
                "roles" => "master,super-admin,game-admin",
                "route" => "admin.buildings.cost-calculator"
            ],
            "Gebäude bearbeiten" => [
                "route" => "admin.buildings.data",
                "roles" => "master,super-admin"
            ],
            "Kategorien" => [
                "route" => "admin.buildings.types",
                "roles" => "master,super-admin"
            ],
            "Voraussetzungen" => [
                "route" => "admin.buildings.requirements",
                "roles" => "master,super-admin",
            ],
            "Gebäudepunkte" => [
                "route" => "admin.buildings.points",
                "roles" => "master,super-admin",
            ]
        ]
    ],
    "Forschung" => [
        "route" => "admin.technology",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Technologien bearbeiten" => [
                "route" => "admin.technologies.data",
                "roles" => "master,super-admin"
            ],
            "Kategorien" => [
                "route" => "admin.technologies.types",
                "roles" => "master,super-admin"
            ],
            "Voraussetzungen" => [
                "route" => "admin.technology.requirements",
                "roles" => "master,super-admin",
            ],
            "Forschungspunkte" => [
                "route" => "admin.technology.points",
                "roles" => "master,super-admin",
            ]
        ]
    ],
    "Schiffe" => [
        "route" => "admin.ships.search",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Bauliste" => [
                "route" => "admin.ships.queue",
                "roles" => "master,super-admin,game-admin"
            ],
            "XP-Rechner" => [
                "route" => "admin.ships.xp-calculator",
                "roles" => "master,super-admin,game-admin",
                "bar" => true
            ],
            "Schiffe bearbeiten" => [
                "route" => "admin.ships.data",
                "roles" => "master,super-admin"
            ],
            "Voraussetzungen" => [
                "route" => "admin.ships.requirements",
                "roles" => "master,super-admin",
            ],
            "Kategorien" => [
                "route" => "admin.ships.types",
                "roles" => "master,super-admin"
            ],
            "Punkte" => [
                "route" => "admin.ships.points",
                "roles" => "master,super-admin",
            ]
        ]
    ],
    "Verteidigung" => [
        "roles" => "master,super-admin,game-admin",
        "route" => "admin.defense.search",
        "children" => [
            "Bauliste" => [
                "route" => "admin.defense.queue",
                "roles" => "master,super-admin,game-admin",
            ],
            "Verteidigung bearbeiten" => [
                "route" => "admin.defense.data",
                "roles" => "master,super-admin,game-admin"
            ],
            "Voraussetzungen" => [
                "route" => "admin.defense.requirements",
                "roles" => "master,super-admin",
            ],
            "Kategorien" => [
                "route" => "admin.defense.types",
                "roles" => "master,super-admin"
            ],
            "Punkte" => [
                "route" => "admin.defense.points",
                "roles" => "master,super-admin",
            ],
            "Transformationen" => [
                "route" => "admin.defense.transforms",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Raketen" => [
        "route" => "admin.missiles",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Raketen bearbeiten" => [
                "route" => "admin.missiles.data",
                "roles" => "master,super-admin,game-admin"
            ],
            "Voraussetzungen" => [
                "route" => "admin.missiles.requirements",
                "roles" => "master,super-admin",
            ]
        ]
    ],
    "Galaxie" => [
        "route" => "admin.universe.entities",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Karte" => [
                "route" => "admin.universe.map",
                "roles" => "master,super-admin,game-admin"
            ],
            "Integrität prüfen" => [
                "route" => "admin.universe.check",
                "roles" => "master,super-admin,game-admin",
            ],
            "Erkundung" => [
                "route" => "admin.universe.exploration",
                "roles" => "master,super-admin,game-admin",
                "bar" => true
            ],
            "Universum" => [
                "route" => "admin.universe.edit",
                "roles" => "master",
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
        "route" => "admin.messages",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "Nachricht senden" => [
                "route" => "admin.messages.send",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Berichte verwalten" => [
                "route" => "admin.messages.reports",
                "roles" => "master,super-admin,game-admin",
            ]
        ]
    ],
    "Chat" => [
        "route" => "admin.chat",
        "roles" => "master,super-admin,game-admin,trial-game-admin,chat-admin",
        "children" => [
            "Log" => [
                "route" => "admin.chat.log",
                "roles" => "master,super-admin,game-admin,chat-admin",
            ]
        ]
    ],
    "Marktplatz" => [
        "route" => "admin.market",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "children" => [
            "Schiffe" => [
                "route" => "admin.market.ships",
                "roles" => "master,super-admin,game-admin",
            ],
            "Rohstoffe" => [
                "route" => "admin.market.resources",
                "roles" => "master,super-admin,game-admin",
            ],
            "Auktionen" => [
                "route" => "admin.market.auctions",
                "roles" => "master,super-admin,game-admin",
            ]
        ]
    ],
    "Logs" => [
        "route" => "admin.logs.general",
        "roles" => "master,super-admin,game-admin,trial-game-admin",
        "children" => [
            "Spiel" => [
                "route" => "admin.logs.game",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Flotten" => [
                "route" => "admin.logs.fleets",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Angriffsverletzung" => [
                "route" => "admin.logs.attack-ban",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Fehler-Log" => [
                "route" => "admin.logs.error",
                "roles" => "master,super-admin",
            ],
            "Trümmerfeld-Log" => [
                "route" => "admin.logs.debris",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ]
        ]
    ],
    "Eventhandler" => [
        "route" => "admin.eventhandler",
        "roles" => "master,super-admin,game-admin"
    ],
    "Periodische Tasks" => [
        "route" => "admin.cronjob",
        "roles" => "master,super-admin,game-admin"
    ],
    "Texte" => [
        "route" => "admin.texts",
        "roles" => "master,super-admin,game-admin",
    ],
    "Tutorials" => [
        "route" => "admin.tutorials",
        "roles" => "master,super-admin,game-admin",
    ],
    "Konfiguration" => [
        "route" => "admin.config",
        "roles" => "master,super-admin",
        "children" => [
            "Erweiterte Konfiguration" => [
                "route" => "admin.config.editor",
                "roles" => "master"
            ],
            "Integritätsprüfung" => [
                "route" => "admin.config.check",
                "roles" => "master"
            ],
            "Zurücksetzen" => [
                "route" => "admin.config.restore",
                "roles" => "master"
            ]
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
            "Admin Sessionlogs" => [
                "route" => "admin.admin-sessionlogs",
                "roles" => "master,super-admin",
            ],
        ]
    ],
    "Datenbank" => [
        "route" => "admin.db",
        "roles" => "master,super-admin,game-admin",
        'additional_routes' => [
            'admin.db.restore',
            'admin.db.optimize',
            'admin.db.analyze',
            'admin.db.repair',
            'admin.db.check',
        ],
        "children" => [
            "Backups" => [
                "route" => "admin.db.backups",
                "roles" => "master,super-admin"
            ],
            "Clean-Up" => [
                "route" => "admin.db.cleanup",
                "roles" => "master,super-admin",
            ],
            "Schema-Migrationen" => [
                "route" => "admin.db.migration",
                "roles" => "master,super-admin",
            ],
            "Reset" => [
                "route" => "admin.db.reset",
                "roles" => "master,super-admin",
            ]
        ]
    ],
    "Diverses" => [
        "route" => "admin.misc.index",
        "roles" => "master",
        "children" => [
            "Designs" => [
                "route" => "admin.design",
                "roles" => "master"
            ],
            "Start-Items" => [
                "route" => "admin.default-items",
                "roles" => "master",
            ],
            "Tipps" => [
                "route" => "admin.tipps",
                "roles" => "master,super-admin"
            ],
            "Ticket-Kategorien" => [
                "route" => "admin.ticket.categories",
                "roles" => "master,super-admin"
            ]
        ]
    ],
    "Tools" => [
        "route" => "admin.tools.index",
        "roles" => "master,super-admin,game-admin",
        "children" => [
            "TF-Rechner" => [
                "route" => "admin.tf-calculator",
                "roles" => "master,super-admin,game-admin,trial-game-admin",
            ],
            "Technikbaum" => [
                "route" => "admin.help.techtree",
                "roles" => "master,super-admin,game-admin",
            ],
            "Datei-Austausch" => [
                "route" => "admin.tools.filesharing",
                "roles" => "master,super-admin,game-admin"
            ],
            "Seitenzugriffe" => [
                "route" => "admin.tools.accesslog",
                "roles" => "master"
            ],
            "IP-Resolver" => [
                "route" => "admin.tools.ipresolver",
                "roles" => "master"
            ]
        ]
    ]
];

