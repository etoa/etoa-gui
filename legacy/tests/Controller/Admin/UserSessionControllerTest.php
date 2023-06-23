<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class UserSessionControllerTest extends SymfonyWebTestCase
{
    public function testSessions(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/sessions');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testSessionLog(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/session-log');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
