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

    public function testBanners(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/banners');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testImageCheck(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/imagecheck');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testMultis(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/multis');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testIps(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/ips');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testIpsSearch(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/ips?ip=129.0.0.1');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
