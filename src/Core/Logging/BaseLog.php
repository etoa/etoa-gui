<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

abstract class BaseLog
{
    // Severities

    /**
     * Debug message
     */
    const DEBUG = 0;
    /**
     * Information
     */
    const INFO = 1;
    /**
     * Warning
     */
    const WARNING = 2;
    /**
     * Error
     */
    const ERROR = 3;
    /**
     * Critical error
     */
    const CRIT = 4;

    public static $severities = [
        "Debug",
        "Information",
        "Warnung",
        "Fehler",
        "Kritisch",
    ];
}
