<?php

namespace EtoA\DefaultItem;

enum DefaultItemType: string
{
    case BUILDING  = 'b';
    case TECHNOLOGY = 't';
    case DEFENSE    = 'd';
    case SHIP       = 's';
    case MISSILE    = 'm';
}