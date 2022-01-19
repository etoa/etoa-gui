<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class UniverseControllerTest extends SymfonyWebTestCase
{
    public function testBigBangConfigure(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/universe/big-bang/configure');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testBigBang(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/universe/big-bang');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testCheck(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/universe/check');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testResetRound(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/universe/reset/full');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testResetUniverse(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/universe/reset/universe');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
