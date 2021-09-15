<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\SymfonyWebTestCase;

class TutorialControllerTest extends SymfonyWebTestCase
{
    public function testClose(): void
    {
        $userId = 1;
        $tutorialId = 1;

        $client = self::createClient();

        $this->loginUser($userId);

        $client->request('PUT', sprintf('/api/tutorials/%s/close', $tutorialId));

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
