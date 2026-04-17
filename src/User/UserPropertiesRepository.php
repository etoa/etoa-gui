<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\User;
use EtoA\Entity\UserProperties;

class UserPropertiesRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly ConfigurationService $config
    )
    {
        parent::__construct($registry, UserProperties::class);
    }

    public function addBlank(User $user): void
    {
        $userProperties = new UserProperties();
        $userProperties->setCssStyle($this->config->get('default_css_style'));
        $user->setUserProperties($userProperties);

        $this->userRepository->save();
    }

    public function getOrCreateProperties(User $user): UserProperties
    {
        if (!$user->getUserProperties()) {
            $this->addBlank($user);
        }

        return $user->getUserProperties();
    }

    public function getProperties(int $userId): ?UserProperties
    {
        return $this->find($userId);
    }

    public function storeProperties(UserProperties $properties): void
    {
        $this->entityManager->persist($properties);
        $this->entityManager->flush();

    }

    /**
     * @return array<int, array>
     */
    public function getDesignStats(int $limit): array
    {
        return $this->createQueryBuilder('q')
            ->select( 'COUNT(q.user) as cnt')
            ->addSelect('q.cssStyle')
            ->groupBy('q.cssStyle')
            ->orderBy('cnt','DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function removeForUser(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
