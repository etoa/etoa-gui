<?php declare(strict_types=1);

namespace EtoA\Form\Validation;

use Symfony\Component\Validator\Constraint;

class UniqueObjectRequirementConstraint extends Constraint
{
    /** @var array<int, string> */
    public array $objectNames = [];
}
