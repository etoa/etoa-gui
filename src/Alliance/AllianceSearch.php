<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Alliance;

class AllianceSearch extends AbstractSearch
{
    public static function create(): AllianceSearch
    {
        return new AllianceSearch();
    }

    public function nameOrTagLike(string $search): self
    {
        $this->parts[] = 'q.name LIKE :nameOrTag OR q.tag LIKE :nameOrTag';
        $this->parameters['nameOrTag'] = '%' . $search . '%';

        return $this;
    }

    public function tagLike(string $search): self
    {
        $this->parts[] = 'q.tag LIKE :tag';
        $this->parameters['tag'] = '%' . $search . '%';

        return $this;
    }

    public function nameLike(string $search): self
    {
        $this->parts[] = 'q.name LIKE :name';
        $this->parameters['name'] = '%' . $search . '%';

        return $this;
    }

    public function textLike(string $search): self
    {
        $this->parts[] = 'q.text LIKE :text';
        $this->parameters['text'] = '%' . $search . '%';

        return $this;
    }

    public function motherId(int|Alliance $motherAllianceId): self
    {
        $this->parts[] = 'q.mother = :motherAllianceId';
        $this->parameters['motherAllianceId'] = $motherAllianceId;

        return $this;
    }

    public function motherRequestAllianceId(int|Alliance $motherRequestAllianceId): self
    {
        $this->parts[] = 'q.motherRequest = :motherRequestAllianceId';
        $this->parameters['motherRequestAllianceId'] = $motherRequestAllianceId;

        return $this;
    }
}
