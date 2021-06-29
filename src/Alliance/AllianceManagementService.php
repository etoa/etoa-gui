<?php

declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceManagementService
{
    private AllianceRepository $repository;

    public function __construct(AllianceRepository $repository)
    {
        $this->repository = $repository;
    }
}
