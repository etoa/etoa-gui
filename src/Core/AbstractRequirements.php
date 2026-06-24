<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\Entity\Building;
use EtoA\Entity\Technology;

interface AbstractRequirements
{
    public function getBuilding(): ?Building;
    public function getTech(): ?Technology;
    public function getLevel(): ?int;
}
