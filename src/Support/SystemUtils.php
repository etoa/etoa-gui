<?php declare(strict_types=1);

namespace EtoA\Support;

class SystemUtils
{
    /**
     * True on a unix-like host with the posix extension loaded. The callers guard
     * posix_*, shell and /proc access with it, so the extension is what matters,
     * not the operating system name.
     */
    public static function isUnix(): bool
    {
        return defined('POSIX_F_OK');
    }

    public static function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Whether the given command is available on a unix host.
     */
    public static function unixCommandExists(string $cmd): bool
    {
        if (!self::isUnix()) {
            return false;
        }

        return (bool) shell_exec("which $cmd 2>/dev/null");
    }
}
