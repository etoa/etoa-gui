
#include "EmpHandler.h"

namespace emp
{
	void EmpHandler::update()
	{
	
		/**
		* Fleet-Action: EMP-Attack
		*/
		
		/** Initialize some stuff **/
		Config &config = Config::instance();

		// Calculate the battle
		BattleHandler *bh = new BattleHandler(this->actionMessage);
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		this->actionMessage->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));
		
		// If the attacker is the winner, deactivade a building
		if (returnV) {
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->shipCnt = this->f->getActionCount();
				this->tLevel = this->f->fleetUser->getTechLevel("EMP-Technik");
				
				// Calculate the possibility
				this->one = rand() % 101;
				this->two = 10 + ceil(this->shipCnt/10000.0) + this->tLevel * 5 + this->f->getSpecialShipBonusEMP() * 100;
				
				if (this->one <= this->two) {
					// Calculate the damage
					this->h = rand() % (10 + this->tLevel + 1);
					if (this->tLevel==0) {
						this->tLevel = 1;
					}
					
					std::string actionString = this->targetEntity->empBuilding(this->h);
					
					if (actionString.length()) {
						this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
						this->actionMessage->addText(this->startEntity->getCoords(),1);
						this->actionMessage->addText(actionString);
						
						this->actionMessage->addSubject("Deaktivierung");
						this->actionMessage->addUserId(this->targetEntity->getUserId());
						
						//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
						
						this->f->deleteActionShip(1);
					}
					// If there exists no building to deactivade, send a message to the planet and the fleet user
					else {
						this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
						this->actionMessage->addText(this->startEntity->getCoords(),1);
						this->actionMessage->addText("[/b]hat erfolglos versucht auf dem Planeten[b]",1);
						this->actionMessage->addText(this->targetEntity->getCoords(),1);
						this->actionMessage->addText("[/b] ein Gebäude zu deaktivieren.\nHinweis: Der Spieler hat keine Gebäudeeinrichtungen, welche deaktiviert werden können!");
						
						this->actionMessage->addSubject("Deaktivierung erfolglos");
						this->actionMessage->addUserId(this->targetEntity->getUserId());
					}
				}
				
				// If the deactivation failed, send a message to the planet and the fleet user
				else {
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat erfolglos versucht auf dem Planeten[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b] ein Gebäude zu deaktivieren.");
					
					this->actionMessage->addSubject("Deaktivierung erfolglos");
					this->actionMessage->addUserId(this->targetEntity->getUserId());
				}
			}						
			// If no ship with the action was in the fleet 
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("versuchte ein Gebäude zu deaktivieren. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
				
				this->actionMessage->addSubject("Deaktivierung gescheitert");
				
				this->actionLog->addText("Action failed: Ship error");
			}
		}
		this->f->setReturn();
		delete bh;
	}
}
