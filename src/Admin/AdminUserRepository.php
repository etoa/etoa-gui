<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\AbstractRepository;

class AdminUserRepository extends AbstractRepository
{
    function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('admin_users')
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?AdminUser
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_users')
            ->where('user_id = ?')
            ->setParameter(0, $id)
            ->execute()
            ->fetchAssociative();
        return $data ? $this->createObject($data) : null;
    }

    public function findOneByNick(string $nick)
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_users')
            ->where('LCASE(user_nick) = LCASE(?)')
            ->setParameter(0, $nick)
            ->execute()
            ->fetchAssociative();
        return $data ? $this->createObject($data) : null;
    }

    public function findAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_users')
            ->orderBy('user_nick')
            ->execute()
            ->fetchAllAssociative();
        return collect($data)
            ->map(fn ($arr) => $this->createObject($arr))
            ->toArray();
    }

    public function findAllAsList(): array
    {
        $data = $this->createQueryBuilder()
            ->select("user_id", 'user_nick')
            ->from('admin_users')
            ->orderBy('user_nick')
            ->execute()
            ->fetchAllAssociative();
        return collect($data)
            ->mapWithKeys(fn ($arr) => [$arr['user_id'] => $arr['user_nick']])
            ->toArray();
    }

    private function createObject(array $data): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->id = (int) $data['user_id'];
        $adminUser->passwordString = $data['user_password'];

        $adminUser->nick = $data['user_nick'];
        $adminUser->forcePasswordChange = $data['user_force_pwchange'] > 0;

        $adminUser->name = $data['user_name'];
        $adminUser->email = $data['user_email'];
        $adminUser->tfaSecret = $data['tfa_secret'];
        $adminUser->playerId = (int) $data['player_id'];
        $adminUser->boardUrl = $data['user_board_url'];
        $adminUser->userTheme = $data['user_theme'];
        $adminUser->ticketEmail = $data['ticketmail'] > 0;
        $adminUser->locked = $data['user_locked'] > 0;
        $adminUser->roles = explode(",", $data['roles']);
        $adminUser->isContact = $data['is_contact'] > 0;

        return $adminUser;
    }

    function setPassword(AdminUser &$adminUser, string $password, bool $forceChange = false): void
    {
        $pws = saltPasswort($password);

        $this->createQueryBuilder()
            ->update('admin_users')
            ->set('user_password', ':password')
            ->set('user_force_pwchange', $forceChange ? 1 : 0)
            ->where('user_id = :id')
            ->setParameters([
                'id' => $adminUser->id,
                'password' => $pws,
            ])
            ->execute();

        $adminUser->passwordString = $pws;
        $adminUser->forcePasswordChange = $forceChange;
    }

    function save(AdminUser &$adminUser): void
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
                    'board_url' => $adminUser->boardUrl,
                    'user_theme' => $adminUser->userTheme,
                    'ticketmail' => $adminUser->ticketEmail ? 1 : 0,
                    'player_id' => $adminUser->playerId,
                    'user_locked' => $adminUser->locked ? 1 : 0,
                    'is_contact' => $adminUser->isContact ? 1 : 0,
                    'roles' => implode(',', $adminUser->roles),
                ])
                ->execute();
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
                ->execute();
            $adminUser->id = (int) $this->getConnection()->lastInsertId();
        }
    }

    function remove(AdminUser &$adminUser): bool
    {
        $affected = $this->createQueryBuilder()
            ->delete('admin_users')
            ->where('user_id = :id')
            ->setParameter('id', $adminUser->id)
            ->execute();
        $adminUser->id = null;
        return $affected > 0;
    }

    function countActiveSessions(int $timeout): int
    {
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT COUNT(*)
                FROM admin_user_sessions
                WHERE time_action > ?;",
                [(time() - $timeout)]
            )
            ->fetchOne();
    }
}
