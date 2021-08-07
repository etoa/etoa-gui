<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Database\AbstractSearch;

class AllianceSearch extends AbstractSearch
{
    public static function create(): AllianceSearch
    {
        return new AllianceSearch();
    }

    public function nameOrTagLike(string $search): self
    {
        $this->parts[] = 'alliance_name LIKE :nameOrTag OR alliance_tag LIKE :nameOrTag';
        $this->parameters['nameOrTag'] = '%' . $search . '%';

        return $this;
    }
}
