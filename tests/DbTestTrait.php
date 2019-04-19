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
        $this->connection->query('TRUNCATE planets');
        $this->connection->query('TRUNCATE techlist');
        $this->connection->query('TRUNCATE buildlist');
        $this->connection->query('TRUNCATE shiplist');
        $this->connection->query('TRUNCATE deflist');
        $this->connection->query('TRUNCATE missilelist');
        $this->connection->query('TRUNCATE quest_tasks');
        $this->connection->query('TRUNCATE quest_log');
        $this->connection->query('TRUNCATE tutorial_user_progress');
        $this->connection->query('TRUNCATE user_sessions');
        $this->connection->query('TRUNCATE users');
        $this->connection->query('DELETE FROM quests');
    }
}
