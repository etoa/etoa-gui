<?php declare(strict_types=1);

namespace EtoA;

use Silex\Application;

trait DbTestTrait
{
    public function setupApplication(): Application
    {
        $environment = 'testing';
        $debug = true;
        $questSystemEnabled = true;

        return require dirname(__DIR__).'/src/app.php';
    }

    protected function tearDown(): void
    {
        $this->connection->executeQuery('TRUNCATE planets');
        $this->connection->executeQuery('TRUNCATE techlist');
        $this->connection->executeQuery('TRUNCATE buildlist');
        $this->connection->executeQuery('TRUNCATE shiplist');
        $this->connection->executeQuery('TRUNCATE deflist');
        $this->connection->executeQuery('TRUNCATE missilelist');
        $this->connection->executeQuery('TRUNCATE quest_tasks');
        $this->connection->executeQuery('TRUNCATE quest_log');
        $this->connection->executeQuery('TRUNCATE tutorial_user_progress');
        $this->connection->executeQuery('TRUNCATE user_sessions');
        $this->connection->executeQuery('TRUNCATE users');
        $this->connection->executeQuery('TRUNCATE tickets');
        $this->connection->executeQuery('TRUNCATE ticket_msg');
        $this->connection->executeQuery('TRUNCATE messages');
        $this->connection->executeQuery('TRUNCATE message_data');
        $this->connection->executeQuery('DELETE FROM quests');
    }
}
