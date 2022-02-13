<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class UserWarningControllerTest extends SymfonyWebTestCase
{
    public function testList(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/warnings');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
