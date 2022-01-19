<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class ChatControllerTest extends SymfonyWebTestCase
{
    public function testView(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/chat/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testLog(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/chat/log');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
