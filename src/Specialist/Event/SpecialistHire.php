<?php declare(strict_types=1);

namespace EtoA\Specialist\Event;

use Symfony\Component\EventDispatcher\Event;

class SpecialistHire extends Event
{
    const HIRE_SUCCESS = 'specialist.hire.success';

    /** @var int */
    private $specialistId;

    public function __construct($specialistId)
    {
        $this->specialistId = $specialistId;
    }
}
