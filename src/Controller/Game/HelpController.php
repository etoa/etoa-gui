<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyListItem;
use EtoA\Support\ExternalUrl;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Technology\TechnologyService;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractGameController
{
    public function __construct()
    {
    }

    #[Route('/game/help/overview', name: 'game.help.overview')]
    public function list(): Response
    {
        // Internal pages
        $links = [
            [
                'label' => 'Supportticket eröffnen',
                'url' => 'game.ticket.list'
            ],
            [
                'label' => 'Admin via Mail kontaktieren',
                'url' => 'game.contact.list'
            ],
            [
                'label' => 'Changelog',
                'url' => 'game.changelog'
            ],
            [
                'label' => 'Credits',
                'url' => 'game.credits'
            ]
        ];

        // External resources
        $externalLinks = [
            [
                'label' => 'Häufig gestellte Fragen',
                'onclick' => ExternalUrl::HELP_CENTER_ON_CLICK,
            ],
            [
                'label' => 'Regeln',
                'onclick' => ExternalUrl::RULES_ON_CLICK,
            ],
            [
                'label' => 'Forum',
                'url' => ExternalUrl::FORUM,
            ],
            [
                'label' => 'Fehler melden',
                'url' => ExternalUrl::DEV_CENTER,
            ]
        ];

        $helpNav = [
            "Datenbank" => [
                "Rassen" => array('races', 'Liste aller Rassen'),
                "Planeten" => array('planets', 'Liste aller Planeten'),
                "Sterne" => array('stars', 'Liste aller Sterne'),
                "Gebäude" => array('buildings', 'Liste aller Geb&auml;ude'),
                "Technologien" => array('research', 'Liste aller Technologien'),
                "Schiffe" => array('shipyard', 'Liste aller Schiffe'),
                "Raketen" => array('missiles', 'Liste aller Raketen'),
                "Verteidigung" => array('defense', 'Liste aller Verteidigungsanlagen'),
                "Spezialisten" => array('specialists', 'Was man mit Spezialisten machen kann'),
            ],
            "Spielmechanismen" => [
                "Einstellungen" => array('settings', 'Grundlegende Einstellungen dieser Runde'),
                "Ressourcen" => array('resources', 'Liste aller Ressourcen'),
                "Markt" => array('market', 'Wie der Marktplatz funktioniert?'),
                "Rohstoffkurse" => array('rates', 'Welche Werte die Rohstoffe akuell haben'),
                "Bewohner" => array('population', 'Wie arbeite ich mit Bewohnern und was muss ich beachten?'),
                "Energie" => array('power', 'Alles über die Energieproduktion'),
                "Schiffsaktionen" => array('action', 'Die verschiedenen Aktionen in der &Uuml;bersicht'),
                "Kryptocenter" => array('crypto', 'Wie man fremde Flottenbewegungen scannt?'),
                "Multis und Sitting" => array('multi_sitting', 'Wie wir Mehrfachaccounts handhaben und wie Sitting funktioniert?'),
                "Raketen" => array('missile_system', 'Wie das Raketensystem funktioniert?'),
                "Raumkarte" => array('space', 'Wie ist das Universum aufgebaut?'),
                "Spezialpunkte" => array('specialpoints', 'Wie man Spezialpunkte und Titel erwerben kann?'),
                "Spionage" => array('spy_info', 'Wie das Spionagesystem funktioniert?'),
                "Statistik" => array('stats', 'Was sind Statistiken und wie werden sie berechnet?'),
                "Technikbaum" => array('techtree', 'Wie lese ich daraus die Voraussetzungen ab?'),
                "Textformatierung" => array('textformat', 'Wie man Text formatieren kann (BBcode)?'),
                "Urlaubsmodus" => array('u_mod', 'Was das ist und wie es funktioniert?'),
                "Wärme- und Kältebonus" => array('tempbonus', 'Welche Auswirkungen hat die Planetentemperatur?')
            ]
        ];

        return $this->render('game/help/overview.html.twig', [
            'links' => $links,
            'externalLinks' =>$externalLinks,
            'helpNav' => $helpNav
        ]);
    }
}