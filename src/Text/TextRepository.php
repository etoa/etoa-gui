<?php

declare(strict_types=1);

namespace EtoA\Text;

use EtoA\Core\AbstractRepository;

use Doctrine\DBAL\Connection;

class TextRepository extends AbstractRepository
{
	private $textDef;

	public function __construct(Connection $connection)
	{
		parent::__construct($connection);
		$this->textDef = fetchJsonConfig("texts.conf");
	}

	public function isValidTextId(string $id): bool
	{
		return isset($this->textDef[$id]);
	}

	public function getLabel(string $id): string
	{
		return $this->textDef[$id]['label'];
	}

	public function getAllTextIDs(): array
	{
		return array_keys($this->textDef);
	}

	public function find(string $id): ?Text
	{
		if (!$this->isValidTextId($id)) {
			return null;
		}

        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('texts')
            ->where('text_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();
		if ($data) {
			$t = new Text($id, $data['text_content']);
			$t->updated = $data['text_updated'];
			$t->enabled = ($data['text_enabled'] > 0);
			$t->label = $this->textDef[$id]['label'];
			$t->description = $this->textDef[$id]['description'];
			$t->isOriginal = ($data['text_content'] == $this->textDef[$id]['default']);
			return $t;
		}
		$t = new Text($id, $this->textDef[$id]['default']);
		$t->label = $this->textDef[$id]['label'];
		$t->description = $this->textDef[$id]['description'];
		return $t;
	}

	public function save(Text $text): void
	{
        $this->getConnection()
            ->executeStatement(
                "REPLACE INTO
					texts
				(
					text_id,
					text_content,
					text_updated,
					text_enabled
				)
				VALUES (?, ?, UNIX_TIMESTAMP(), ?);",
                [
					$text->id,
					$text->content,
					$text->enabled ? 1 : 0
				]
            );
	}

	public function enableText(string $id): void
	{
        $this->createQueryBuilder()
            ->update('texts')
            ->set('text_enabled', '1')
            ->where('text_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
	}

	public function disableText(string $id): void
	{
        $this->createQueryBuilder()
            ->update('texts')
            ->set('text_enabled', '0')
            ->where('text_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
	}

	public function reset(string $id): void
	{
        $this->createQueryBuilder()
            ->delete('texts')
            ->where('text_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
	}
}
