<?php

declare(strict_types=1);

namespace EtoA\Text;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Text;

class TextRepository extends AbstractRepository
{
    /** @var array<string, array<string, string>> */
    private array $textDef;

    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Text::class);
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
