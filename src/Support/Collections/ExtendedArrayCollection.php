<?php

namespace EtoA\Support\Collections;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Extended array collection methods
 *
 * @see https://gist.github.com/jamesmoey/275faa6d58392fa742b6
 * @see https://adamwathan.me/2016/07/14/customizing-keys-when-mapping-collections/
 */
class ExtendedArrayCollection extends ArrayCollection
{
    /**
     * Reduce the collection into a single value.
     *
     * @param \Closure $func
     * @param null $initialValue
     * @return mixed
     */
    public function reduce(Closure $func, $initialValue = null)
    {
        return array_reduce($this->toArray(), $func, $initialValue);
    }

    public function mapToAssoc($callback)
    {
        return $this->map($callback)->toAssoc();
    }

    public function toAssoc()
    {
        return $this->reduce(function ($assoc, $keyValuePair) {
            list($key, $value) = $keyValuePair;
            $assoc[$key] = $value;
            return $assoc;
        }, new static);
    }
}
