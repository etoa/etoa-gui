<?php

declare(strict_types=1);

namespace EtoA\Universe;

use DirectoryIterator;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DatabaseManagerRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Mutex;
use UserToXml;

class UniverseResetService
{
    private ConfigurationService $config;
    private UserRepository $userRepo;
    private PlanetRepository $planetRepo;
    private DatabaseManagerRepository $databaseManager;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepo,
        PlanetRepository $planetRepo,
        DatabaseManagerRepository $databaseManager
    ) {
        $this->config = $config;
        $this->userRepo = $userRepo;
        $this->planetRepo = $planetRepo;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Resets the universe and all user data
     * The Anti-Big-Bang
     */
    public function reset(bool $all = true): void
    {
        $mtx = new Mutex();
        $mtx->acquire();

        $tbl = [];
        $tbl[] = "cells";
        $tbl[] = "entities";
        $tbl[] = "stars";
        $tbl[] = "planets";
        $tbl[] = "asteroids";
        $tbl[] = "nebulas";
        $tbl[] = "wormholes";
        $tbl[] = "space";

        $planetsWithUserCount = $this->planetRepo->countWithUser();
        if ($planetsWithUserCount > 0) {
            $tbl[] = "buildlist";
            $tbl[] = "deflist";
            $tbl[] = "def_queue";
            $tbl[] = "fleet";
            $tbl[] = "fleet_ships";
            $tbl[] = "market_auction";
            $tbl[] = "market_ship";
            $tbl[] = "market_ressource";
            $tbl[] = "missilelist";
            $tbl[] = "missile_flights";
            $tbl[] = "missile_flights_obj";
            $tbl[] = "shiplist";
            $tbl[] = "ship_queue";
            $tbl[] = "techlist";
        }

        if ($all) {
            $tbl[] = "alliances";
            $tbl[] = "alliance_bnd";
            $tbl[] = "alliance_applications";
            $tbl[] = "alliance_history";
            $tbl[] = "alliance_news";
            $tbl[] = "alliance_ranks";
            $tbl[] = "alliance_poll_votes";
            $tbl[] = "alliance_rankrights";
            $tbl[] = "allianceboard_cat";
            $tbl[] = "allianceboard_posts";
            $tbl[] = "allianceboard_catranks";
            $tbl[] = "allianceboard_topics";
            $tbl[] = "alliance_stats";
            $tbl[] = "alliance_polls";
            $tbl[] = "alliance_points";
            $tbl[] = "alliance_buildlist";
            $tbl[] = "alliance_spends";
            $tbl[] = "alliance_techlist";

            $tbl[] = "users";
            $tbl[] = "user_multi";
            $tbl[] = "user_log";
            $tbl[] = "user_sessionlog";
            $tbl[] = "user_points";
            $tbl[] = "user_sitting";
            $tbl[] = "user_stats";
            $tbl[] = "user_ratings";
            $tbl[] = "user_onlinestats";
            $tbl[] = "user_comments";
            $tbl[] = "user_warnings";
            $tbl[] = "user_properties";
            $tbl[] = "user_sessions";
            $tbl[] = "user_surveillance";

            $tbl[] = "buddylist";
            $tbl[] = "messages";
            $tbl[] = "message_data";
            $tbl[] = "message_ignore";
            $tbl[] = "notepad";
            $tbl[] = "notepad_data";
            $tbl[] = "bookmarks";
            $tbl[] = "fleet_bookmarks";
            $tbl[] = "chat_log";
            $tbl[] = "reports";
            $tbl[] = "reports_other";
            $tbl[] = "reports_battle";
            $tbl[] = "reports_spy";
            $tbl[] = "reports_market";

            $tbl[] = "logs";
            $tbl[] = "logs_alliance";
            $tbl[] = "logs_battle";
            $tbl[] = "logs_fleet";
            $tbl[] = "logs_game";

            $tbl[] = "login_failures";
            $tbl[] = "admin_user_log";
            $tbl[] = "admin_user_sessionlog";
            $tbl[] = "tickets";
            $tbl[] = "ticket_msg";
            $tbl[] = "chat";
            $tbl[] = "chat_users";
            $tbl[] = "hostname_cache";
            $tbl[] = "backend_message_queue";
        } else {
            $this->userRepo->resetDiscoveryMask();
        }

        $this->databaseManager->truncateTables($tbl);

        $this->config->set('market_metal_factor', 1);
        $this->config->set('market_crystal_factor', 1);
        $this->config->set('market_plastic_factor', 1);
        $this->config->set('market_fuel_factor', 1);
        $this->config->set('market_food_factor', 1);

        // Remove user XML backups
        $userXmlPath = UserToXml::getDataDirectory();
        foreach (new DirectoryIterator($userXmlPath) as $fileInfo) {
            if (!$fileInfo->isDot()) {
                unlink($fileInfo->getPathname());
            }
        }

        $mtx->release();
    }
}
