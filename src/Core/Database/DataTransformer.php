<?php declare(strict_types=1);

namespace EtoA\Core\Database;

use EtoA\Entity\User;
use EtoA\Universe\Resources\ResourceNames;

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

    public static function ressourceString(string $string): array
    {
        $entries = [];
        $dataEntries = explode(':', $string);

        if($dataEntries && str_contains($string,':'))
            foreach (ResourceNames::NAMES as $k => $v) {
                $entries[$v] = (int) $dataEntries[$k];
            }

        return $entries;
    }
}
