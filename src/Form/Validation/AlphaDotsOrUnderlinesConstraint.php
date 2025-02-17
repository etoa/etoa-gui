<?php

namespace EtoA\Form\Validation;

use Symfony\Component\Validator\Constraint;

class AlphaDotsOrUnderlinesConstraint extends Constraint
{
    public string $text = '';
}