<?php declare(strict_types=1);

namespace EtoA\Building;

enum BuildingId: int
{
    case BUILDING      = 6;   // Basis
    case PEOPLE        = 7;   // Wohnmodul
    case TECHNOLOGY    = 8;   // Lab
    case SHIPYARD      = 9;   // Schiffswerft
    case DEFENSE       = 10;  // Waffenfabrik
    case FLEET_CONTROL = 11;  // Flottenkontrolle
    case MARKET        = 21;  // Marktplatz
    case CRYPTO        = 24;  // Kryptocenter
    case MISSILE       = 25;  // Raketensilo
    case RES_BUNKER    = 26;  // Rohstoffbunker
    case FLEET_BUNKER  = 27;  // Flottenbunker
}