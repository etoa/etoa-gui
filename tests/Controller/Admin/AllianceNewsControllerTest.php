<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class AllianceNewsControllerTest extends SymfonyWebTestCase
{
    public function testNews(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/alliances/news');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
