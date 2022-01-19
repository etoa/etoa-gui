<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Support\Mail\MailSenderService;
use EtoA\WebTestCase;
use Swift_Mailer;

class UserServiceTest extends WebTestCase
{
    private UserService $service;
    private UserRepository $repository;
    private MailSenderService $mailSenderService;
    private Swift_Mailer $mailer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[UserService::class];
        $this->repository = $this->app[UserRepository::class];
        $this->mailSenderService = $this->app[MailSenderService::class];

        $this->mailer = $this->createMock(\Swift_Mailer::class);
        $this->mailSenderService->setMailer($this->mailer);

        $this->mailer
            ->method('send')
            ->willReturn(1);
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
        $this->assertNotNull($this->repository->getUser($user->id));
        $this->assertEquals($name, $user->name);
        $this->assertEquals($nick, $user->nick);
        $this->assertEquals($email, $user->email);
    }

    public function testSetPassword(): void
    {
        // given
        $name = 'John Doe';
        $nick = 'JohnDoe';
        $email = 'johndoe@example.com';
        $password = '12345678';
        $newPassword = '87654321';
        $user = $this->service->register($name, $email, $nick, $password);
        $this->assertTrue(validatePasswort($password, $user->password));
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        // when
        $this->service->setPassword($user->id, $password, $newPassword, $newPassword);

        // then
        $user = $this->repository->getUser($user->id);
        $this->assertTrue(validatePasswort($newPassword, $user->password));
    }

    public function testDelete(): void
    {
        // given
        $name = 'John Doe';
        $nick = 'JohnDoe';
        $email = 'johndoe@example.com';
        $password = '12345678';
        $user = $this->service->register($name, $email, $nick, $password);

        // when
        $this->service->delete($user->id);

        // then
        $this->assertNull($this->repository->getUser($user->id));
    }

    public function testDeleteRequest(): void
    {
        // given
        $name = 'John Doe';
        $nick = 'JohnDoe';
        $email = 'johndoe@example.com';
        $password = '12345678';
        $user = $this->service->register($name, $email, $nick, $password);

        // when
        $result = $this->service->deleteRequest($user->id, $password);

        // then
        $this->assertTrue($result);
        $user = $this->repository->getUser($user->id);
        $this->assertGreaterThan(time(), $user->deleted);
    }
}
