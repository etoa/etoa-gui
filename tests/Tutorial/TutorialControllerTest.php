<?php

namespace EtoA\Tutorial;

use EtoA\WebTestCase;

class TutorialControllerTest extends WebTestCase
{
    public function testClose()
    {
        $userId = 1;
        $tutorialId = 1;

        $this->loginUser($userId);

        $client = self::createClient();
        $client->request('PUT', sprintf('/api/tutorials/%s/close', $tutorialId));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
