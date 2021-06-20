<?php declare(strict_types=1);

namespace EtoA\Specialist\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SpecialistHire extends Event
{
    public const HIRE_SUCCESS = 'specialist.hire.success';

    private int $specialistId;

    public function __construct(int $specialistId)
    {
        $this->specialistId = $specialistId;
    }
}
