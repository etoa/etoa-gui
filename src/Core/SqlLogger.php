<?php declare(strict_types=1);

namespace EtoA\Core;

use Psr\Log\LoggerInterface;

class SqlLogger implements \Doctrine\DBAL\Logging\SQLLogger
{
    const MAX_STRING_LENGTH = 32;
    const BINARY_DATA_VALUE = '(binary value)';

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function startQuery($sql, array $params = null, array $types = null): void
    {
        $this->logger->info($sql, null === $params ? array() : $this->normalizeParams($params));
    }

    public function stopQuery(): void
    {
    }

    private function normalizeParams(array $params): array
    {
        foreach ($params as $index => $param) {
            // normalize recursively
            if (is_array($param)) {
                $params[$index] = $this->normalizeParams($param);
                continue;
            }
            if (!is_string($params[$index])) {
                continue;
            }
            // non utf-8 strings break json encoding
            if (!preg_match('//u', $params[$index])) {
                $params[$index] = self::BINARY_DATA_VALUE;
                continue;
            }
            // detect if the too long string must be shorten
            if (self::MAX_STRING_LENGTH < mb_strlen($params[$index], 'UTF-8')) {
                $params[$index] = mb_substr($params[$index], 0, self::MAX_STRING_LENGTH - 6, 'UTF-8').' [...]';
                continue;
            }
        }

        return $params;
    }
}
