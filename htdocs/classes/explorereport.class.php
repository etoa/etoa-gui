<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use EtoA\Support\StringUtils;

/**
 * Description of marketreport
 *
 * @author Nicolas
 */
class ExploreReport extends Report
{
    static $subTypes = array();

    protected $subType = 'other';

    public function __construct(\EtoA\Message\Report $args)
    {
        parent::__construct($args);
        if ($this->valid) {
        }
    }

    static function add($data)
    {
        return false;
    }

    function createSubject()
    {
        return 'Erkundung';
    }

    function __toString()
    {
        ob_start();
        $start = Entity::createFactoryById($this->entity2Id);
        $target = Entity::createFactoryById($this->entity1Id);

        switch ($this->subType) {
            case 'other':
                echo "Eine Flotte vom Planeten <b>" . $start->detailLink() . "</b> hat das Ziel <b>" . $target->detailLink() . "</b> um <b>" . StringUtils::formatDate($this->timestamp) . "</b> erkundet.";
                break;
            default:
                etoa_dump($this);
        }

        return ob_get_clean();
    }
}
