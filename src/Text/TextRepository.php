<?php

declare(strict_types=1);

namespace EtoA\Text;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Text;
use EtoA\Support\FileUtils;
use Exception;

class TextRepository extends AbstractRepository
{
    /** @var array<string, array<string, string>> */
    private array $textDef;

    /**
     * @throws Exception
     */
    public function __construct(
        ManagerRegistry $registry,
        readonly FileUtils $fileUtils
    )
    {
        parent::__construct($registry, Text::class);
        $this->textDef = $fileUtils->fetchJsonConfig("texts.json");
    }

    public function isValidTextId(string $id): bool
    {
        return isset($this->textDef[$id]);
    }

    public function getLabel(string $id): string
    {
        return $this->textDef[$id]['label'];
    }

    /**
     * @return string[]
     */
    public function getAllTextIDs(): array
    {
        return array_keys($this->textDef);
    }

    public function enableText(string $id): void
    {
        $this->createQueryBuilder('q')
            ->update('texts')
            ->set('text_enabled', '1')
            ->where('text_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();
    }

    public function disableText(string $id): void
    {
        $this->createQueryBuilder('q')
            ->update('texts')
            ->set('text_enabled', '0')
            ->where('text_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();
    }

    public function reset(string $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('texts')
            ->where('text_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?object
    {
        $text = parent::find($id);

        if (!$text && !$this->isValidTextId($id)) {
            return null;
        }

        if(!$text || !$text->getContent()) {
            $text = new Text();
            $text->setContent($this->textDef[$id]['default']);
        }

        return $text;
    }

    public function getEnabledTextOrDefault(string $key, string $default = ''): string
    {
        $text = $this->find($key);
        if ($text !== null) {
            if ($text->isEnabled() && $text->getContent() !== '') {
                return $text->getContent();
            }

            return $default;
        }

        throw new \RuntimeException('Text not found for key: ' . $key);
    }
}
