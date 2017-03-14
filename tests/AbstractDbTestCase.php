<?php

namespace EtoA;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

class AbstractDbTestCase extends TestCase
{
    /** @var Container */
    protected $app;
    /** @var Connection */
    protected $connection;

    protected function setUp()
    {
        parent::setUp();

        $this->app = require dirname(__DIR__).'/src/app.php';
        $this->connection = $this->app['db'];
    }

    protected function tearDown()
    {
        $this->connection->query('TRUNCATE planets');
        $this->connection->query('TRUNCATE shiplist');
        $this->connection->query('TRUNCATE deflist');
        $this->connection->query('TRUNCATE missilelist');
        $this->connection->query('TRUNCATE quest_tasks');
        $this->connection->query('DELETE FROM quests');
    }
}
