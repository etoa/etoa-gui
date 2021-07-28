<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\WebTestCase;

class UserServiceTest extends WebTestCase
{
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[UserService::class];
    }

    public function testRegister(): void
    {
        // given
        $name = 'John Doe';
        $nick = 'JohnDoe';
        $email = 'johndoe@example.com';
        $password = '12345678';

        // when
        $user = $this->service->register($name, $email, $nick, $password);

        // then
        $this->assertEquals($name, $user->name);
        $this->assertEquals($nick, $user->nick);
        $this->assertEquals($email, $user->email);
    }
}
