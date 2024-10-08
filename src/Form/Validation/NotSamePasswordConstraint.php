<?php

namespace EtoA\Form\Validation;

use Symfony\Component\Validator\Constraint;

class NotSamePasswordConstraint extends Constraint
{
    public string $password = '';
    public string $message = 'Das Passwort darf nicht identisch sein!';

    public function __construct(?string $message = null)
    {
        parent::__construct([]);

        $this->message = $message ?? $this->message;
    }
}