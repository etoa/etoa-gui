<?php

namespace EtoA\Form\Validation;

use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AlphaDotsOrUnderlinesConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository     $userRepository,
        private readonly Security                 $security,
    ){}

    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint):void
    {
        if (!$constraint instanceof AlphaDotsOrUnderlinesConstraint) {
            throw new UnexpectedTypeException($constraint, AlphaDotsOrUnderlinesConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        if(!StringUtils::hasAlphaDotsOrUnderlines($value))
            $this->context->addViolation('Eingabe ist ungültig!');
    }
}