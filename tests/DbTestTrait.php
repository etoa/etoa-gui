<?php

namespace EtoA;

trait DbTestTrait
{
    public function setupApplication()
    {
        $environment = 'testing';
        $debug = true;

        return require dirname(__DIR__).'/src/app.php';
    }

    protected function tearDown()
    {
        $this->connection->query('TRUNCATE planets');
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
