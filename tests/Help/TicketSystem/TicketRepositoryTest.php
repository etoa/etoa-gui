<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\SymfonyWebTestCase;

class TicketRepositoryTest extends SymfonyWebTestCase
{
    private TicketRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(TicketRepository::class);
    }

    private function createTicket(
        int $id,
        int $userId,
        int $catId,
        int $adminId = 0,
        string $status = TicketStatus::NEW,
        string $solution = TicketSolution::OPEN,
        string $adminComment = '',
        ?int $timestamp = null
    ): int {
        $this->getConnection()->createQueryBuilder()
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
                'timestamp' => $timestamp ?? time(),
            ])->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function testFind_existing(): void
    {
        // given
        $id = 1;
        $userId = 2;
        $catId = 3;
        $adminId = 4;
        $status = TicketStatus::ASSIGNED;
        $solution = 'solved';
        $adminComment = 'foo bar';
        $timestamp = time();

        $this->createTicket(
            $id,
            $userId,
            $catId,
            $adminId,
            $status,
            $solution,
            $adminComment,
            $timestamp
        );

        // when
        $ticket = $this->repository->find($id);

        // then
        $this->assertNotNull($ticket);
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

    public function testCountNew(): void
    {
        // given
        $ticketId = 1;
        $userId = 2;
        $catId = 3;

        $this->createTicket(
            $ticketId,
            $userId,
            $catId
        );

        // when
        $count = $this->repository->countNew();

        // then
        $this->assertEquals(1, $count);
    }

    public function testCountAssigned(): void
    {
        // given
        $ticketId = 1;
        $userId = 2;
        $catId = 3;
        $adminId = 4;

        $this->createTicket(
            $ticketId,
            $userId,
            $catId,
            $adminId,
            TicketStatus::ASSIGNED
        );

        // when
        $count = $this->repository->countAssigned($adminId);

        // then
        $this->assertEquals(1, $count);
        $this->assertEquals(0, $this->repository->countNew());
    }

    public function testRemoveByIds(): void
    {
        // given
        $ticketId = 1;
        $userId = 2;
        $catId = 3;

        $this->createTicket(
            $ticketId,
            $userId,
            $catId
        );
        $this->assertNotNull($this->repository->find($ticketId));

        // when
        $result = $this->repository->removeByIds([$ticketId]);

        // then
        $this->assertEquals(1, $result);
        $this->assertNull($this->repository->find($ticketId));
    }
}
