<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class ConfigControllerTest extends SymfonyWebTestCase
{
    public function testCommon(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/config/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testEditor(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/config/editor');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testCheck(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/config/check');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
