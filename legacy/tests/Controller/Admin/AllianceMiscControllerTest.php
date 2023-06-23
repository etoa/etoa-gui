<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class AllianceMiscControllerTest extends SymfonyWebTestCase
{
    public function testNew(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/alliances/new');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testCrap(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/alliances/crap');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testImagecheck(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/alliances/imagecheck');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
