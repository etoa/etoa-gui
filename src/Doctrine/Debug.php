<?php

namespace EtoA\Doctrine;

use Doctrine\ORM\Query;

class Debug
{
    //returns SQL with parsed parameters
    public static function toParsedSql(Query $query):string
    {
        $vals = $query->getParameters();
        foreach(explode('?', $query->getSql()) as $i => $part) {
            $sql = (isset($sql) ? $sql : null) . $part;

            if (isset($vals[$i])) {
                $val =  $vals[$i]->getValue();
                if(is_array($val))
                    $val = implode(',',$val);

                $sql .= $val;
            }
        }
        return $sql;
    }
}