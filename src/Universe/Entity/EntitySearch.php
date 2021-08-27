<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Core\Database\AbstractSearch;

class EntitySearch extends AbstractSearch
{
    public static function create(): EntitySearch
    {
        return new EntitySearch();
    }

    public function id(int $id): self
    {
        $this->parts[] = 'e.id = :id';
        $this->parameters['id'] = $id;

        return $this;
    }

    public function cellId(int $cellId): self
    {
        $this->parts[] = 'c.id = :cellId';
        $this->parameters['cellId'] = $cellId;

        return $this;
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

    public function cx(int $cx): self
    {
        $this->parts[] = 'c.cx = :cx';
        $this->parameters['cx'] = $cx;

        return $this;
    }

    public function cy(int $cy): self
    {
        $this->parts[] = 'c.cy = :cy';
        $this->parameters['cy'] = $cy;

        return $this;
    }

    public function pos(int $pos): self
    {
        $this->parts[] = 'e.pos = :pos';
        $this->parameters['pos'] = $pos;

        return $this;
    }

    /**
     * @param string[] $codes
     */
    public function codeIn(array $codes): self
    {
        $this->parts[] = 'e.code IN (:codes)';
        $this->stringArrayParameters['codes'] = $codes;

        return $this;
    }

    public function coordinates(EntityCoordinates $coordinates): self
    {
        return $this
            ->sx($coordinates->sx)
            ->sy($coordinates->sy)
            ->cx($coordinates->cx)
            ->cy($coordinates->cy)
            ->pos($coordinates->pos);
    }
}
