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

class TicketRepositoryTest extends AbstractDbTestCase
{
    private TicketRepository $repository;
    private MessageRepository $userMessageRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.help.ticket.repository'];
        $this->userMessageRepository = $this->app['etoa.user.message.repository'];
    }

    public function testFind_existing(): void
    {
        // given
        $id = 1;
        $userId = 2;
        $catId = 3;
        $adminId = 4;
        $status = 'assigned';
        $solution = 'solved';
        $adminComment = 'foo bar';
        $timestamp = time();

        $this->connection->createQueryBuilder()
            ->insert('tickets')
            ->values([
                'id' => ':id',
                'user_id' => ':userId',
                'cat_id' => ':catId',
                'admin_id' => ':adminId',
                'status' => ':status',
                'solution' => ':solution',
                'admin_comment' => ':adminComment',
                'timestamp' => ':timestamp',
            ])->setParameters([
                'id' => $id,
                'userId' => $userId,
                'catId' => $catId,
                'adminId' => $adminId,
                'status' => $status,
                'solution' => $solution,
                'adminComment' => $adminComment,
                'timestamp' => $timestamp,
            ])->execute();

        // when
        $ticket = $this->repository->find($id);

        // then
        $this->assertEquals($id, $ticket->id);
        $this->assertEquals($userId, $ticket->userId);
        $this->assertEquals($catId, $ticket->catId);
        $this->assertEquals($adminId, $ticket->adminId);
        $this->assertEquals($status, $ticket->status);
        $this->assertEquals($solution, $ticket->solution);
        $this->assertEquals($adminComment, $ticket->adminComment);
        $this->assertEquals($timestamp, $ticket->timestamp);
    }

    public function testFind_notExisting(): void
    {
        // when
        $ticket = $this->repository->find(100);

        // then
        $this->assertNull($ticket);
    }

    public function testCreate(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Error, iure! Corporis, debitis! Fugit, at, iure, culpa enim tenetur optio repellat inventore consequatur ipsum asperiores mollitia aut quis vero explicabo voluptatibus.';

        // when
        $ticket = $this->repository->create($userId, $catId, $message);

        // then
        $this->assertEquals($userId, $ticket->userId);
        $this->assertEquals($catId, $ticket->catId);
        $this->assertEquals('new', $ticket->status);
        $this->assertEquals('open', $ticket->solution);
        $this->assertNull($ticket->adminId);
        $this->assertNull($ticket->adminComment);

        $messages = $this->repository->getMessages($ticket);
        $this->assertCount(1, $messages);
        $this->assertEquals($ticket->id, $messages[0]->ticketId);
        $this->assertEquals($userId, $messages[0]->userId);
        $this->assertEquals($message, $messages[0]->message);

        $userMessages = $this->userMessageRepository->findByRecipient($userId);
        $this->assertCount(1, $userMessages);
        $this->assertEquals('Dein Ticket #000001 wurde erstellt', $userMessages[0]->subject);
        $this->assertEquals(USER_MSG_CAT_ID, $userMessages[0]->catId);
    }

    public function testCountNew(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Foo bar';

        $this->repository->create($userId, $catId, $message);

        // when
        $count = $this->repository->countNew();

        // then
        $this->assertEquals(1, $count);
    }

    public function testAssign(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);

        // when
        $result = $this->repository->assign($ticket, $adminId);

        // then
        $this->assertTrue($result);
        $this->assertEquals($adminId, $ticket->adminId);
        $this->assertEquals('assigned', $ticket->status);
    }

    public function testCountAssigned(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->repository->assign($ticket, $adminId);

        // when
        $count = $this->repository->countAssigned($adminId);

        // then
        $this->assertEquals(1, $count);
        $this->assertEquals(0, $this->repository->countNew());
    }

    public function testClose_withNewTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);

        // when
        $result = $this->repository->close($ticket, 'solved');

        // then
        $this->assertFalse($result);
        $this->assertEquals('new', $ticket->status);
    }

    public function testClose_withAssignedTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->repository->assign($ticket, $adminId);

        // when
        $result = $this->repository->close($ticket, 'solved');

        // then
        $this->assertTrue($result);
        $this->assertEquals('closed', $ticket->status);
        $this->assertEquals('solved', $ticket->solution);
    }

    public function testReopen_withNewTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);

        // when
        $result = $this->repository->reopen($ticket);

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

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->repository->assign($ticket, $adminId);

        // when
        $result = $this->repository->reopen($ticket);

        // then
        $this->assertTrue($result);
        $this->assertEquals('new', $ticket->status);
        $this->assertEquals(0, $ticket->adminId);
    }

    public function testReopen_withClosedTicket(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $adminId = 3;
        $message = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->repository->assign($ticket, $adminId);
        $this->repository->close($ticket, 'solved');

        // when
        $result = $this->repository->reopen($ticket);

        // then
        $this->assertTrue($result);
        $this->assertEquals('new', $ticket->status);
        $this->assertEquals('open', $ticket->solution);
        $this->assertEquals(0, $ticket->adminId);
    }

    public function testAddMessage_withSystemUser(): void
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->assertCount(1, $this->repository->getMessages($ticket));

        // when
        $addedMessage = $this->repository->addMessage($ticket, [
            'message' => $newMessage,
        ]);

        // then
        $this->assertEquals($newMessage, $addedMessage->message);
        $this->assertEquals(0, $addedMessage->userId);
        $this->assertEquals(0, $addedMessage->adminId);

        $messages = $this->repository->getMessages($ticket);
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

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->assertCount(1, $this->repository->getMessages($ticket));

        // when
        $addedMessage = $this->repository->addMessage($ticket, [
            'message' => $newMessage,
            'admin_id' => $adminId,
        ]);

        // then
        $this->assertEquals($newMessage, $addedMessage->message);
        $this->assertEquals(0, $addedMessage->userId);
        $this->assertEquals($adminId, $addedMessage->adminId);

        $messages = $this->repository->getMessages($ticket);
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

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->assertCount(1, $this->repository->getMessages($ticket));

        // when
        $addedMessage = $this->repository->addMessage($ticket, [
            'message' => $newMessage,
            'user_id' => $userId,
        ]);

        // then
        $this->assertEquals($newMessage, $addedMessage->message);
        $this->assertEquals($userId, $addedMessage->userId);
        $this->assertEquals(0, $addedMessage->adminId);

        $messages = $this->repository->getMessages($ticket);
        $this->assertCount(2, $messages);
        $this->assertEquals($newMessage, $messages[1]->message);
        $this->assertEquals($userId, $messages[1]->userId);
        $this->assertEquals(0, $messages[1]->adminId);
    }

    public function testFindOrphanedIds()
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';

        $ticket = $this->repository->create($userId, $catId, $message);

        // when
        $ids = $this->repository->findOrphanedIds();

        // then
        $this->assertEquals([$ticket->id], $ids);
    }

    public function removeByIds()
    {
        // given
        $userId = 1;
        $catId = 2;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->assertNotNull($this->repository->find($ticket->id));

        // when
        $result = $this->repository->removeByIds($ticket->id);

        // then
        $this->assertEquals(1, $result);
        $this->assertNull($this->repository->find($ticket->id));
    }

    public function testCloseAssignedInactive_withInactiveTicket()
    {
        // given
        $userId = 1;
        $adminId = 2;
        $catId = 3;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->repository->assign($ticket, $adminId);
        $this->repository->addMessage($ticket, [
            'message' => $newMessage,
            'admin_id' => $adminId,
        ]);

        $this->connection->createQueryBuilder()
            ->update('ticket_msg')
            ->set('timestamp', 'timestamp - (73 * 3600)')
            ->where('ticket_id = :ticket_id')
            ->setParameter('ticket_id', $ticket->id)
            ->execute();

        // when
        $this->repository->closeAssignedInactive();

        // then
        $ticket = $this->repository->find($ticket->id);

        $this->assertEquals('closed', $ticket->status);
        $this->assertEquals('solved', $ticket->solution);
    }

    public function testCloseAssignedInactive_withActiveTicket()
    {
        // given
        $userId = 1;
        $adminId = 2;
        $catId = 3;
        $message = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere voluptates optio nihil nulla delectus provident sed fugit. Ab fugiat deleniti hic in quae. Dolor officia iste voluptate perferendis? Aspernatur, animi?';
        $newMessage = 'Foo bar';

        $ticket = $this->repository->create($userId, $catId, $message);
        $this->repository->assign($ticket, $adminId);
        $this->repository->addMessage($ticket, [
            'message' => $newMessage,
            'admin_id' => $adminId,
        ]);

        $this->connection->createQueryBuilder()
            ->update('ticket_msg')
            ->set('timestamp', 'timestamp - (71 * 3600)')
            ->where('ticket_id = :ticket_id')
            ->setParameter('ticket_id', $ticket->id)
            ->execute();

        // when
        $this->repository->closeAssignedInactive();

        // then
        $ticket = $this->repository->find($ticket->id);

        $this->assertEquals('assigned', $ticket->status);
        $this->assertEquals('open', $ticket->solution);
    }
}
