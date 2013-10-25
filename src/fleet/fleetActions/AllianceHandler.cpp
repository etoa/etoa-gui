
#include "AllianceHandler.h"

namespace alliance
{
void AllianceHandler::update()
{
    /**
    * Fleet-Action: Attack
    */

    Config &config = Config::instance();

    if (config.nget("alliance_fleets_max_players",0))
    {
        f->sendHomeExceedingAllianceFleets
        (
            (unsigned int) config.nget("alliance_fleets_max_players",1),
            this->targetEntity->getUserId(),
            this->actionLog
        );
    }

    BattleHandler *bh = new BattleHandler();
    bh->battle(this->f,this->targetEntity,this->actionLog);

    // if fleet user has won the fight, send fleet home
    if (bh->returnFleet)
    {
        this->f->setReturn();
    }

    delete bh;
}
}

