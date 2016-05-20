
#include "AllianceHandler.h"

namespace alliance
{
void AllianceHandler::update()
{
    /**
    * Fleet-Action: Attack
    */

    Config &config = Config::instance();
    
    // no delete necessary, this is done by ~targetEntity()
    User * opponent = this->targetEntity->getUser();
    
    bool isAbsEnabled = (config.nget("abs_enabled",0) != 0);
    bool isAbsRestrictedOnWar = (config.nget("abs_enabled",1) != 0);
    bool isAllianceAtWarWithOpponent = false;
    
    if(opponent != NULL)
    {
        isAllianceAtWarWithOpponent = this->f->fleetUser->isAtWarWith(opponent->getAllianceId());
    }
    
    if(isAbsEnabled && (!isAbsRestrictedOnWar || isAllianceAtWarWithOpponent))
    {
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
    } else {
        BattleReport *bReport = new BattleReport(this->f->getUserId(),
                                                this->targetEntity->getUserId(),
                                                this->f->getEntityTo(),
                                                this->f->getEntityFrom(),
                                                this->f->getLandtime(),
                                                this->f->getId());
        if(!isAbsEnabled)
        {
            // Set the message to "ABS is not active"
            // (this case should be prevented by the frontend anyway)
            bReport->setSubtype("absdisabled");
        }
        // else (!isAbsRestrictedOnWar || isAllianceAtWarWithOpponent) is false at this point
        // thus (!isAbsRestrictedOnWar) and (isAllianceAtWarWithOpponent) are both false
        // so no other check is needed
        else
        {
            // Set the message to "The attack failed because there is no ongoing war"
            // This occurs e.g. when a war ends while an alliance fleet is still on its way
            bReport->setSubtype("alliancenowar");
        }
        
        // BUG: all fleet users should receive the report
        // with this implementation, only the fleet leader gets the report
        // (TODO: check whether this is done correctly in battle handler or not)
        
        // This sends the report to the users
        delete bReport;
    }
}
}

