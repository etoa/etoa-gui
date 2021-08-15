<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\Database\AbstractSearch;

class TechnologyListItemSearch extends AbstractSearch
{
    public static function create(): TechnologyListItemSearch
    {
        return new TechnologyListItemSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'techlist_user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function technologyId(int $technologyId): self
    {
        $this->parts[] = 'techlist_tech_id = :technologyId';
        $this->parameters['technologyId'] = $technologyId;

        return $this;
    }

    public function notTechnologyId(int $technologyId): self
    {
        $this->parts[] = 'techlist_tech_id <> :notTechnologyId';
        $this->parameters['notTechnologyId'] = $technologyId;

        return $this;
    }

    public function underConstruction(): self
    {
        $this->parts[] = 'techlist_build_type > 0';

        return $this;
    }
}
