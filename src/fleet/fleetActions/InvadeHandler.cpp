
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
		
		//invade the planet
		if (bh->returnV==1) {
			
			OtherReport *report = new OtherReport(this->f->getUserId(),
												  this->f->getEntityTo(),
												  this->f->getEntityFrom(),
												  this->f->getLandtime(),
												  this->f->getId(),
												  this->f->getAction());
			report->setStatus(this->f->getStatus());
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->shipCnt = this->f->getActionCount();
				
				if (this->targetEntity->getUserId()==this->f->getUserId()) 
				{
					// Send a message to the user
					report->setSubtype("return");
					report->setRes(floor(this->f->getResMetal()),
								   floor(this->f->getResCrystal()),
								   floor(this->f->getResPlastic()),
								   floor(this->f->getResFuel()),floor(this->f->getResFood()),
								   floor(this->f->getResPeople()));
					report->setShips(this->f->getShipString());
					
					fleetLand(1);
				}
				// if the planet doesnt belong to the fleet user
				else 
				{
					/** Anti-Hack (exploited by Pain & co)
					* Check again if planet is no a main planet
					* Also explioted using a fake haven form, such 
					* that an invasion to an illegal target could be launched */
					if (!this->targetEntity->getIsUserMain()) 
					{
						this->pointsDef = (int)this->f->fleetUser->getUserPoints();
						this->pointsAtt = (int)this->targetEntity->getUser()->getUserPoints();
						
						// Calculate the Chance
						this->chance = config.nget("INVADE_POSSIBILITY",0) / this->pointsAtt * this->pointsDef;
						
						// Check if the chance is wheter higher then the max not lower then the min
						if(this->chance > config.nget("INVADE_POSSIBILITY",1))
							this->chance = config.nget("INVADE_POSSIBILITY",1);
						else if(this->chance < config.nget("INVADE_POSSIBILITY",1))
							this->chance = config.nget("INVADE_POSSIBILITY",1);
						
						this->one = rand() % 101;
						this->two = (100 * this->chance);
						
						if (this->one < this->two) {
						
							// if the user has already the number of planets
							if (this->f->fleetUser->getPlanetsCount() < (int)config.nget("user_max_planets",0)) {
								// Invade the planet
								this->targetEntity->invadeEntity(this->f->getUserId());
								this->f->fleetUser->setLastInvasion();
								this->f->deleteActionShip(1);
								
								report->setSubtype("invasion");
								report->setRes(floor(this->f->getResMetal()),
											   floor(this->f->getResCrystal()),
											   floor(this->f->getResPlastic()),
											   floor(this->f->getResFuel()),floor(this->f->getResFood()),
											   floor(this->f->getResPeople()));
								report->setShips(this->f->getShipString());
								report->setOpponent1Id(this->targetEntity->getUserId());
								
								OtherReport *vreport = new OtherReport(this->targetEntity->getUserId(),
																	  this->f->getEntityTo(),
																	  this->f->getEntityFrom(),
																	  this->f->getLandtime(),
																	  this->f->getId(),
																	  this->f->getAction());
								vreport->setStatus(this->f->getStatus());
								vreport->setSubtype("invaded");
								vreport->setOpponent1Id(this->f->getUserId());
								
								delete vreport;
								
								// Land fleet
								fleetLand(1);
								
								this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));
								
								etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
							}
							// if the user has already reached the max number of planets
							else {
								report->setSubtype("invasionfailed");
								report->setOpponent1Id(this->targetEntity->getUserId());
																
								OtherReport *vreport = new OtherReport(this->targetEntity->getUserId(),
																	   this->f->getEntityTo(),
																	   this->f->getEntityFrom(),
																	   this->f->getLandtime(),
																	   this->f->getId(),
																	   this->f->getAction());
								vreport->setStatus(this->f->getStatus());
								vreport->setSubtype("invadedfailed");
								vreport->setOpponent1Id(this->f->getUserId());
								
								delete vreport;
							}
						}
						
						// if the invasion failed
						else {
							report->setSubtype("invasionfailed");
							report->setOpponent1Id(this->targetEntity->getUserId());
							
							OtherReport *vreport = new OtherReport(this->targetEntity->getUserId(),
																   this->f->getEntityTo(),
																   this->f->getEntityFrom(),
																   this->f->getLandtime(),
																   this->f->getId(),
																   this->f->getAction());
							vreport->setStatus(this->f->getStatus());
							vreport->setSubtype("invadedfailed");
							vreport->setOpponent1Id(this->f->getUserId());
							
							delete vreport;
						}
					}
					
					// if the planet is a main planet
					else {
						report->setSubtype("invasionfailed");
						report->setOpponent1Id(this->targetEntity->getUserId());
						
						OtherReport *vreport = new OtherReport(this->targetEntity->getUserId(),
															   this->f->getEntityTo(),
															   this->f->getEntityFrom(),
															   this->f->getLandtime(),
															   this->f->getId(),
															   this->f->getAction());
						vreport->setStatus(this->f->getStatus());
						vreport->setSubtype("invadedfailed");
						vreport->setOpponent1Id(this->f->getUserId());
						
						delete vreport;
					}
				}
			}
			// If no ship with the action was in the fleet 
			else {
				report->setSubtype("actionfailed");
				
				this->actionLog->addText("Action failed: Ship error");
			}
			delete report;
		}
		
		this->f->setReturn();
		delete bh;
	}
}
