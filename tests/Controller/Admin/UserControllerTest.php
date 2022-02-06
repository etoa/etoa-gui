<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class UserControllerTest extends SymfonyWebTestCase
{
    public function testNew(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/users/new');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
