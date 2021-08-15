<?php declare(strict_types=1);

namespace EtoA\Log;

class LogSeverity
{
    public const DEBUG = 0;
    public const INFO = 1;
    public const WARNING = 2;
    public const ERROR = 3;
    public const CRIT = 4;

    public const SEVERITIES = [
        self::DEBUG => "Debug",
        self::INFO => "Information",
        self::WARNING => "Warnung",
        self::ERROR => "Fehler",
        self::CRIT => "Kritisch",
    ];
}
