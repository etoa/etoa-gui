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

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testDebris(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/debris');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testAttackBan(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/attack-ban');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testFleets(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/fleets');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testGame(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/game');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testError(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/logs/error');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
