<?php declare(strict_types=1);

namespace EtoA\Form\Validation;

use Symfony\Component\Validator\Constraint;

class UniqueObjectRequirementConstraint extends Constraint
{
    /** Name of the object whose requirement list is validated, used in the message. */
    public string $objectName = '';
}
