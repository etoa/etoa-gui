<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Planet;
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

    public function getUserMainId(int $userId): ?int
    {
       return $this->findOneBy(['mainPlanet'=>1,'userId'=>$userId])?->getId();
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
        int $id,
        float $resMetal,
        float $resCrystal,
        float $resPlastic,
        float $resFuel,
        float $resFood,
        int $people = 0,
        int $fields = 0
    ): bool {
        $affected = $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_res_metal', 'planet_res_metal + :res_metal')
            ->set('planet_res_crystal', 'planet_res_crystal + :res_crystal')
            ->set('planet_res_plastic', 'planet_res_plastic + :res_plastic')
            ->set('planet_res_fuel', 'planet_res_fuel + :res_fuel')
            ->set('planet_res_food', 'planet_res_food + :res_food')
            ->set('planet_people', 'planet_people + :people')
            ->set('planet_fields_used', 'planet_fields_used + :fields')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'res_metal' => $resMetal,
                'res_crystal' => $resCrystal,
                'res_plastic' => $resPlastic,
                'res_fuel' => $resFuel,
                'res_food' => $resFood,
                'people' => $people,
                'fields' => $fields,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    /**
     * @param BaseResources|PreciseResources $resources
     */
    public function removeResources(int $id, $resources): bool
    {
        $planetResources = $this->getPlanetResources($id);
        if ($planetResources === null) {
            return false;
        }

        $missing = $resources->missing($planetResources);
        if ($missing->getSum() > 0) {
            return false;
        }

        $affected = $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_res_metal', 'planet_res_metal - :res_metal')
            ->set('planet_res_crystal', 'planet_res_crystal - :res_crystal')
            ->set('planet_res_plastic', 'planet_res_plastic - :res_plastic')
            ->set('planet_res_fuel', 'planet_res_fuel - :res_fuel')
            ->set('planet_res_food', 'planet_res_food - :res_food')
            ->set('planet_people', 'planet_people - :people')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'res_metal' => $resources->metal,
                'res_crystal' => $resources->crystal,
                'res_plastic' => $resources->plastic,
                'res_fuel' => $resources->fuel,
                'res_food' => $resources->food,
                'people' => $resources->people,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function getPlanetResources(int $id): ?PreciseResources
    {
        $data = $this->createQueryBuilder('q')
            ->select('planet_res_metal', 'planet_res_crystal', 'planet_res_plastic, planet_res_fuel, planet_res_food, planet_people')
            ->from('planets')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if ($data === false) {
            return null;
        }

        $resources = new PreciseResources();
        $resources->metal = (float) $data['planet_res_metal'];
        $resources->crystal = (float) $data['planet_res_crystal'];
        $resources->plastic = (float) $data['planet_res_plastic'];
        $resources->fuel = (float) $data['planet_res_fuel'];
        $resources->food = (float) $data['planet_res_food'];
        $resources->people = (float) $data['planet_people'];

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

    public function changeUser(int $id, int $userId, ?string $name = null): bool
    {
        $qry = $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_user_id', ':userId')
            ->set('planet_user_changed', 'UNIX_TIMESTAMP()')
            ->set('planet_user_main', (string) 0)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ]);

        if ($name !== null) {
            $qry->set('planet_name', ':name')
                ->setParameter('name', $name);
        }

        $affected = $qry->executeQuery()->rowCount();

        return $affected > 0;
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
        int $id,
        float $metal,
        float $crystal,
        float $plastic,
        float $fuel,
        float $food
    ): void {
        $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_bunker_metal', ':metal')
            ->set('planet_bunker_crystal', ':crystal')
            ->set('planet_bunker_plastic', ':plastic')
            ->set('planet_bunker_fuel', ':fuel')
            ->set('planet_bunker_food', ':food')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'metal' => $metal,
                'crystal' => $crystal,
                'plastic' => $plastic,
                'fuel' => $fuel,
                'food' => $food,
            ])
            ->executeQuery();
    }

    public function reset(Planet $planet): void
    {
        $planet->setUserId(0);
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

    public function setMain(int $id, int $userId): bool
    {
        if ($userId == 0) {
            return false;
        }

        $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_user_main', (string) 0)
            ->where('planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->executeQuery();

        $affected = $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_user_main', (string) 1)
            ->where('id = :id')
            ->andWhere('planet_user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function unsetMain(int $id): bool
    {
        $affected = $this->createQueryBuilder('q')
            ->update('planets')
            ->set('planet_user_main', (string) 0)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
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
        return $this->getMaxResourcesOfAPlayer('planet_res_metal');
    }

    public function getMaxCrystalOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('planet_res_crystal');
    }

    public function getMaxPlasticOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('planet_res_plastic');
    }

    public function getMaxFuelOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('planet_res_fuel');
    }

    public function getMaxFoodOfAPlayer(): int
    {
        return $this->getMaxResourcesOfAPlayer('planet_res_food');
    }

    private function getMaxResourcesOfAPlayer(string $field): int
    {
        return (int) $this->getConnection()
            ->fetchOne(
                "SELECT
                    SUM(" . $field . ") AS sum
                FROM
                    planets
                INNER JOIN
                    users
                ON
                    user_id = planet_user_id
                    AND user_ghost = 0
                    AND user_hmode_from = 0
                    AND user_hmode_to = 0
                GROUP BY
                    planet_user_id
                ORDER BY
                    sum DESC
                LIMIT 1;"
            );
    }

    /**
     * @return string[]
     */
    public function getMaxMetal(): array
    {
        return $this->getMaxResources('planet_res_metal');
    }

    /**
     * @return string[]
     */
    public function getMaxCrystal(): array
    {
        return $this->getMaxResources('planet_res_crystal');
    }

    /**
     * @return string[]
     */
    public function getMaxPlastic(): array
    {
        return $this->getMaxResources('planet_res_plastic');
    }

    /**
     * @return string[]
     */
    public function getMaxFuel(): array
    {
        return $this->getMaxResources('planet_res_fuel');
    }

    /**
     * @return string[]
     */
    public function getMaxFood(): array
    {
        return $this->getMaxResources('planet_res_food');
    }

    /**
     * @return string[]
     */
    private function getMaxResources(string $field): array
    {
        return $this->getConnection()
            ->fetchAssociative(
                "SELECT
                    SUM(" . $field . ") AS sum,
                    AVG(" . $field . ") AS avg,
                    COUNT(id) AS cnt
                FROM
                    planets
                INNER JOIN
                    users
                ON
                    planet_user_id = user_id
                    AND user_ghost = 0
                    AND user_hmode_from = 0
                    AND user_hmode_to = 0
                    AND " . $field . " > 0"
            );
    }

    /**
     * @return ?string[]
     */
    public function getMaxMetalOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('planet_res_metal');
    }

    /**
     * @return ?string[]
     */
    public function getMaxCrystalOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('planet_res_crystal');
    }

    /**
     * @return ?string[]
     */
    public function getMaxPlasticOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('planet_res_plastic');
    }

    /**
     * @return ?string[]
     */
    public function getMaxFuelOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('planet_res_fuel');
    }

    /**
     * @return ?string[]
     */
    public function getMaxFoodOnAPlanet(): ?array
    {
        return $this->getMaxResourcesOnAPlanet('planet_res_food');
    }

    /**
     * @return ?string[]
     */
    private function getMaxResourcesOnAPlanet(string $field): ?array
    {
        $data = $this->getConnection()
            ->fetchAssociative(
                "SELECT
                    " . $field . " AS res,
                    type_name AS type
                FROM
                    planet_types
                INNER JOIN
                    (
                        planets
                    INNER JOIN
                        users
                    ON
                        planet_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    planet_type_id = type_id
                ORDER BY
                    res DESC
                LIMIT 1;"
            );

        return $data !== false ? $data : null;
    }
}
