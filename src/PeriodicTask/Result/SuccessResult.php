<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Result;

class SuccessResult implements ResultInterface
{
    private string $message;

    public static function create(string $message): SuccessResult
    {
        $result = new self();
        $result->message = $message;

        return $result;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
