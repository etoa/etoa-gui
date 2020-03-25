<?php declare(strict_types=1);

namespace EtoA\Specialist\Event;

use Symfony\Component\EventDispatcher\Event;

class SpecialistDischarge extends Event
{
    public const DISCHARGE_SUCCESS = 'specialist.discharge.success';

    /** @var int */
    private $specialistId;

    public function __construct(int $specialistId)
    {
        $this->specialistId = $specialistId;
    }
}
