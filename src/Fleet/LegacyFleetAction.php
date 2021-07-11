<?php

declare(strict_types=1);

namespace EtoA\Fleet;

class LegacyFleetAction
{
    public static function createFactory(string $code): \FleetAction
    {
        if (!filled($code) || !ctype_alpha($code)) {
            throw new \Exception('Invalid class name for fleet action ' . $code);
        }

        $className = "fleetAction" . ucfirst($code);
        $classFile = __DIR__ . "/../../htdocs/classes/fleetaction/" . strtolower($className) . ".class.php";
        if (file_exists($classFile)) {
            include_once($classFile);

            return new $className();
        }

        throw new \Exception("Problem mit Flottenaktion $code ($classFile)!");
    }
}
