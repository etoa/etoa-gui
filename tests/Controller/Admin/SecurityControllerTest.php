<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class SecurityControllerTest extends SymfonyWebTestCase
{
    public function testSetupFirstUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/login/setup');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testReset(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/login/reset');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
