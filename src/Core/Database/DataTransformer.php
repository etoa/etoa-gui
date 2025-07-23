<?php declare(strict_types=1);

namespace EtoA\Core\Database;

use EtoA\Entity\User;

class DataTransformer
{
    /**
     * @return int[]
     */
    public static function userString(string $string): array
    {
        //TODO: refactor from static to use DI
        $userRepository = $GLOBALS['app']->getContainer()->get('doctrine')->getRepository(User::class);

        return array_values(array_map(fn (int $user) => $userRepository->find((int) $user), array_filter(explode(',', $string))));
    }

    /**
     * @return int[]
     */
    public static function dataString(string $string, string $class): array
    {

        //TODO: refactor from static to use DI
        $repository = $GLOBALS['app']->getContainer()->get('doctrine')->getRepository($class);
        $entries = [];
        $shipEntries = array_filter(explode(',', $string));
        foreach ($shipEntries as $entry) {
            [$id, $count] = explode(":", $entry);
            if ($id > 0) {
                $entries[$repository->find($id)] = (int) $count;
            }
        }

        return $entries;
    }
}
