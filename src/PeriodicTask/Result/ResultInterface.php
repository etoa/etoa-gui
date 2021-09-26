<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Result;

interface ResultInterface
{
    public function getMessage(): string;
}
