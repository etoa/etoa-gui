
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

		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionLog);

		//invade the planet
		if (bh->returnV==1) {

			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->shipCnt = this->f->getActionCount();

				if (this->targetEntity->getUserId()==this->f->getUserId())
				{
					OtherReport *report = new OtherReport(this->f->getUserId(),
														  this->f->getEntityTo(),
														  this->f->getEntityFrom(),
														  this->f->getLandtime(),
														  this->f->getId(),
														  this->f->getAction());
					report->setStatus(this->f->getStatus());
					report->setSubtype("return");
					report->setRes(floor(this->f->getResMetal()),
								   floor(this->f->getResCrystal()),
								   floor(this->f->getResPlastic()),
								   floor(this->f->getResFuel()),floor(this->f->getResFood()),
								   floor(this->f->getResPeople()));
					report->setShips(this->f->getShipString());
					delete report;

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
						// ToDo, find a better solution!!!
						this->pointsAtt = 1;//(int)this->f->fleetUser->getUserPoints();
						this->pointsDef = 1;//(int)this->targetEntity->getUser()->getUserPoints();

						// Calculate the Chance
						this->chance = config.nget("invade_possibility",0) / this->pointsAtt * this->pointsDef;

						// Check if the chance is wheter higher then the max not lower then the min
						if(this->chance > config.nget("invade_possibility",1))
							this->chance = config.nget("invade_possibility",1);
						else if(this->chance < config.nget("invade_possibility",2))
							this->chance = config.nget("invade_possibility",2);

						this->one = rand() % 101;
						this->two = (100 * this->chance);

						if (this->one < this->two && this->targetEntity->getUser()->isInactiv()) {
							if (this->f->fleetUser->getPlanetsCount() < (int)config.nget("user_max_planets",0)) {

								BattleReport *invade = new BattleReport(this->f->getUserId(),
																		 this->targetEntity->getUserId(),
																		 this->f->getEntityTo(),
																		 this->f->getEntityFrom(),
																		 this->f->getLandtime(),
																		 this->f->getId());
								invade->setSubtype("invasion");
								invade->setRes(floor(this->f->getResMetal()),
											   floor(this->f->getResCrystal()),
											   floor(this->f->getResPlastic()),
											   floor(this->f->getResFuel()),floor(this->f->getResFood()),
											   floor(this->f->getResPeople()));
								invade->setShips(this->f->getShipString());
								invade->setOpponent1Id(this->targetEntity->getUserId());
								delete invade;

								BattleReport *vreport = new BattleReport(this->targetEntity->getUserId(),
																		 this->f->getUserId(),
																		 this->f->getEntityTo(),
																		 this->f->getEntityFrom(),
																		 this->f->getLandtime(),
																		 this->f->getId());
								vreport->setSubtype("invaded");
								vreport->setOpponent1Id(this->f->getUserId());

								delete vreport;

								// Invade the planet
								this->targetEntity->invadeEntity(this->f->getUserId());
								this->f->fleetUser->setLastInvasion();
								this->f->deleteActionShip(1);

								// Land fleet
								fleetLand(1);

								this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));

								etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
							}
							// if the user has already reached the max number of planets
							else {
								BattleReport *invade = new BattleReport(this->f->getUserId(),
																		 this->targetEntity->getUserId(),
																		 this->f->getEntityTo(),
																		 this->f->getEntityFrom(),
																		 this->f->getLandtime(),
																		 this->f->getId());
								invade->setSubtype("invasionfailed");
								invade->setOpponent1Id(this->targetEntity->getUserId());
								invade->setContent("1");
								delete invade;

								BattleReport *vreport = new BattleReport(this->targetEntity->getUserId(),
																		this->f->getUserId(),
																	   this->f->getEntityTo(),
																	   this->f->getEntityFrom(),
																	   this->f->getLandtime(),
																	   this->f->getId());
								vreport->setSubtype("invadedfailed");
								vreport->setContent("1");
								vreport->setOpponent1Id(this->f->getUserId());

								delete vreport;
							}
						}

						// if the invasion failed
						else {
							BattleReport *invade = new BattleReport(this->f->getUserId(),
																	 this->targetEntity->getUserId(),
																	 this->f->getEntityTo(),
																	 this->f->getEntityFrom(),
																	 this->f->getLandtime(),
																	 this->f->getId());
							invade->setSubtype("invasionfailed");
							invade->setOpponent1Id(this->targetEntity->getUserId());
							delete invade;

							BattleReport *vreport = new BattleReport(this->targetEntity->getUserId(),
																	this->f->getUserId(),
																   this->f->getEntityTo(),
																   this->f->getEntityFrom(),
																   this->f->getLandtime(),
																   this->f->getId());
							vreport->setSubtype("invadedfailed");
							vreport->setContent("1");
							vreport->setOpponent1Id(this->f->getUserId());

							delete vreport;
						}
					}

					// if the planet is a main planet
					else {
						BattleReport *invade = new BattleReport(this->f->getUserId(),
																 this->targetEntity->getUserId(),
																 this->f->getEntityTo(),
																 this->f->getEntityFrom(),
																 this->f->getLandtime(),
																 this->f->getId());
						invade->setSubtype("invasionfailed");
						invade->setContent("2");
						invade->setOpponent1Id(this->targetEntity->getUserId());
						delete invade;

						BattleReport *vreport = new BattleReport(this->targetEntity->getUserId(),
																this->f->getUserId(),
															   this->f->getEntityTo(),
															   this->f->getEntityFrom(),
															   this->f->getLandtime(),
															   this->f->getId());
						vreport->setSubtype("invadedfailed");
						vreport->setContent("2");
						vreport->setOpponent1Id(this->f->getUserId());

						delete vreport;
					}
				}
			}
			// If no ship with the action was in the fleet
			else {
				OtherReport *report = new OtherReport(this->f->getUserId(),
													this->f->getEntityTo(),
													this->f->getEntityFrom(),
													this->f->getLandtime(),
													this->f->getId(),
													this->f->getAction());
				report->setSubtype("actionfailed");

				delete report;

				this->actionLog->addText("Action failed: Ship error");
			}
		}

		this->f->setReturn();
		delete bh;
	}
}
