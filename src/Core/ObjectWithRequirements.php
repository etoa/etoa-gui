<?php declare(strict_types=1);

namespace EtoA\Core;

use Doctrine\Common\Collections\Collection;

interface ObjectWithRequirements
{
    public function getObjectRequirements(): Collection;
}
