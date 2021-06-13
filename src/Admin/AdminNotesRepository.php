<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\AbstractRepository;

class AdminNotesRepository extends AbstractRepository
{
    function countForAdmin(int $adminId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('admin_notes')
            ->where('admin_id = ?')
            ->setParameter(0, $adminId)
            ->execute()
            ->fetchOne();
    }

    public function findAllForAdmin(int $adminId): array
    {
        return $this->createQueryBuilder()
            ->select("*")
            ->from('admin_notes')
            ->where('admin_id = ?')
            ->setParameter(0, $adminId)
            ->orderBy('date', 'DESC')
            ->execute()
            ->fetchAllAssociative();
    }

    public function findForAdmin(int $id, int $adminId): ?array
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
        return $data ? $data : null;
    }

    function create(string $titel, string $text, int $adminId): int
    {
        $this->createQueryBuilder()
            ->insert('admin_notes')
            ->values([
                'titel' => ':titel',
                'text' => ':text',
                'date' => time(),
                'admin_id' => ':admin_id'
            ])
            ->setParameters([
                'titel' => $titel,
                'text' => $text,
                'admin_id' => $adminId,
            ])
            ->execute();
        return (int) $this->getConnection()->lastInsertId();
    }

    function update(int $id, string $titel, string $text): bool
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
        return $affected > 0;
    }

    function remove(int $id): bool
    {
        $affected = $this->createQueryBuilder()
            ->delete('admin_notes')
            ->where('notes_id = :id')
            ->setParameter('id', $id)
            ->execute();
        return $affected > 0;
    }
}
