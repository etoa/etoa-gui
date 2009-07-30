
#include "AttackHandler.h"

namespace attack
{
	void AttackHandler::update()
	{
		/**
		* Fleet-Action: Attack
		*/
		BattleHandler *bh = new BattleHandler(this->actionMessage);
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		this->actionMessage->dontSend();
		
		// if fleet user has won the fight, send fleet home
		if (bh->returnFleet)
		{
			this->f->setReturn();
		}

		delete bh;
	}
}
