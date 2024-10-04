<?php

namespace EtoA\Form\Validation;

use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidUserConstraintValidator extends ConstraintValidator
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
        if (!$constraint instanceof ValidUserConstraint) {
            throw new UnexpectedTypeException($constraint, ValidUserConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        $sitterUser = $this->userRepository->findUser(UserSearch::create()->notUser($this->security->getUser()->getId())->nick($value));
        if(!$sitterUser)
            $this->context->addViolation('Benutzername ist ungültig!');
    }
}