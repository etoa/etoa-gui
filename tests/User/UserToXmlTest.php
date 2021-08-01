<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\WebTestCase;

class UserToXmlTest extends WebTestCase
{
    private UserToXml $userToXml;
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userToXml = $this->app[UserToXml::class];
        $this->service = $this->app[UserService::class];
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
