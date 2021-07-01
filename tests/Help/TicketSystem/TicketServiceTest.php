<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\AbstractDbTestCase;
use EtoA\Message\MessageRepository;

// TODO define at a more suitable place, or find a way to make these definitions obsolete
if (!defined('RELATIVE_ROOT')) {
    define('RELATIVE_ROOT', '');
}
require_once __DIR__ . '/../../../htdocs/inc/const.inc.php';
require_once __DIR__ . '/../../../htdocs/inc/functions.inc.php';

class TicketServiceTest extends AbstractDbTestCase
{
    private TicketRepository $repository;
    private TicketService $service;
    private MessageRepository $userMessageRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[TicketRepository::class];
        $this->service = $this->app[TicketService::class];
        $this->userMessageRepository = $this->app[MessageRepository::class];
    }

    public function testCreate(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Error, iure! Corporis, debitis! Fugit, at, iure, culpa enim tenetur optio repellat inventore consequatur ipsum asperiores mollitia aut quis vero explicabo voluptatibus.';

        // when
        $ticket = $this->service->create($userId, $catId, $message);

        // then
        $this->assertEquals($userId, $ticket->userId);
        $this->assertEquals($catId, $ticket->catId);
        $this->assertEquals(TicketStatus::NEW, $ticket->status);
        $this->assertEquals(TicketSolution::OPEN, $ticket->solution);
        $this->assertNull($ticket->adminId);
        $this->assertNull($ticket->adminComment);

        $messages = $this->service->getMessages($ticket);
        $this->assertCount(1, $messages);
        $this->assertEquals($ticket->id, $messages[0]->ticketId);
        $this->assertEquals($userId, $messages[0]->userId);
        $this->assertEquals($message, $messages[0]->message);

        $userMessages = $this->userMessageRepository->findByRecipient($userId);
        $this->assertCount(1, $userMessages);
        $this->assertEquals('Dein Ticket #000001 wurde erstellt', $userMessages[0]->subject);
        $this->assertEquals(USER_MSG_CAT_ID, $userMessages[0]->catId);
    }

    public function testAssign(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);

        // when
        $result = $this->service->assign($ticket, $adminId);

        // then
        $this->assertTrue($result);
        $this->assertEquals($adminId, $ticket->adminId);
        $this->assertEquals('assigned', $ticket->status);
    }

    public function testClose_withNewTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);

        // when
        $result = $this->service->close($ticket, 'solved');

        // then
        $this->assertFalse($result);
        $this->assertEquals(TicketStatus::NEW, $ticket->status);
    }

    public function testClose_withAssignedTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->service->assign($ticket, $adminId);

        // when
        $result = $this->service->close($ticket, 'solved');

        // then
        $this->assertTrue($result);
        $this->assertEquals(TicketStatus::CLOSED, $ticket->status);
        $this->assertEquals(TicketSolution::SOLVED, $ticket->solution);
    }

    public function testReopen_withNewTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);

        // when
        $result = $this->service->reopen($ticket);

        // then
        $this->assertFalse($result);
    }

    public function testReopen_withAssignedTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->service->assign($ticket, $adminId);

        // when
        $result = $this->service->reopen($ticket);

        // then
        $this->assertTrue($result);
        $this->assertEquals(TicketStatus::NEW, $ticket->status);
        $this->assertEquals(0, $ticket->adminId);
    }

    public function testReopen_withClosedTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->service->assign($ticket, $adminId);
        $this->service->close($ticket, 'solved');

        // when
        $result = $this->service->reopen($ticket);

        // then
        $this->assertTrue($result);
        $this->assertEquals(TicketStatus::NEW, $ticket->status);
        $this->assertEquals(TicketSolution::OPEN, $ticket->solution);
        $this->assertEquals(0, $ticket->adminId);
    }

    public function testAddMessage_withSystemUser(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->assertCount(1, $this->service->getMessages($ticket));

        // when
        $addedMessage = $this->service->addMessage($ticket, $newMessage);

        // then
        $this->assertEquals($newMessage, $addedMessage->message);
        $this->assertEquals(0, $addedMessage->userId);
        $this->assertEquals(0, $addedMessage->adminId);

        $messages = $this->service->getMessages($ticket);
        $this->assertCount(2, $messages);
        $this->assertEquals($newMessage, $messages[1]->message);
        $this->assertEquals(0, $messages[1]->userId);
        $this->assertEquals(0, $messages[1]->adminId);
    }

    public function testAddMessage_withAdmin(): void
    {
        // given
        $userId = 1;
        $adminId = 2;
        $catId = 3;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->assertCount(1, $this->service->getMessages($ticket));

        // when
        $addedMessage = $this->service->addMessage($ticket, $newMessage, 0, $adminId);

        // then
        $this->assertEquals($newMessage, $addedMessage->message);
        $this->assertEquals(0, $addedMessage->userId);
        $this->assertEquals($adminId, $addedMessage->adminId);

        $messages = $this->service->getMessages($ticket);
        $this->assertCount(2, $messages);
        $this->assertEquals($newMessage, $messages[1]->message);
        $this->assertEquals(0, $messages[1]->userId);
        $this->assertEquals($adminId, $messages[1]->adminId);
    }

    public function testAddMessage_withUser(): void
    {
        // given
        $userId = 1;
        $catId = 3;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->assertCount(1, $this->service->getMessages($ticket));

        // when
        $addedMessage = $this->service->addMessage($ticket, $newMessage, $userId);

        // then
        $this->assertEquals($newMessage, $addedMessage->message);
        $this->assertEquals($userId, $addedMessage->userId);
        $this->assertEquals(0, $addedMessage->adminId);

        $messages = $this->service->getMessages($ticket);
        $this->assertCount(2, $messages);
        $this->assertEquals($newMessage, $messages[1]->message);
        $this->assertEquals($userId, $messages[1]->userId);
        $this->assertEquals(0, $messages[1]->adminId);
    }

    public function testCloseAssignedInactive_withInactiveTicket(): void
    {
        // given
        $userId = 1;
        $adminId = 2;
        $catId = 3;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->service->assign($ticket, $adminId);
        $this->service->addMessage($ticket, $newMessage, 0, $adminId);

        $this->connection->createQueryBuilder()
            ->update('ticket_msg')
            ->set('timestamp', 'timestamp - (73 * 3600)')
            ->where('ticket_id = :ticket_id')
            ->setParameter('ticket_id', $ticket->id)
            ->execute();

        // when
        $this->service->closeAssignedInactive();

        // then
        $ticket = $this->repository->find($ticket->id);

        $this->assertEquals(TicketStatus::CLOSED, $ticket->status);
        $this->assertEquals(TicketSolution::SOLVED, $ticket->solution);
    }

    public function testCloseAssignedInactive_withActiveTicket(): void
    {
        // given
        $userId = 1;
        $adminId = 2;
        $catId = 3;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->service->create($userId, $catId, $message);
        $this->service->assign($ticket, $adminId);
        $this->service->addMessage($ticket, $newMessage, 0, $adminId);

        $this->connection->createQueryBuilder()
            ->update('ticket_msg')
            ->set('timestamp', 'timestamp - (71 * 3600)')
            ->where('ticket_id = :ticket_id')
            ->setParameter('ticket_id', $ticket->id)
            ->execute();

        // when
        $this->service->closeAssignedInactive();

        // then
        $ticket = $this->repository->find($ticket->id);

        $this->assertEquals(TicketStatus::ASSIGNED, $ticket->status);
        $this->assertEquals(TicketSolution::OPEN, $ticket->solution);
    }
}
