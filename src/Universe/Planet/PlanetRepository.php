<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Star\StarRepository;

class PlanetRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, private readonly StarRepository $starRepository, private readonly EntityRepository $entityRepository)
    {
        parent::__construct($registry, Planet::class);
    }

    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("id")
            ->from('planets')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    /**
     * @return Planet[]
     */
    public function getUserPlanets(int $userId): array
    {
        return $this->findBy(['user'=>$userId]);
    }

    /**
     * @return PlanetWithCoordinates[]
     */
    public function getUserPlanetsWithCoordinates(int $userId): array
    {
        $data = $this->userPlanetsQueryBuilder($userId)
            ->addSelect(
                'e.id',
                'c.id as cid',
                'code',
                'pos',
                'sx',
                'sy',
                'cx',
                'cy'
            )
            ->innerJoin('planets', 'entities', 'e', 'e.id = planets.id')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->orderBy('planet_user_main', 'DESC')
            ->addOrderBy('planets.id', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new PlanetWithCoordinates($row), $data);
    }

    private function userPlanetsQueryBuilder(int $userId): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->select('planets.*')
            ->where('planet_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('planet_user_main', 'DESC')
            ->addOrderBy('planet_name', 'ASC');
    }

    /**
     * @return Planet[]
     */
    public function search(PlanetSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('*')
            ->from('planets', 'p')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Planet($row), $data);
    }

    /**
     * @return Planet[]
     */
    public function getMainPlanets(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('planets')
            ->where('planet_user_main = 1')
            ->andWhere('planet_user_id > 0')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Planet($row), $data);
    }

    public function getPlanetUserId(int $planetId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('planet_user_id')
            ->from('planets')
            ->where('id = :planetId')
            ->setParameter('planetId', $planetId)
            ->fetchOne();
    }

    /**
     * @return PlanetNameWithUserNick[]
     */
    public function searchPlanetNamesWithUserNick(PlanetSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('p.id, p.planet_name')
            ->addSelect('u.user_id, u.user_nick')
            ->from('planets', 'p')
            ->leftJoin('p', 'users', 'u', 'u.user_id = p.planet_user_id')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new PlanetNameWithUserNick($row), $data);
    }

    public function getUserMain(User $user): ?Planet
    {
       return $this->findOneBy(['mainPlanet'=>1,'user'=>$user]);
    }

    public function getPlanetCount(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(p.id)')
            ->from('planets', 'p')
            ->where('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->fetchOne();
    }

    public function countWithUser(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select("COUNT(id)")
            ->from('planets')
            ->where('planet_user_id > 0')
            ->fetchOne();
    }

    public function getRandomFreePlanet(int $sx = 0, int $sy = 0, ?int $minFields = null, ?int $planetType = null, ?int $starType = null):?Planet
    {
        $qry = $this->createQueryBuilder('q')
            ->innerJoin('App:PlanetType', 't', 'WITH', 'q.typeId = t.id AND t.habitable = 1')
            ->innerJoin('App:Entity', 'e', 'WITH', 'q.id = e.id')
            ->innerJoin('App:Cell', 'c', 'WITH', 'e.cellId = c.id')
            ->orderBy('Rand()')
            ->setMaxResults(1);

        if ($sx > 0) {
            $qry->andWhere('c.sx = :sx')
                ->setParameter('sx', $sx);
        }

        if ($sy > 0) {
            $qry->andWhere('c.sy = :sy')
                ->setParameter('sy', $sy);
        }

        if ($planetType > 0) {
            $qry->andWhere('q.typeId = :planetType')
                ->setParameter('planetType', $planetType);
        }

        if ($minFields > 0) {
            $qry->andWhere('q.fields > :minFields')
                ->setParameter('minFields', $minFields);
        }

        if ($starType > 0) {
            //TODO: simplify
            $stars = $this->starRepository->findBy(['typeId'=>$starType]);
            $starIds = array_map(fn ($row) => (int) $row->getId(), $stars);
            $entity = $this->entityRepository->searchEntities(EntitySearch::create()->ids($starIds));
            $cellIds = array_map(fn ($row) => (int) $row['cid'], $entity);

            $qry->andWhere(
                'e.cellId in (:cellIds)')
                ->setParameter('cellIds', $cellIds);
        }

        return $qry
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function add(
        int $id,
        int $typeId,
        int $fields,
        string $image,
        int $tempFrom,
        int $tempTo
    ): void {
        $this->createQueryBuilder('q')
            ->insert('planets')
            ->values([
                'id' => ':id',
                'planet_type_id' => ':type_id',
                'planet_fields' => ':fields',
                'planet_image' => ':image',
                'planet_temp_from' => ':temp_from',
                'planet_temp_to' => ':temp_to',
            ])
            ->setParameters([
                'id' => $id,
                'type_id' => $typeId,
                'fields' => $fields,
                'image' => $image,
                'temp_from' => $tempFrom,
                'temp_to' => $tempTo,
            ])
            ->executeQuery();
    }

    public function setResources(
        Planet $planet,
        int $resMetal,
        int $resCrystal,
        int $resPlastic,
        int $resFuel,
        int $resFood,
        int $people
    ): void {
        $planet->setResMetal($resMetal);
        $planet->setResCrystal($resCrystal);
        $planet->setResPlastic($resPlastic);
        $planet->setResFuel($resFuel);
        $planet->setResFood($resFood);
        $planet->setPeople($people);
        $this->save();
    }

    public function addResources(
        Planet $planet,
        float $resMetal,
        float $resCrystal,
        float $resPlastic,
        float $resFuel,
        float $resFood,
        int $people = 0,
        int $fields = 0
    ): void {

        $planet->setResMetal($planet->getResMetal()+$resMetal);
        $planet->setResCrystal($planet->getResCrystal()+$resCrystal);
        $planet->setResPlastic($planet->getResPlastic()+$resPlastic);
        $planet->setResFuel($planet->getResFuel()+$resFuel);
        $planet->setResFood($planet->getResFood()+$resFood);
        $planet->setPeople($planet->getPeople()+$people);
        $planet->setFieldsUsed($planet->getFieldsUsed()+$fields);

        $this->save();
    }

    /**
     * @param BaseResources|PreciseResources $resources
     */
    public function removeResources(Planet $planet, BaseResources|PreciseResources $resources): bool
    {
        $planetResources = $this->getPlanetResources($planet);
        if ($planetResources === null) {
            return false;
        }

        $missing = $resources->missing($planetResources);
        if ($missing->getSum() > 0) {
            return false;
        }


        $affected = $this->createQueryBuilder('q')
            ->update()
            ->set('q.resMetal', 'q.resMetal - :res_metal')
            ->set('q.resCrystal', 'q.resCrystal - :res_crystal')
            ->set('q.resPlastic', 'q.resPlastic - :res_plastic')
            ->set('q.resFuel', 'q.resFuel - :res_fuel')
            ->set('q.resFood', 'q.resFood - :res_food')
            ->set('q.people', 'q.people - :people')
            ->where('q.id = :id')
            ->setParameters([
                'id' => $planet->getId(),
                'res_metal' => $resources->metal,
                'res_crystal' => $resources->crystal,
                'res_plastic' => $resources->plastic,
                'res_fuel' => $resources->fuel,
                'res_food' => $resources->food,
                'people' => $resources->people,
            ])
            ->getQuery()
            ->execute();

        return $affected > 0;
    }

    public function getPlanetResources(Planet $planet): ?PreciseResources
    {
        $resources = new PreciseResources();
        $resources->metal = $planet->getResMetal();
        $resources->crystal = $planet->getResCrystal();
        $resources->plastic = $planet->getResPlastic();
        $resources->fuel = $planet->getResFuel();
        $resources->food = $planet->getResFood();
        $resources->people = $planet->getPeople();

        return $resources;
    }

    public function addPeople(int $id, int $amount): void
    {
        $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_people', 'planet_people + :people')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'people' => $amount,
            ])
            ->executeQuery();
    }

    public function assignToUser(Planet $planet, int $userId, bool $main = false): void
    {
        $planet->setUserId($userId);
        $planet->setMainPlanet($main);
        $this->save();
    }

    public function changeUser(Planet $planet, ?User $user, ?string $name = null): void
    {
        if($planet->getUser() !== $user)
            $planet->setUser($user);
        $planet->setUserChanged(time());
        $planet->setMainPlanet(false);

        if ($name) {
            $planet->setName($name);
        }

        $this->save();
    }

    public function setNameAndComment(int $id, string $name, string $comment): void
    {
        $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_name', ':name')
            ->set('planet_desc', ':comment')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'name' => stripBBCode($name),
                'comment' => $comment,
            ])
            ->executeQuery();
    }

    public function updateBunker(
        Planet $planet,
        int $metal,
        int $crystal,
        int $plastic,
        int $fuel,
        int $food
    ): void {
        $planet->setBunkerMetal($metal);
        $planet->setBunkerMetal($crystal);
        $planet->setBunkerMetal($plastic);
        $planet->setBunkerMetal($fuel);
        $planet->setBunkerMetal($food);

        $this->save();
    }

    public function reset(Planet $planet): void
    {
        $planet->setUser(null);
        $planet->setName('');
        $planet->setMainPlanet(false);
        $planet->setFieldsUsed(0);
        $planet->setFieldsExtra(0);
        $planet->setResMetal(0);
        $planet->setResCrystal(0);
        $planet->setResPlastic(0);
        $planet->setResFuel(0);
        $planet->setResFood(0);
        $planet->setUsePower(0);
        $planet->setLastUpdated(0);
        $planet->setProdMetal(0);
        $planet->setProdCrystal(0);
        $planet->setProdPlastic(0);
        $planet->setProdFuel(0);
        $planet->setProdFood(0);
        $planet->setBunkerMetal(0);
        $planet->setBunkerCrystal(0);
        $planet->setBunkerPlastic(0);
        $planet->setBunkerFuel(0);
        $planet->setBunkerFood(0);
        $planet->setStoreMetal(0);
        $planet->setStoreCrystal(0);
        $planet->setStorePlastic(0);
        $planet->setStoreFuel(0);
        $planet->setStoreFood(0);
        $planet->setPeople(0);
        $planet->setPeoplePlace(0);
        $planet->setDescription('');
        $this->save();
    }

    public function resetUserChanged(int $id): void
    {
        $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_user_changed', (string) 0)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();
    }

    public function setLastUpdated(int $id, int $timestamp): void
    {
        $this->createQueryBuilder('q')
            ->set('planet_last_updated', $timestamp)
            ->where('id = :id')
            ->setParameters([
                'id' => $id
            ])
            ->getQuery()
            ->execute();
    }

    public function setMain(Planet $planet): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.mainPlanet',0)
            ->where('q.user = :user')
            ->setParameters([
                'user' => $planet->getUser(),
            ])
            ->getQuery()
            ->execute();

        $planet->setMainPlanet(true);
        $planet->getUser()->setUserChangedMainPlanet(true);

        $this->save();
    }

    public function unsetMain(Planet $planet): void
    {
        $planet->setMainPlanet(false);
        $this->save();
    }

    public function freezeProduction(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->set('q.lastUpdated', "0")
            ->set('q.prodMetal', "0")
            ->set('q.prodCrystal', "0")
            ->set('q.prodPlastic', "0")
            ->set('q.prodFuel', "0")
            ->set('q.prodFood', "0")
            ->set('q.prodPower', "0")
            ->where('q.userId = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->getQuery()
            ->execute();
    }

    public function getGlobalResources(): BaseResources
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'SUM(planet_res_metal) as metal',
                'SUM(planet_res_crystal) as crystal',
                'SUM(planet_res_plastic) as plastic',
                'SUM(planet_res_fuel) as fuel',
                'SUM(planet_res_food) as food'
            )
            ->from('planets', 'p')
            ->innerJoin('p', 'users', 'u', 'planet_user_id = user_id AND user_ghost = 0')
            ->fetchAssociative();

        $res = new BaseResources();
        $res->metal = (int) $data['metal'];
        $res->crystal = (int) $data['crystal'];
        $res->plastic = (int) $data['plastic'];
        $res->fuel = (int) $data['fuel'];
        $res->food = (int) $data['food'];

        return $res;
    }

    public function getMaxMetalOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('q.resMetal');
    }

    public function getMaxCrystalOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('q.resCrystal');
    }

    public function getMaxPlasticOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('q.resPlastic');
    }

    public function getMaxFuelOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('q.resFuel');
    }

    public function getMaxFoodOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('q.resFood');
    }

    private function getMaxResourcesOfAPlayer(string $field): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select( "SUM($field) as sum")
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->setMaxResults(1)
            ->groupBy('q.user')
            ->orderBy('sum', 'DESC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return string[]
     */
    public function getMaxMetal(): array
    {
        return $this->getMaxResources('q.resMetal');
    }

    /**
     * @return string[]
     */
    public function getMaxCrystal(): array
    {
        return $this->getMaxResources('q.resCrystal');
    }

    /**
     * @return string[]
     */
    public function getMaxPlastic(): array
    {
        return $this->getMaxResources('q.resPlastic');
    }

    /**
     * @return string[]
     */
    public function getMaxFuel(): array
    {
        return $this->getMaxResources('q.resFuel');
    }

    /**
     * @return string[]
     */
    public function getMaxFood(): array
    {
        return $this->getMaxResources('q.resFood');
    }

    /**
     * @return string[]
     */
    private function getMaxResources(string $field): array
    {
        return $this->createQueryBuilder('q')
            ->select( "SUM($field) as sum")
            ->addSelect("AVG($field) as avg")
            ->addSelect('COUNT(q.id) as cnt')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->andWhere("$field > 0")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return ?string[]
     */
    public function getMaxMetalOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('q.resMetal');
    }

    /**
     * @return ?string[]
     */
    public function getMaxCrystalOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('q.resCrystal');
    }

    /**
     * @return ?string[]
     */
    public function getMaxPlasticOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('q.resPlastic');
    }

    /**
     * @return ?string[]
     */
    public function getMaxFuelOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('q.resFuel');
    }

    /**
     * @return ?string[]
     */
    public function getMaxFoodOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('q.resFood');
    }

    /**
     * @return ?string[]
     */
    private function getMaxResourcesOnAPlanet(string $field): ?array
    {
        return $this->createQueryBuilder('q')
            ->select( "$field as res")
            ->addSelect('t.name as type')
            ->innerJoin('App:PlanetType', 't', 'WITH', 'q.planetType = t.id')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->orderBy('res')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, array{name: string, cnt: string}>
     */
    public function getNumberOfOwnedPlanetsByType(): array
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q.id) as cnt')
            ->addSelect('t.name as name')
            ->innerJoin('App:PlanetType', 't', 'WITH', 'q.planetType = t.id')
            ->innerJoin('App:User', 'u', 'WITH', 'q.user = u.id')
            ->where('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->groupBy('t.id')
            ->orderBy('cnt')
            ->getQuery()
            ->execute();
    }
}
