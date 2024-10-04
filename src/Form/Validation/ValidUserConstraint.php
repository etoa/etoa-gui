<?php

namespace EtoA\Form\Validation;

use Symfony\Component\Validator\Constraint;

class ValidUserConstraint extends Constraint
{
    public string $userNick = '';
}