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

    public function create(string $titel, string $text, int $adminId): int
    {
        $this->createQueryBuilder()
            ->insert('admin_notes')
            ->values([
                'titel' => ':titel',
                'text' => ':text',
                'date' => time(),
                'admin_id' => ':admin_id',
            ])
            ->setParameters([
                'titel' => $titel,
                'text' => $text,
                'admin_id' => $adminId,
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(int $id, string $titel, string $text): bool
    {
        $affected = $this->createQueryBuilder()
            ->update('admin_notes')
            ->set('titel', ':titel')
            ->set('text', ':text')
            ->where('notes_id = :id')
            ->setParameters([
                'id' => $id,
                'titel' => $titel,
                'text' => $text,
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
