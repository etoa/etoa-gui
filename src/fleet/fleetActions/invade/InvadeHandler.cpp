
#include "InvadeHandler.h"

namespace invade
{
	void InvadeHandler::update()
	{
	
		/**
		* Fleet-Action: Invade
		*/
		/** Initialize data **/
		Config &config = Config::instance();
		this->time = std::time(0);
		
		BattleHandler *bh = new BattleHandler(this->actionMessage);
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		this->actionMessage->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));
		
		//invade the planet
		if (bh->returnV) {
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->shipCnt = this->f->getActionCount();
				
				if (this->targetEntity->getUserId()==this->f->getUserId()) {
					// Send a message to the user
					this->actionMessage->addText("Eine Flotte hat folgendes Ziel erreicht:[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[b]Zeit:[/b]",1);
					this->actionMessage->addText(this->f->getLandtimeString(),1);
					this->actionMessage->addText("[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!",1);
					
					this->actionMessage->addSubject("Flotte angekommen");
					
					fleetLand(1);
				}
				// if the planet doesnt belong to the fleet user
				else {
					/** Anti-Hack (exploited by Pain & co)
					* Check again if planet is no a main planet
					* Also explioted using a fake haven form, such 
					* that an invasion to an illegal target could be launched */
					if (!this->targetEntity->getIsUserMain()) {
						this->pointsDef = this->f->fleetUser->getUserPoints();
						this->pointsAtt = this->targetEntity->getUser()->getUserPoints();
						
						// Calculate the Chance
						this->chance = config.nget("INVADE_POSSIBILITY",0) / this->pointsAtt * this->pointsDef;
						
						// Check if the chance is wheter higher then the max not lower then the min
						if(this->chance > config.nget("INVADE_POSSIBILITY",1))
							this->chance = config.nget("INVADE_POSSIBILITY",1);
						else if(this->chance < config.nget("INVADE_POSSIBILITY",1))
							this->chance = config.nget("INVADE_POSSIBILITY",1);
						
						this->one = rand() % 101;
						this->two = (100 * this->chance);
						
						if (this->one<=this->two) {
						
							// if the user has already the number of planets
							if (this->f->fleetUser->getPlanetsCount() < (int)config.nget("user_max_planets",0)) {
								// Load the main planet of the victim
								
								int entityUser = this->targetEntity->getUserId();
								
								// Invade the planet
								this->targetEntity->invadeEntity(this->f->getUserId());
								
								this->actionMessage->addText("[b]Planet:[/b]");
								this->actionMessage->addText(this->targetEntity->getCoords(),1);
								this->actionMessage->addText("[b]Besitzer:[/b] ");
								this->actionMessage->addText(this->targetEntity->getUser()->getUserNick(),2);
								this->actionMessage->addText("Dieser Planet wurde von einer Flotte, welche vom Planeten ",1);
								this->actionMessage->addText(this->startEntity->getCoords(),1);
								this->actionMessage->addText(" stammt, übernommen!",1);
								
								Message* victimMessage = new Message(this->actionMessage);
								victimMessage->addSubject("Kolonie wurde invasiert");
								victimMessage->addUserId(entityUser);
								delete victimMessage;
								
								this->actionMessage->addText("Ein Invasionsschiff wurde bei der Übernahme aufgebraucht!",1);
								
								this->actionMessage->addSubject("Planet erfolgreich invasiert");
								
								this->f->deleteActionShip(1);
								
								// Land fleet
								fleetLand(1);
								
								etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
							}
							// if the user has already reached the max number of planets
							else {
								this->actionMessage->addText("[b]Planet:[/b]");
								this->actionMessage->addText(this->targetEntity->getCoords(),1);
								this->actionMessage->addText("[b]Besitzer:[/b] ");
								this->actionMessage->addText(this->targetEntity->getUser()->getUserNick(),2);
								this->actionMessage->addText("Eine Flotte vom Planeten ",1);
								this->actionMessage->addText(this->startEntity->getCoords(),1);
								this->actionMessage->addText(" versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!");
																
								Message* victimMessage = new Message(this->actionMessage);
								victimMessage->addSubject("Invasionsversuch gescheitert");
								victimMessage->addUserId(this->targetEntity->getUserId());
								delete victimMessage;
								
								this->actionMessage->addText("Hinweis: Du hast bereits die maximale Anzahl Planeten erreicht!",1);
								
								this->actionMessage->addSubject("Invasionsversuch gescheitert");
							}
						}
						
						// if the invasion failed
						else {
							this->actionMessage->addText("[b]Planet:[/b]");
							this->actionMessage->addText(this->targetEntity->getCoords(),1);
							this->actionMessage->addText("[b]Besitzer:[/b] ");
							this->actionMessage->addText(this->targetEntity->getUser()->getUserNick(),2);
							this->actionMessage->addText("Eine Flotte vom Planeten ",1);
							this->actionMessage->addText(this->startEntity->getCoords(),1);
							this->actionMessage->addText(" versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!");
							
							this->actionMessage->addSubject("Invasionsversuch gescheitert");
							
							this->actionMessage->addUserId(this->targetEntity->getUserId());
						}
					}
					
					// if the planet is a main planet
					else {
						this->actionMessage->addText("[b]Planet:[/b]");
						this->actionMessage->addText(this->targetEntity->getCoords(),1);
						this->actionMessage->addText("[b]Besitzer:[/b] ");
						this->actionMessage->addText(this->targetEntity->getUser()->getUserNick(),2);
						this->actionMessage->addText("Eine Flotte vom Planeten ",1);
						this->actionMessage->addText(this->startEntity->getCoords(),1);
						this->actionMessage->addText(" versuchte, das Ziel zu übernehmen. Dies ist aber ein Hauptplanet, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
						
						this->actionMessage->addSubject("Invasionsversuch gescheitert");
						
						this->actionMessage->addUserId(this->targetEntity->getUserId());
					}
				}
			}
			// If no ship with the action was in the fleet 
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("versuchte das Ziel zu übernehmen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
				
				this->actionMessage->addSubject("Antraxangriff gescheitert");
				
				this->actionLog->addText("Action failed: Ship error");
			}
		}
		else
			this->actionMessage->dontSend();
		
		this->f->setReturn();
		delete bh;
	}
}
