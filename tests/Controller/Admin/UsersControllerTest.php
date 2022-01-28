<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class UsersControllerTest extends SymfonyWebTestCase
{
    public function testList(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testSitting(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/sitting');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testLoginFailures(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/login-failures');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testPoints(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/points');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
