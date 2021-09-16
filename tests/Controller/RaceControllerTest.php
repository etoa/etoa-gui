<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\SymfonyWebTestCase;

class RaceControllerTest extends SymfonyWebTestCase
{
    public function testInfo(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/races/info?id=1');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
