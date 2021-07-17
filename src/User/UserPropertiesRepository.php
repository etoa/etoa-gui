<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserPropertiesRepository extends AbstractRepository
{
    /**
     * @return array<string[]>
     */
    public function getDesignStats(int $limit): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    css_style,
                    COUNT(id) as cnt
                FROM
                    user_properties
                GROUP BY
                    css_style
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllAssociative();
    }

    /**
     * @return array<string[]>
     */
    public function getImagePackStats(int $limit): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    image_url,
                    COUNT(id) as cnt
                FROM
                    user_properties
                GROUP BY
                    image_url
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllAssociative();
    }

    /**
     * @return array<string[]>
     */
    public function getImageExtensionStats(int $limit): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    image_ext,
                    COUNT(id) as cnt
                FROM
                    user_properties
                GROUP BY
                    image_ext
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllAssociative();
    }
}
