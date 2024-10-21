<?php

declare(strict_types=1);

namespace EtoA\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AdminUser;

class AdminUserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, AdminUser::class);
    }

    public function count(array $criteria = []): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select("COUNT(*)")
            ->from('admin_users')
            ->fetchOne();
    }

    /**
     * @return int[]
     */
    public function getAdminPlayerIds(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('player_id')
            ->from('admin_users')
            ->where('player_id <> 0')
            ->fetchAllAssociative();

        return array_map(fn($value) => (int)$value, $data);
    }

    public function findOneByNick(string $nick): ?AdminUser
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('admin_users')
            ->where('LCASE(user_nick) = LCASE(:nick)')
            ->setParameter('nick', $nick)
            ->fetchAssociative();

        return $data !== false ? AdminUser::createFromArray($data) : null;
    }

    public function findOneByNickAndEmail(string $nick, string $email): ?AdminUser
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('admin_users')
            ->where('LCASE(user_nick) = LCASE(:nick)')
            ->where('user_email = :email')
            ->setParameter('nick', $nick)
            ->setParameter('email', $email)
            ->fetchAssociative();

        return $data !== false ? AdminUser::createFromArray($data) : null;
    }

    /**
     * @return array<AdminUser>
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('admin_users')
            ->orderBy('user_nick')
            ->fetchAllAssociative();

        return array_map(fn(array $arr) => AdminUser::createFromArray($arr), $data);
    }

    /**
     * @return array<int, string>
     */
    public function searchNicknames(): array
    {
        $qb = $this->createQueryBuilder('q')
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
        return $this->createQueryBuilder('q')
            ->select("user_id", 'user_nick')
            ->from('admin_users')
            ->orderBy('user_nick')
            ->fetchAllKeyValue();
    }

    public function setPassword(AdminUser $adminUser, string $newHashedPassword, bool $forceChange = false): void
    {
        $this->createQueryBuilder('q')
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
        $this->createQueryBuilder('q')
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
        $this->entityManager->flush();
    }

    public function remove(AdminUser $adminUser): bool
    {
        $affected = $this->createQueryBuilder('q')
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
        $data = $this->createQueryBuilder('q')
            ->select($property)
            ->from('admin_users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchOne();

        return $data !== false ? $data : null;
    }
}
