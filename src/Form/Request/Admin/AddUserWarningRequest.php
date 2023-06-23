<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class AddUserWarningRequest
{
    public int $userId = 0;
    public string $text = '';
}
