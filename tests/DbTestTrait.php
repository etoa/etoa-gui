<?php declare(strict_types=1);

namespace EtoA;

use Silex\Application;

trait DbTestTrait
{
    public function setupApplication(): Application
    {
        $environment = 'testing';
        $debug = true;

        return require dirname(__DIR__).'/src/app.php';
    }

    protected function tearDown(): void
    {
        $this->connection->executeQuery('TRUNCATE alliances');
        $this->connection->executeQuery('TRUNCATE alliance_applications');
        $this->connection->executeQuery('TRUNCATE alliance_spends');
        $this->connection->executeQuery('TRUNCATE alliance_news');
        $this->connection->executeQuery('TRUNCATE alliance_polls');
        $this->connection->executeQuery('TRUNCATE alliance_ranks');
        $this->connection->executeQuery('TRUNCATE alliance_rankrights');
        $this->connection->executeQuery('TRUNCATE allianceboard_cat');
        $this->connection->executeQuery('TRUNCATE allianceboard_catranks');
        $this->connection->executeQuery('TRUNCATE allianceboard_topics');
        $this->connection->executeQuery('TRUNCATE allianceboard_posts');
        $this->connection->executeQuery('TRUNCATE bookmarks');
        $this->connection->executeQuery('TRUNCATE buddylist');
        $this->connection->executeQuery('TRUNCATE chat');
        $this->connection->executeQuery('TRUNCATE chat_banns');
        $this->connection->executeQuery('TRUNCATE chat_users');
        $this->connection->executeQuery('TRUNCATE default_items');
        $this->connection->executeQuery('TRUNCATE default_item_sets');
        $this->connection->executeQuery('TRUNCATE fleet');
        $this->connection->executeQuery('TRUNCATE fleet_ships');
        $this->connection->executeQuery('TRUNCATE fleet_bookmarks');
        $this->connection->executeQuery('TRUNCATE login_failures');
        $this->connection->executeQuery('TRUNCATE planets');
        $this->connection->executeQuery('TRUNCATE techlist');
        $this->connection->executeQuery('TRUNCATE buildlist');
        $this->connection->executeQuery('TRUNCATE shiplist');
        $this->connection->executeQuery('TRUNCATE deflist');
        $this->connection->executeQuery('TRUNCATE def_queue');
        $this->connection->executeQuery('TRUNCATE missilelist');
        $this->connection->executeQuery('TRUNCATE missile_flights');
        $this->connection->executeQuery('TRUNCATE quest_tasks');
        $this->connection->executeQuery('TRUNCATE quest_log');
        $this->connection->executeQuery('TRUNCATE tutorial_user_progress');
        $this->connection->executeQuery('TRUNCATE user_sessions');
        $this->connection->executeQuery('TRUNCATE users');
        $this->connection->executeQuery('TRUNCATE user_comments');
        $this->connection->executeQuery('TRUNCATE user_sitting');
        $this->connection->executeQuery('TRUNCATE user_warnings');
        $this->connection->executeQuery('TRUNCATE user_surveillance');
        $this->connection->executeQuery('TRUNCATE tech_points');
        $this->connection->executeQuery('TRUNCATE building_points');
        $this->connection->executeQuery('TRUNCATE tickets');
        $this->connection->executeQuery('TRUNCATE ticket_msg');
        $this->connection->executeQuery('TRUNCATE messages');
        $this->connection->executeQuery('TRUNCATE message_data');
        $this->connection->executeQuery('DELETE FROM quests');
        $this->connection->executeQuery('DELETE FROM market_rates');
        $this->connection->executeQuery('DELETE FROM market_auction');
        $this->connection->executeQuery('DELETE FROM market_ressource');
        $this->connection->executeQuery('DELETE FROM market_ship');
    }
}
