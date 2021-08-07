<?php

declare(strict_types=1);

namespace EtoA\HostCache;

class NetworkNameService
{
    private HostCacheRepository $hostCacheRepository;

    /** @var string[] */
    private array $hostCache = [];

    /** @var string[] */
    private array $ipCache = [];

    public function __construct(HostCacheRepository $hostCacheRepository)
    {
        $this->hostCacheRepository = $hostCacheRepository;
    }

    /**
     * Returns the hostname of the given ip address
     * Lookup results are cached in a static array and used
     * if this function is used multiple times with the same ip. Additionally
     * the values are stored in a memory database table for faster lookups. This records
     * expire after one day.
     */
    public function getHost(string $ip): string
    {
        if (isset($this->hostCache[$ip])) {
            return $this->hostCache[$ip];
        }

        $host = $this->hostCacheRepository->getHost($ip);
        if ($host !== null) {
            $this->hostCache[$ip] = $host;

            return $host;
        }

        $host = @gethostbyaddr($ip);
        $this->hostCache[$ip] = $host;
        $this->hostCacheRepository->store($host, $ip);

        return $host;
    }

    public function getAddr(string $host): string
    {
        if (isset($this->ipCache[$host])) {
            return $this->ipCache[$host];
        }

        $ip = $this->hostCacheRepository->getAddr($host);
        if ($ip !== null) {
            $this->ipCache[$host] = $ip;

            return $ip;
        }

        $ip = @gethostbyname($host);
        $this->ipCache[$host] = $ip;
        $this->hostCacheRepository->store($host, $ip);

        return $ip;
    }

    public function clearCache(): void
    {
        $this->hostCacheRepository->clear();
    }
}
