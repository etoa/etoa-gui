<?php declare(strict_types=1);

namespace EtoA\Specialist\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SpecialistDischarge extends Event
{
    public const DISCHARGE_SUCCESS = 'specialist.discharge.success';

    private int $specialistId;

    public function __construct(int $specialistId)
    {
        $this->specialistId = $specialistId;
    }

    public function getSpecialistId(): int
    {
        return $this->specialistId;
    }
}
