<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class AdminProfileControllerTest extends SymfonyWebTestCase
{
    public function testOverview(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/profile/');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
