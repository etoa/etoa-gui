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

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testPoll(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/chat/poll');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testPush(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/chat/poll?ctext=test');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testLogout(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/chat/logout');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
