<?php

return [
    0 => [
        'id' => 0,
        'title' => 'Verstärkung gesucht!',
        'description' => 'Eure Exzellenz, wir haben unsere Grenzen erreicht und können nur mit Mühe mit den anderen Spielern mithalten. Wir sollten versuchen Verstärkung anzufordern. Unsere Spionageabteilung hat erfahren, dass einige Spezialisten verfügbar sind und mit genügend finanzieller Überzeugung sich jene uns für eine Weile anschliessen werden. Wir sollten diese Chance unbedingt nutzen!',
        'task' => [
            'id' => 0,
            'type' => 'hire-specialist',
            'operator' => 'equal-to',
            'description' => 'Stelle einen Spezialisten ein',
            'value' => 1,
        ],
        'trigger' => [
            'id' => 0,
            'type' => 'have-specialist',
            'operator' => 'equal-to',
            'value' => 0,
        ],
        'rewards' => [
            [
                'type' => 'missile',
                'value' => 1,
                'missile_id' => 1,
            ],
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 1,
            ],
            [
                'type' => 'defense',
                'value' => 1,
                'defense_id' => 1,
            ],
        ],
    ],
    1 => [
        'id' => 1,
        'title' => 'Verräter im Haus!',
        'description' => 'Eure Exzellenz, wir mussten feststellen, dass es sich bei dem Spezialisten leider um einen feindlichen Spion handelt. Wir sollten ihn unbedingt entlassen und, wenn es ok ist, werd ich persönlich zusehen, dass er nie wieder Tageslicht sehen wird.',
        'task' => [
            'id' => 0,
            'type' => 'discharge-specialist',
            'operator' => 'equal-to-or-more',
            'description' => 'Entlasse den Verräter',
            'value' => 1,
        ],
        'trigger' => [
            'id' => 0,
            'type' => 'have-specialist',
            'operator' => 'equal-to',
            'value' => 1,
        ],
        'rewards' => [
            [
                'type' => 'missile',
                'value' => 1,
                'missile_id' => 2,
            ],
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 2,
            ],
            [
                'type' => 'defense',
                'value' => 1,
                'defense_id' => 2,
            ],
        ],
    ],
    2 => [
        'id' => 2,
        'title' => 'Raketenkriege',
        'description' => 'Eure Exzellenz, unsere Spionagesonden haben entdeckt, dass unsere Nachbarn ihr Raketenarsenal aufgestockt haben. Ein paar Raketen mehr in unserem Arsenal würden nicht schaden und werden uns helfen die Dominanz in unserem Sonnensystem sicherzustellen.',
        'task' => [
            'id' => 0,
            'type' => 'buy-missile',
            'operator' => 'equal-to-or-more',
            'description' => 'Kaufe 10 Raketen',
            'value' => 10,
        ],
        'rewards' => [
            [
                'type' => 'missile',
                'value' => 1,
                'missile_id' => 3,
            ],
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 3,
            ],
            [
                'type' => 'defense',
                'value' => 1,
                'defense_id' => 3,
            ],
        ],
    ],
    3 => [
        'id' => 3,
        'title' => 'Schrott entsorgen',
        'description' => 'Eure Exzellenz, anonyme Quellen haben wir mitgeteilt, dass einige unserer Schiffe schon etwas älter sind und die Crews sich gar weigern mit einigen Schiffen zu fliegen. Wir sollten ein paar Schiffe recyclen, damit wir später aus den Rohstoffen zuverlässigere Schiffe bauen können. ',
        'task' => [
            'id' => 0,
            'type' => 'recycle-ship',
            'operator' => 'equal-to-or-more',
            'description' => 'Recycle 5 Schiffe',
            'value' => 5,
        ],
        'rewards' => [
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 8,
            ]
        ],
    ],
    4 => [
        'id' => 4,
        'title' => '"Friendly Fire"',
        'description' => 'Eure Exzellenz, einige Piloten haben sich in letzter Zeit immer wieder beklagt, dass unsere eigenen Verteidigungsanlagen auch auf eigene Schiffe schiessen. Es ist an der Zeit die defekten Anlagen zu abzubauen und statt dessen Anlagen der neusten Generation aufzubauen.',
        'task' => [
            'id' => 0,
            'type' => 'recycle-defense',
            'operator' => 'equal-to-or-more',
            'description' => 'Recycle 5 Verteidigungsanlagen',
            'value' => 5,
        ],
        'rewards' => [
            [
                'type' => 'defense',
                'value' => 1,
                'defense_id' => 8,
            ]
        ],
    ],
    5 => [
        'id' => 5,
        'title' => 'Für noch mehr Ruhm',
        'description' => 'Eure Exzellenz, wir mussten feststellen, dass fremde Imperien nicht von unseren Heldentaten berichten, weil sie nicht wissen, wie man unseren schönen Planeten nennt. Wir müssen unserem Planeten einen Namen geben, welcher unvergesslich in die Geschichtsbücher eingehen wird.',
        'task' => [
            'id' => 0,
            'type' => 'rename-planet',
            'operator' => 'equal-to-or-more',
            'description' => 'Gib dem Planeten einen Namen',
            'value' => 1,
        ],
        'rewards' => [
            [
                'type' => 'defense',
                'value' => 20,
                'defense_id' => 9,
            ]
        ],
    ],
    6 => [
        'id' => 6,
        'title' => 'Kontakte Knüpfen',
        'description' => 'Eure Exzellenz, wir haben so viele Nachbarn, aber zu keinem wirklich Kontakt. Wenn ihr einen unseren Nachbarn kontaktieren und auf unsere Seite bringen können, dann wird es einfacher für uns sein unsere Ziele zu erreichen. Selbstverständlich können wir diesen Nachbarn am Ende immer noch hintergehen.',
        'task' => [
            'id' => 0,
            'type' => 'send-message',
            'operator' => 'equal-to-or-more',
            'description' => 'Sende 1 Nachricht',
            'value' => 1,
        ],
        'rewards' => [
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 13,
            ]
        ],
    ],
    7 => [
        'id' => 7,
        'title' => 'Frische Luft schnappen',
        'description' => 'Eure Exzellenz, unsere Piloten langweilen sich! Vielleicht sollten wir einen kleinen Überraschungsangriff starten? Je grösser die Beute dabei, desto besser.',
        'task' => [
            'id' => 0,
            'type' => 'launch-fleet',
            'operator' => 'equal-to-or-more',
            'description' => 'Starte eine Flotte',
            'value' => 1,
        ],
        'rewards' => [
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 12,
            ]
        ],
    ],
    8 => [
        'id' => 8,
        'title' => 'Fortschritt duch Technik',
        'description' => 'Eure Exzellenz, Investment in die Forschung wird uns technische Überlegenheit garantieren und die Bevölkerung wird sogar noch mehr bewundern.',
        'task' => [
            'id' => 0,
            'type' => 'have-building-level',
            'operator' => 'equal-to-or-more',
            'description' => 'Bau das Forschungslabor',
            'value' => 1,
            'attributes' => [
                'building_id' => 8,
            ],
        ],
        'rewards' => [
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 12,
            ]
        ],
    ],
    9 => [
        'id' => 9,
        'title' => 'Fortschritt duch Technik (2)',
        'description' => 'Eure Exzellenz, Investment in die Forschung wird uns technische Überlegenheit garantieren und die Bevölkerung wird sogar noch mehr bewundern.',
        'task' => [
            'id' => 0,
            'type' => 'have-technology-level',
            'operator' => 'equal-to-or-more',
            'description' => 'Forsche Energietechnik bis Level 5',
            'value' => 5,
            'attributes' => [
                'technology_id' => 3,
            ],
        ],
        'trigger' => [
            'id' => 0,
            'type' => 'have-technology-level',
            'operator' => 'less-than',
            'value' => 5,
            'attributes' => [
                'technology_id' => 3,
            ],
        ],
        'rewards' => [
            [
                'type' => 'ship',
                'value' => 1,
                'ship_id' => 12,
            ]
        ],
    ],
    10 => [
        'id' => 10,
        'title' => '',
        'description' => 'Eure Exzellenz, unsere Nachbarn haben unseren Reichtum entdeckt und gesehen, dass wir nicht wirklich Verteidigungsanlagen haben, um diesen zu beschützen',
        'task' => [
            'id' => 0,
            'type' => 'have-defense',
            'operator' => 'equal-to-or-more',
            'description' => 'Baue NEKKAR Plasmawerfer bis du insgesamt 10 hast.',
            'value' => 10,
            'attributes' => [
                'defense_id' => 7,
            ],
        ],
        'trigger' => [
            'id' => 0,
            'type' => 'have-defense',
            'operator' => 'less-than',
            'value' => 10,
            'attributes' => [
                'defense_id' => 7,
            ],
        ],
        'rewards' => [
            [
                'type' => 'defense',
                'value' => 1,
                'defense_id' => 7,
            ]
        ],
    ],
    11 => [
        'id' => 11,
        'title' => '',
        'description' => 'Eure Exzellenz, ein Upgrade für unser Mysticum ist bereit. Mit dem Upgrade hat unsere Flotte erhebliche Vorteile.',
        'task' => [
            'id' => 0,
            'type' => 'upgrade-ship',
            'operator' => 'equal-to-or-more',
            'description' => 'Führe ein Mysticum upgrade aus',
            'value' => 1,
            'attributes' => [
                'defense_id' => 7,
            ],
        ],
        'rewards' => [
            [
                'type' => 'defense',
                'value' => 1,
                'defense_id' => 7,
            ]
        ],
    ],
    12 => [
        'id' => 12,
        'title' => '',
        'description' => 'Eure Exzellenz, die Galaxy ist gross und bietet  viele Überraschungen. Wir sollten alles erkunden!',
        'task' => [
            'id' => 0,
            'type' => 'have-galaxy-discovered',
            'operator' => 'equal-to-or-more',
            'description' => 'Erkunde die ganze Galaxy.',
            'value' => 1,
        ],
        'rewards' => [
            [
                'type' => 'defense',
                'value' => 1,
                'defense_id' => 7,
            ]
        ],
    ],
    13 => [
        'id' => 13,
        'title' => 'Für noch mehr Ruhm',
        'description' => 'Eure Exzellenz, wir mussten feststellen, dass fremde Imperien nicht von unseren Heldentaten berichten, weil sie nicht wissen, wie man unser Sonnensystem nennt. Wir müssen dem System einen Namen geben, welcher unvergesslich in die Geschichtsbücher eingehen wird.',
        'task' => [
            'id' => 0,
            'type' => 'rename-star',
            'operator' => 'equal-to-or-more',
            'description' => 'Gib dem Sonnensystem einen Namen',
            'value' => 1,
        ],
        'rewards' => [
            [
                'type' => 'defense',
                'value' => 20,
                'defense_id' => 9,
            ]
        ],
    ],
    14 => [
        'id' => 14,
        'title' => 'Punktesammler',
        'description' => 'Eure Exzellenz, Punkte müssen her! Viele! Los geht\'s!',
        'task' => [
            'id' => 0,
            'type' => 'have-points',
            'operator' => 'equal-to-or-more',
            'description' => 'Hab mindestens 200 Punkte',
            'value' => 200,
        ],
        'trigger' => [
            'id' => 0,
            'type' => 'have-points',
            'operator' => 'less-than',
            'value' => 150,
        ],
        'rewards' => [
            [
                'type' => 'defense',
                'value' => 20,
                'defense_id' => 9,
            ]
        ],
    ],
];
