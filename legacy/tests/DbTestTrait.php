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
        $this->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $this->getConnection()->executeQuery('TRUNCATE admin_users');
        $this->getConnection()->executeQuery('TRUNCATE admin_user_sessions');
        $this->getConnection()->executeQuery('TRUNCATE alliances');
        $this->getConnection()->executeQuery('TRUNCATE alliance_applications');
        $this->getConnection()->executeQuery('TRUNCATE alliance_spends');
        $this->getConnection()->executeQuery('TRUNCATE alliance_news');
        $this->getConnection()->executeQuery('TRUNCATE alliance_polls');
        $this->getConnection()->executeQuery('TRUNCATE alliance_ranks');
        $this->getConnection()->executeQuery('TRUNCATE alliance_bnd');
        $this->getConnection()->executeQuery('TRUNCATE alliance_rankrights');
        $this->getConnection()->executeQuery('TRUNCATE allianceboard_cat');
        $this->getConnection()->executeQuery('TRUNCATE allianceboard_catranks');
        $this->getConnection()->executeQuery('TRUNCATE allianceboard_topics');
        $this->getConnection()->executeQuery('TRUNCATE allianceboard_posts');
        $this->getConnection()->executeQuery('TRUNCATE alliance_buildlist');
        $this->getConnection()->executeQuery('TRUNCATE bookmarks');
        $this->getConnection()->executeQuery('TRUNCATE buddylist');
        $this->getConnection()->executeQuery('TRUNCATE chat');
        $this->getConnection()->executeQuery('TRUNCATE chat_banns');
        $this->getConnection()->executeQuery('TRUNCATE chat_users');
        $this->getConnection()->executeQuery('TRUNCATE default_items');
        $this->getConnection()->executeQuery('TRUNCATE default_item_sets');
        $this->getConnection()->executeQuery('TRUNCATE fleet');
        $this->getConnection()->executeQuery('TRUNCATE fleet_ships');
        $this->getConnection()->executeQuery('TRUNCATE fleet_bookmarks');
        $this->getConnection()->executeQuery('TRUNCATE login_failures');
        $this->getConnection()->executeQuery('TRUNCATE logs_game');
        $this->getConnection()->executeQuery('TRUNCATE planets');
        $this->getConnection()->executeQuery('TRUNCATE cells');
        $this->getConnection()->executeQuery('TRUNCATE entities');
        $this->getConnection()->executeQuery('TRUNCATE techlist');
        $this->getConnection()->executeQuery('TRUNCATE buildlist');
        $this->getConnection()->executeQuery('TRUNCATE shiplist');
        $this->getConnection()->executeQuery('TRUNCATE deflist');
        $this->getConnection()->executeQuery('TRUNCATE def_queue');
        $this->getConnection()->executeQuery('TRUNCATE missilelist');
        $this->getConnection()->executeQuery('TRUNCATE missile_flights');
        $this->getConnection()->executeQuery('TRUNCATE quest_tasks');
        $this->getConnection()->executeQuery('TRUNCATE quest_log');
        $this->getConnection()->executeQuery('TRUNCATE tutorial_user_progress');
        $this->getConnection()->executeQuery('TRUNCATE user_sessions');
        $this->getConnection()->executeQuery('TRUNCATE users');
        $this->getConnection()->executeQuery('TRUNCATE user_stats');
        $this->getConnection()->executeQuery('TRUNCATE user_comments');
        $this->getConnection()->executeQuery('TRUNCATE user_sitting');
        $this->getConnection()->executeQuery('TRUNCATE user_warnings');
        $this->getConnection()->executeQuery('TRUNCATE user_surveillance');
        $this->getConnection()->executeQuery('TRUNCATE tech_points');
        $this->getConnection()->executeQuery('TRUNCATE building_points');
        $this->getConnection()->executeQuery('TRUNCATE tickets');
        $this->getConnection()->executeQuery('TRUNCATE ticket_msg');
        $this->getConnection()->executeQuery('TRUNCATE messages');
        $this->getConnection()->executeQuery('TRUNCATE message_data');
        $this->getConnection()->executeQuery('DELETE FROM quests');
        $this->getConnection()->executeQuery('DELETE FROM market_rates');
        $this->getConnection()->executeQuery('DELETE FROM market_auction');
        $this->getConnection()->executeQuery('DELETE FROM market_ressource');
        $this->getConnection()->executeQuery('DELETE FROM market_ship');
        $this->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        parent::tearDown();
    }
}
