<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\SymfonyWebTestCase;

class UserToXmlTest extends SymfonyWebTestCase
{
    private UserToXml $userToXml;
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userToXml = self::getContainer()->get(UserToXml::class);
        $this->service = self::getContainer()->get(UserService::class);
    }

    public function testGenerate(): void
    {
        // given
        $name = 'John Doe';
        $nick = 'JohnDoe';
        $email = 'johndoe@example.com';
        $password = '12345678';
        $user = $this->service->register($name, $email, $nick, $password);

        // when
        $result = $this->userToXml->generate($user->id);

        // then
        $this->assertStringContainsString("<nick>$nick</nick>", $result);
        $this->assertStringContainsString("<email>$email</email>", $result);
    }
}
