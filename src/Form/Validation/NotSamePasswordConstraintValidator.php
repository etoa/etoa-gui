<?php

namespace EtoA\Form\Validation;

use EtoA\Security\Player\CurrentPlayer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Bundle\SecurityBundle\Security;
use EtoA\User\UserRepository;

class NotSamePasswordConstraintValidator extends ConstraintValidator
{

    public function __construct(
        private readonly Security                 $security,
        private readonly UserPasswordHasherInterface $passwordHasher
    ){}

    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint):void
    {
        if (!$constraint instanceof NotSamePasswordConstraint) {
            throw new UnexpectedTypeException($constraint, NotSamePasswordConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        $user = $this->security->getUser();

        // input password = main password
        if ($this->passwordHasher->isPasswordValid($user, $value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}