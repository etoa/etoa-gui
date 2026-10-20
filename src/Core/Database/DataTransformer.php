<?php declare(strict_types=1);

namespace EtoA\Core\Database;

use EtoA\Entity\User;
use EtoA\Universe\Resources\ResourceNames;
use Doctrine\Persistence\ManagerRegistry;

class DataTransformer
{
    private static ?ManagerRegistry $registry = null;

    public static function setRegistry(?ManagerRegistry $registry): void
    {
        self::$registry = $registry;
    }

    private static function registry(): ManagerRegistry
    {
        if (self::$registry === null) {
            throw new \LogicException('DataTransformer has no ManagerRegistry, it is set in Kernel::boot().');
        }

        return self::$registry;
    }

    /**
     * @return User[]
     */
    public static function userString(string $string): array
    {
        $userRepository = self::registry()->getRepository(User::class);

        $data = [];
        foreach (array_filter(explode(',', $string)) as $ele) {
            $user = $userRepository->findOneBy(['id'=>$ele]);

            if($user)
                $data[] = $user;
        }
        return $data;
    }

    /**
     * @param class-string $class
     * @return array<int, array{data: object, count: string}>
     */
    public static function dataString(string $string, string $class): array
    {
        $repository = self::registry()->getRepository($class);
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
