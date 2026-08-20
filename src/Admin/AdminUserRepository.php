<?php

declare(strict_types=1);

namespace EtoA\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AdminUser;

class AdminUserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminUser::class);
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

    public function findOneByNickAndEmail(string $nick, string $email): ?AdminUser
    {
        return $this->findOneBy(['nick'=>$nick,'email'=>$email]);
    }

    /**
     * @return array<AdminUser>
     */
    public function findAll(): array
    {
        return $this->findBy([],['nick'=>'ASC']);
    }

    /**
     * @return array<int, string>
     */
    public function searchNicknames(): array
    {
        $qb = $this->createQueryBuilder('q')
            ->orderBy('q.nick');

        return $this->applySearchSortLimit($qb)
            ->getQuery()
            ->execute();
    }

    public function setPassword(AdminUser $adminUser, string $newHashedPassword, bool $forceChange = false): void
    {
        $adminUser->setPassword($newHashedPassword);
        $adminUser->setForcePasswordChange($forceChange);

        $this->save();
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
