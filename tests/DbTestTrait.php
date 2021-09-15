<?php declare(strict_types=1);

namespace EtoA;

use Silex\Application;

trait DbTestTrait
{
    public function setupApplication(): Application
    {
        $environment = 'testing';
        $debug = true;

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        return require dirname(__DIR__).'/src/app.php';
    }

    protected function tearDown(): void
    {
        self::$staticConnection->executeQuery('TRUNCATE alliances');
        self::$staticConnection->executeQuery('TRUNCATE alliance_applications');
        self::$staticConnection->executeQuery('TRUNCATE alliance_spends');
        self::$staticConnection->executeQuery('TRUNCATE alliance_news');
        self::$staticConnection->executeQuery('TRUNCATE alliance_polls');
        self::$staticConnection->executeQuery('TRUNCATE alliance_ranks');
        self::$staticConnection->executeQuery('TRUNCATE alliance_bnd');
        self::$staticConnection->executeQuery('TRUNCATE alliance_rankrights');
        self::$staticConnection->executeQuery('TRUNCATE allianceboard_cat');
        self::$staticConnection->executeQuery('TRUNCATE allianceboard_catranks');
        self::$staticConnection->executeQuery('TRUNCATE allianceboard_topics');
        self::$staticConnection->executeQuery('TRUNCATE allianceboard_posts');
        self::$staticConnection->executeQuery('TRUNCATE alliance_buildlist');
        self::$staticConnection->executeQuery('TRUNCATE bookmarks');
        self::$staticConnection->executeQuery('TRUNCATE buddylist');
        self::$staticConnection->executeQuery('TRUNCATE chat');
        self::$staticConnection->executeQuery('TRUNCATE chat_banns');
        self::$staticConnection->executeQuery('TRUNCATE chat_users');
        self::$staticConnection->executeQuery('TRUNCATE default_items');
        self::$staticConnection->executeQuery('TRUNCATE default_item_sets');
        self::$staticConnection->executeQuery('TRUNCATE fleet');
        self::$staticConnection->executeQuery('TRUNCATE fleet_ships');
        self::$staticConnection->executeQuery('TRUNCATE fleet_bookmarks');
        self::$staticConnection->executeQuery('TRUNCATE login_failures');
        self::$staticConnection->executeQuery('TRUNCATE planets');
        self::$staticConnection->executeQuery('TRUNCATE techlist');
        self::$staticConnection->executeQuery('TRUNCATE buildlist');
        self::$staticConnection->executeQuery('TRUNCATE shiplist');
        self::$staticConnection->executeQuery('TRUNCATE deflist');
        self::$staticConnection->executeQuery('TRUNCATE def_queue');
        self::$staticConnection->executeQuery('TRUNCATE missilelist');
        self::$staticConnection->executeQuery('TRUNCATE missile_flights');
        self::$staticConnection->executeQuery('TRUNCATE quest_tasks');
        self::$staticConnection->executeQuery('TRUNCATE quest_log');
        self::$staticConnection->executeQuery('TRUNCATE tutorial_user_progress');
        self::$staticConnection->executeQuery('TRUNCATE user_sessions');
        self::$staticConnection->executeQuery('TRUNCATE users');
        self::$staticConnection->executeQuery('TRUNCATE user_stats');
        self::$staticConnection->executeQuery('TRUNCATE user_comments');
        self::$staticConnection->executeQuery('TRUNCATE user_sitting');
        self::$staticConnection->executeQuery('TRUNCATE user_warnings');
        self::$staticConnection->executeQuery('TRUNCATE user_surveillance');
        self::$staticConnection->executeQuery('TRUNCATE tech_points');
        self::$staticConnection->executeQuery('TRUNCATE building_points');
        self::$staticConnection->executeQuery('TRUNCATE tickets');
        self::$staticConnection->executeQuery('TRUNCATE ticket_msg');
        self::$staticConnection->executeQuery('TRUNCATE messages');
        self::$staticConnection->executeQuery('TRUNCATE message_data');
        self::$staticConnection->executeQuery('DELETE FROM quests');
        self::$staticConnection->executeQuery('DELETE FROM market_rates');
        self::$staticConnection->executeQuery('DELETE FROM market_auction');
        self::$staticConnection->executeQuery('DELETE FROM market_ressource');
        self::$staticConnection->executeQuery('DELETE FROM market_ship');

        parent::tearDown();
    }
}
