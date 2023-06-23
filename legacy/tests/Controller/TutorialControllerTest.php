<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\SymfonyWebTestCase;

class TutorialControllerTest extends SymfonyWebTestCase
{
    public function testShow(): void
    {
        $userId = 1;
        $tutorialId = 1;

        $client = self::createClient();

        $this->loginUser($userId);

        $client->request('GET', sprintf('/api/tutorials/%s', $tutorialId));

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testClose(): void
    {
        $userId = 1;
        $tutorialId = 1;

        $client = self::createClient();

        $this->loginUser($userId);

        $client->request('PUT', sprintf('/api/tutorials/%s/close', $tutorialId));

        $this->assertStatusCode(200, $client->getResponse());
    }
}
