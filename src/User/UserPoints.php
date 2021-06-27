<?php declare(strict_types=1);

namespace EtoA\User;

class UserPoints
{
    public int $id;
    public int $userId;
    public int $timestamp;
    public int $points;
    public int $shipPoints;
    public int $techPoints;
    public int $buildingPoints;

    public function __construct(array $data)
    {
        $this->id = (int) $data['point_id'];
        $this->userId = (int) $data['point_user_id'];
        $this->timestamp = (int) $data['point_timestamp'];
        $this->points = (int) $data['point_points'];
        $this->shipPoints = (int) $data['point_ship_points'];
        $this->techPoints = (int) $data['point_tech_points'];
        $this->buildingPoints = (int) $data['point_building_points'];
    }
}
