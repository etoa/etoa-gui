<?php declare(strict_types=1);

namespace EtoA\Support\Balancing;

use Doctrine\DBAL\Connection;

class Exporter
{
    private const BALANCING_TABLES = [
        'alliance_buildings',
        'alliance_technologies',
        'building_requirements',
        'building_types',
        'buildings',
        'def_cat',
        'def_requirements',
        'default_item_sets',
        'default_items',
        'defense',
        'missile_requirements',
        'missiles',
        'obj_transforms',
        'planet_types',
        'races',
        'ship_cat',
        'ship_requirements',
        'ships',
        'sol_types',
        'specialists',
        'tech_requirements',
        'tech_types',
        'technologies',
        'ticket_cat',
        'tips',
        'tutorial',
        'tutorial_texts',
    ];

    private Connection $connection;
    private string $balancingFolder;

    public function __construct(Connection $connection, string $balancingFolder)
    {
        $this->connection = $connection;
        $this->balancingFolder = $balancingFolder;
    }

    public function export(string $folder): void
    {
        $path = $this->balancingFolder . $folder;

        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        foreach (self::BALANCING_TABLES as $table) {
            $data = $this->connection->fetchAllAssociative('SELECT * FROM ' . $table);
            file_put_contents(sprintf('%s/%s.json', $path, $table), json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        }
    }

    public function import(string $folder): void
    {
        $path = $this->balancingFolder . $folder;

        $this->connection->executeQuery('START TRANSACTION');
        $this->connection->executeQuery('SET foreign_key_checks = 0');

        foreach (self::BALANCING_TABLES as $table) {
            $content = json_decode(file_get_contents(sprintf('%s/%s.json', $path, $table)), true);
            $this->connection->executeQuery('TRUNCATE ' . $table);

            $keys = array_keys($content[0]);

            $replacementRow = '(' .implode(',', array_fill(0, count($keys), '?')) . ')';
            $replacements = implode(',', array_fill(0, count($content), $replacementRow));
            $parameters = array_merge(...array_map(fn (array $row) => array_values($row), $content));
            $this->connection->executeQuery('INSERT INTO ' . $table . '(' . implode(',', $keys) . ') VALUES ' . $replacements, $parameters);
        }

        $this->connection->executeQuery('SET foreign_key_checks = 1');
        $this->connection->executeQuery('COMMIT');
    }
}
