<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\AbstractRepository;

class AdminNotesRepository extends AbstractRepository
{
    public function countForAdmin(int $adminId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('admin_notes')
            ->where('admin_id = :adminId')
            ->setParameter('adminId', $adminId)
            ->execute()
            ->fetchOne();
    }

    /**
     * @return AdminNote[]
     */
    public function findAllForAdmin(int $adminId): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_notes')
            ->where('admin_id = :adminId')
            ->setParameter('adminId', $adminId)
            ->orderBy('date', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AdminNote($row), $data);
    }

    public function findForAdmin(int $id, int $adminId): ?AdminNote
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_notes')
            ->where('notes_id = :id')
            ->andWhere('admin_id = :admin_id')
            ->setParameters([
                'id' => $id,
                'admin_id' => $adminId,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new AdminNote($data) : null;
    }

    public function create(AdminNote $note): int
    {
        $this->createQueryBuilder()
            ->insert('admin_notes')
            ->values([
                'titel' => ':titel',
                'text' => ':text',
                'date' => ':date',
                'admin_id' => ':admin_id',
            ])
            ->setParameters([
                'titel' => $note->title,
                'text' => $note->text,
                'admin_id' => $note->adminId,
                'date' => $note->date,
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(AdminNote $note): bool
    {
        $affected = $this->createQueryBuilder()
            ->update('admin_notes')
            ->set('titel', ':titel')
            ->set('text', ':text')
            ->where('notes_id = :id')
            ->setParameters([
                'id' => $note->id,
                'titel' => $note->title,
                'text' => $note->text,
            ])
            ->execute();

        return (int) $affected > 0;
    }

    public function remove(int $id): bool
    {
        $affected = $this->createQueryBuilder()
            ->delete('admin_notes')
            ->where('notes_id = :id')
            ->setParameter('id', $id)
            ->execute();

        return (int) $affected > 0;
    }
}
