<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class LogControllerTest extends SymfonyWebTestCase
{
    public function testGeneral(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testDebris(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/debris');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testAttackBan(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/attack-ban');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testFleets(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/fleets');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testGame(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/game');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testError(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/error');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
