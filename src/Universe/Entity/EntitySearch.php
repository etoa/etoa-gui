<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Core\Database\AbstractSearch;

class EntitySearch extends AbstractSearch
{
    public static function create(): EntitySearch
    {
        return new EntitySearch();
    }

    public function sx(int $sx): self
    {
        $this->parts[] = 'c.sx = :sx';
        $this->parameters['sx'] = $sx;

        return $this;
    }

    public function sy(int $sy): self
    {
        $this->parts[] = 'c.sy = :sy';
        $this->parameters['sy'] = $sy;

        return $this;
    }

    public function pos(int $pos): self
    {
        $this->parts[] = 'e.pos = :pos';
        $this->parameters['pos'] = $pos;

        return $this;
    }
}
