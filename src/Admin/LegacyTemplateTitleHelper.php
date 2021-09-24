<?php declare(strict_types=1);

namespace EtoA\Admin;

class LegacyTemplateTitleHelper
{
    public static string $title = '';
    public static string $subTitle = '';
    /** @var array<string, string> */
    public static array $flashes = [];

    public static function addFlash(string $type, string $message): void
    {
        self::$flashes[$message] = $type;
    }
}
