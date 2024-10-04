<?php

namespace EtoA\Form\Validation;

use Symfony\Component\Validator\Constraint;

class SamePasswordConstraint extends Constraint
{
    public string $password = '';
}