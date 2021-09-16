<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\SymfonyWebTestCase;

class ChatControllerTest extends SymfonyWebTestCase
{
    public function testUsers(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/chat/users');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testLogout(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/chat/logout');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
