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
        //TODO: refactor from static to use DI or use container
        $userRepository = $GLOBALS['app']->getContainer()->get('doctrine')->getRepository(User::class);

        $data = [];
        foreach (array_filter(explode(',', $string)) as $ele) {
            $user = $userRepository->findOneBy(['id'=>$ele]);

            if($user)
                $data[] = $user;
        }
        return $data;
    }

    /**
     * @return int[]
     */
    public static function dataString(string $string, string $class): array
    {
        //TODO: refactor from static to use DI
        $repository = $GLOBALS['app']->getContainer()->get('doctrine')->getRepository($class);
        $entries = [];
        $dataEntries = array_filter(explode(',', $string));
        foreach ($dataEntries as $entry) {
            [$id, $count] = explode(":", $entry);
            if ($id > 0) {
                $data = $repository->find($id);
                if($data)
                    $entries[] = ['data'=>$data,'count'=>$count];
            }
        }

        return $entries;
    }
}
