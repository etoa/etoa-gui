<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class GalaxyMapControllerTest extends SymfonyWebTestCase
{
    public function testView(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/universe/map');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
