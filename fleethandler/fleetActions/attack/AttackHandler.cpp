
#include "AttackHandler.h"

namespace attack
{
	void AttackHandler::update()
	{
		/**
		* Fleet-Action: Attack
		*/
		std::cout << "start\n";
		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionMessage,this->actionLog);
		std::cout << "end\n";		
		// if fleet user has won the fight, send fleet home
		if (bh->returnFleet)
		{
			this->f->setReturn();
		}

		delete bh;
	}
}

