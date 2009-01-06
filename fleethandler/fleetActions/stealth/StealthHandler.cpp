
#include "StealthHandler.h"

namespace stealth
{
	void StealthHandler::update()
	{
	
		/**
		* Fleet-Action: Stealth Attack
		*/

		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionMessage,this->actionLog);
		
		// if fleet user has won the fight, send fleet home
		if (bh->returnFleet)
		{
			this->f->setReturn();
		}

		delete bh;
	}
}
