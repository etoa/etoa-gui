<?php declare(strict_types=1);

namespace EtoA\Tutorial;

use EtoA\WebTestCase;

class TutorialControllerTest extends WebTestCase
{
    public function testClose(): void
    {
        $userId = 1;
        $tutorialId = 1;

        $this->loginUser($userId);

        $client = $this->createClient();
        $client->request('PUT', sprintf('/api/tutorials/%s/close', $tutorialId));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
