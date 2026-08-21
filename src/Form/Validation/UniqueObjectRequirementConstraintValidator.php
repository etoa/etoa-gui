<?php declare(strict_types=1);

namespace EtoA\Form\Validation;

use EtoA\Core\AbstractRequirements;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class UniqueObjectRequirementConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, UniqueObjectRequirementConstraint::class);

        if (!is_iterable($value)) {
            return;
        }

        $seen = [];
        foreach ($value as $requirement) {
            if (!$requirement instanceof AbstractRequirements) {
                continue;
            }

            $building = $requirement->getBuilding();
            $technology = $requirement->getTech();

            // a requirement points either at a building or at a technology
            if ($building !== null) {
                $key = 'building-' . $building->getId();
                $label = 'Gebäude ' . $building->getName();
            } elseif ($technology !== null) {
                $key = 'technology-' . $technology->getId();
                $label = 'Forschung ' . $technology->getName();
            } else {
                continue;
            }

            if (isset($seen[$key])) {
                $this->context->addViolation(sprintf(
                    '%s ist mehrfach als Voraussetzung definiert für %s',
                    $label,
                    $constraint->objectName !== '' ? $constraint->objectName : 'dieses Objekt'
                ));
            }

            $seen[$key] = true;
        }
    }
}
