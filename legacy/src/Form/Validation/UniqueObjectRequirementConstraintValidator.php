<?php declare(strict_types=1);

namespace EtoA\Form\Validation;

use EtoA\Building\BuildingDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class UniqueObjectRequirementConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private BuildingDataRepository $buildingDataRepository,
        private TechnologyDataRepository $technologyDataRepository,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, UniqueObjectRequirementConstraint::class);

        if (!is_array($value) || count($value) === 0) {
            return;
        }

        $requiredIds = [];
        foreach ($value as $requirement) {
            if (isset($requiredIds[$requirement->requiredId()])) {
                if ($requirement->requiredBuildingId > 0) {
                    $this->context->addViolation(sprintf(
                        'Gebäude %s is mehrfach als Voraussetzung definiert für %s',
                        $this->buildingDataRepository->getBuildingName($requirement->requiredBuildingId),
                        $constraint->objectNames[$requirement->objectId] ?? 'Unbekanntes Object'
                    ));
                } elseif ($requirement->requiredTechnologyId > 0) {
                    $this->context
                        ->addViolation((sprintf(
                            'Forschung %s is bereits als Voraussetzung definiert für %s',
                            $this->technologyDataRepository->getTechnologyName($requirement->requiredTechnologyId),
                            $constraint->objectNames[$requirement->objectId] ?? 'Unbekanntes Object'
                        )));
                }
            }

            $requiredIds[$requirement->requiredId()] = true;
        }
    }
}
