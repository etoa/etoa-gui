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
            'operator' => 'equal-to',
            'description' => 'Entlasse den Verräter',
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
            'operator' => 'equal-to',
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
            'operator' => 'equal-to',
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
];
