<?php

use EtoA\HostCache\HostCacheRepository;

/**
 * Description of net
 *
 * @author Nicolas
 */
class Net
{
    private static array $hostCache = [];
    private static array $ipCache = [];

    /**
     * Returns the hostname of the given ip address
     * Lookup results are cached in a static array and used
     * if this function is used multiple times with the same ip. Additionally
     * the values are stored in a memory database table for faster lookups. This records
     * expire after one day.
     */
    static function getHost($ip)
    {
        if (isset(self::$hostCache[$ip]))
            return self::$hostCache[$ip];

        global $app;
        /** @var HostCacheRepository $hostCacheRepository */
        $hostCacheRepository = $app[HostCacheRepository::class];

        $host = $hostCacheRepository->getHost($ip);
        if ($host !== null) {
            self::$hostCache[$ip] = $host;
            return $host;
        }

        $host = @gethostbyaddr($ip);
        self::$hostCache[$ip] = $host;
        $hostCacheRepository->store($host, $ip);

        return $host;
    }

    /**
     *
     */
    static function getAddr($host)
    {
        if (isset(self::$ipCache[$host]))
            return self::$ipCache[$host];

        global $app;
        /** @var HostCacheRepository $hostCacheRepository */
        $hostCacheRepository = $app[HostCacheRepository::class];

        $ip = $hostCacheRepository->getAddr($host);
        if ($ip !== null) {
            self::$ipCache[$host] = $ip;
            return $ip;
        }

        $ip = @gethostbyname($host);
        self::$ipCache[$host] = $ip;
        $hostCacheRepository->store($host, $ip);

        return $ip;
    }

    static function clearCache()
    {
        global $app;
        /** @var HostCacheRepository $hostCacheRepository */
        $hostCacheRepository = $app[HostCacheRepository::class];
        $hostCacheRepository->clear();
    }
}
