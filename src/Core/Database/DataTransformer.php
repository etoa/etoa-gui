<?php declare(strict_types=1);

namespace EtoA\Core\Database;

class DataTransformer
{
    /**
     * @return int[]
     */
    public static function userString(string $string): array
    {
        return array_values(array_map(fn (string $userId) => (int) $userId, array_filter(explode(',', $string))));
    }

    /**
     * @return int[]
     */
    public static function dataString(string $string): array
    {
        $entries = [];
        $shipEntries = array_filter(explode(',', $string));
        foreach ($shipEntries as $entry) {
            [$id, $count] = explode(":", $entry);
            if ($id > 0) {
                $entries[(int) $id] = (int) $count;
            }
        }

        return $entries;
    }
}
