<?php declare(strict_types=1);

namespace EtoA\Message;

class ReportContextRequest
{
    /** @var int[] */
    private array $entityIds = [];
    /** @var int[] */
    private array $userIds = [];
    /** @var int[] */
    private array $fleetIds = [];
    /** @var int[] */
    private array $shipIds = [];
    /** @var int[] */
    private array $buildingIds = [];
    /** @var int[] */
    private array $technologyIds = [];
    /** @var int[] */
    private array $defenseIds = [];

    public function addEntityId(int $id): self
    {
        $this->entityIds[] = $id;

        return $this;
    }

    public function addUserId(int $id): self
    {
        $this->userIds[] = $id;

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function addUserIds(array $ids): self
    {
        $this->userIds = array_merge($this->userIds, $ids);

        return $this;
    }

    public function addFleetId(int $id): self
    {
        $this->fleetIds[] = $id;

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function addShipIds(array $ids): self
    {
        $this->shipIds = array_merge($this->shipIds, $ids);

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function addBuildingIds(array $ids): self
    {
        $this->buildingIds = array_merge($this->buildingIds, $ids);

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function addTechnologyIds(array $ids): self
    {
        $this->technologyIds = array_merge($this->technologyIds, $ids);

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function addDefenseIds(array $ids): self
    {
        $this->defenseIds = array_merge($this->defenseIds, $ids);

        return $this;
    }

    /**
     * @return int[]
     */
    public function getEntityIds(): array
    {
        return array_filter(array_unique($this->entityIds));
    }

    /**
     * @return int[]
     */
    public function getUserIds(): array
    {
        return array_filter(array_unique($this->userIds));
    }

    /**
     * @return int[]
     */
    public function getFleetIds(): array
    {
        return array_filter(array_unique($this->fleetIds));
    }

    /**
     * @return int[]
     */
    public function getShipIds(): array
    {
        return array_filter(array_unique($this->shipIds));
    }

    /**
     * @return int[]
     */
    public function getBuildingIds(): array
    {
        return array_filter(array_unique($this->buildingIds));
    }

    /**
     * @return int[]
     */
    public function getTechnologyIds(): array
    {
        return array_filter(array_unique($this->technologyIds));
    }

    /**
     * @return int[]
     */
    public function getDefenseIds(): array
    {
        return array_filter(array_unique($this->defenseIds));
    }
}
