<?php

namespace EtoA\Universe\Entity;

abstract class AbstractEntity
{
    public abstract function getEntityCodeString():string;
    public abstract function getAllowedFleetActions():array;
}