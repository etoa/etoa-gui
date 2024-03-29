<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\AbstractRepository;

class AdminUserRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('admin_users')
            ->fetchOne();
    }

    /**
     * @return int[]
     */
    public function getAdminPlayerIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select('player_id')
            ->from('admin_users')
            ->where('player_id <> 0')
            ->fetchAllAssociative();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function find(int $id): ?AdminUser
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_users')
            ->where('user_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? AdminUser::createFromArray($data) : null;
    }

    public function findOneByNick(string $nick): ?AdminUser
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_users')
            ->where('LCASE(user_nick) = LCASE(:nick)')
            ->setParameter('nick', $nick)
            ->fetchAssociative();

        return $data !== false ? AdminUser::createFromArray($data) : null;
    }

    /**
     * @return array<AdminUser>
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_users')
            ->orderBy('user_nick')
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => AdminUser::createFromArray($arr), $data);
    }

    /**
     * @return array<int, string>
     */
    public function searchNicknames(): array
    {
        $qb = $this->createQueryBuilder()
            ->select('user_id, user_nick')
            ->from('admin_users')
            ->orderBy('user_nick');

        return $this->applySearchSortLimit($qb, null, null, null)
            ->fetchAllKeyValue();
    }

    /**
     * @return array<int,string>
     */
    public function findAllAsList(): array
    {
        return $this->createQueryBuilder()
            ->select("user_id", 'user_nick')
            ->from('admin_users')
            ->orderBy('user_nick')
            ->fetchAllKeyValue();
    }

    public function setPassword(AdminUser $adminUser, string $newHashedPassword, bool $forceChange = false): void
    {
        $this->createQueryBuilder()
            ->update('admin_users')
            ->set('user_password', ':password')
            ->set('user_force_pwchange', ':pwchange')
            ->where('user_id = :id')
            ->setParameters([
                'id' => $adminUser->id,
                'password' => $newHashedPassword,
                'pwchange' => $forceChange ? 1 : 0,
            ])
            ->executeQuery();

        $adminUser->passwordString = $newHashedPassword;
        $adminUser->forcePasswordChange = $forceChange;
    }

    public function setTfaSecret(AdminUser $adminUser, string $secret): void
    {
        $this->createQueryBuilder()
            ->update('admin_users')
            ->set('tfa_secret', ':secret')
            ->where('user_id = :id')
            ->setParameters([
                'id' => $adminUser->id,
                'secret' => $secret,
            ])
            ->executeQuery();
    }

    public function save(AdminUser $adminUser): void
    {
        if ($adminUser->id != null) {
            $this->createQueryBuilder()
                ->update('admin_users')
                ->set('user_nick', ':nick')
                ->set('user_name', ':name')
                ->set('user_email', ':email')
                ->set('tfa_secret', ':tfa_secret')
                ->set('user_board_url', ':board_url')
                ->set('user_theme', ':user_theme')
                ->set('ticketmail', ':ticketmail')
                ->set('player_id', ':player_id')
                ->set('user_locked', ':user_locked')
                ->set('is_contact', ':is_contact')
                ->set('roles', ':roles')
                ->where('user_id = :id')
                ->setParameters([
                    'id' => $adminUser->id,
                    'nick' => $adminUser->nick,
                    'name' => $adminUser->name,
                    'email' => $adminUser->email,
                    'tfa_secret' => $adminUser->tfaSecret,
                    'board_url' => (string) $adminUser->boardUrl,
                    'user_theme' => $adminUser->userTheme,
                    'ticketmail' => $adminUser->ticketEmail ? 1 : 0,
                    'player_id' => $adminUser->playerId,
                    'user_locked' => $adminUser->locked ? 1 : 0,
                    'is_contact' => $adminUser->isContact ? 1 : 0,
                    'roles' => implode(',', $adminUser->roles),
                ])
                ->executeQuery();
        } else {
            $password = saltPasswort(generatePasswort());
            $this->createQueryBuilder()
                ->insert('admin_users')
                ->values([
                    'user_nick' => ':nick',
                    'user_name' => ':name',
                    'user_email' => ':email',
                    'tfa_secret' => ':tfa_secret',
                    'user_board_url' => ':board_url',
                    'user_theme' => ':user_theme',
                    'ticketmail' => ':ticketmail',
                    'player_id' => ':player_id',
                    'user_locked' => ':user_locked',
                    'is_contact' => ':is_contact',
                    'roles' => ':roles',
                    'user_password' => ':password',
                ])
                ->setParameters([
                    'nick' => $adminUser->nick,
                    'name' => $adminUser->name,
                    'email' => $adminUser->email,
                    'tfa_secret' => $adminUser->tfaSecret,
                    'board_url' => $adminUser->boardUrl,
                    'user_theme' => $adminUser->userTheme,
                    'ticketmail' => $adminUser->ticketEmail ? 1 : 0,
                    'player_id' => $adminUser->playerId,
                    'user_locked' => $adminUser->locked ? 1 : 0,
                    'is_contact' => $adminUser->isContact ? 1 : 0,
                    'roles' => implode(',', $adminUser->roles),
                    'password' => $password,
                ])
                ->executeQuery();
            $adminUser->id = (int) $this->getConnection()->lastInsertId();
        }
    }

    public function remove(AdminUser $adminUser): bool
    {
        $affected = $this->createQueryBuilder()
            ->delete('admin_users')
            ->where('user_id = :id')
            ->setParameter('id', $adminUser->id)
            ->executeQuery();
        $adminUser->id = null;

        return $affected->rowCount() > 0;
    }

    public function getNick(int $userId): ?string
    {
        return $this->getUserProperty($userId, 'user_nick');
    }

    private function getUserProperty(int $userId, string $property): ?string
    {
        $data = $this->createQueryBuilder()
            ->select($property)
            ->from('admin_users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchOne();

        return $data !== false ? $data : null;
    }
}
