
#include "SpyHandler.h"

namespace spy
{
	void SpyHandler::update()
	{
	
		/**
		* Fleet-Action: Spy
		*/
		
		Config &config = Config::instance();
		
		std::cout << "spy1"<<std::endl;
		
		this->actionMessage->addType((int)config.idget("SHIP_SPY_MSG_CAT_ID"));
		
		// Load tech levels first agressor, needs a value higher then 0 for one of them, cause /0 
		this->spyLevelAtt = this->f->fleetUser->getTechLevel("Spionagetechnik") + 1e-2 + this->f->fleetUser->getSpecialist()->getSpecialistSpyLevel();
		this->tarnLevelAtt = this->f->fleetUser->getTechLevel("Tarntechnik") + this->f->fleetUser->getSpecialist()->getSpecialistTarnLevel();
		

		// Then load the tech levels of the victim 
		this->spyLevelDef = this->targetEntity->getUser()->getTechLevel("Spionagetechnik");

  	std::cout << "TODO: spy specialist makes trouble (sigsegv when fetching level)"<<std::endl;
		//this->spyLevelDef += this->targetEntity->getUser()->getSpecialist()->getSpecialistSpyLevel();
		this->tarnLevelDef = this->targetEntity->getUser()->getTechLevel("Tarntechnik");
		//this->tarnLevelDef = + this->targetEntity->getUser()->getSpecialist()->getSpecialistTarnLevel();
		

		
		// Load spy ships agressor 
		this->spyShipsAtt = this->f->getActionCount();
		
		// Load spy ships defender or sometimes victim 
		this->spyShipsDef = this->targetEntity->getSpyCount();
		
		// If there are some spy ships in the fleet 
		if (spyShipsAtt) {
			// Calculate the defense 
			this->spyDefense1 = std::max(0.0,(this->spyLevelDef / (this->spyLevelAtt + this->tarnLevelAtt) * config.idget("SPY_DEFENSE_FACTOR_TECH")));
			this->spyDefense2 = std::max(0.0,((this->spyShipsDef / this->spyShipsAtt) * config.idget("SPY_DEFENSE_FACTOR_SHIPS")));
			this->spyDefense = std::min(this->spyDefense1 + this->spyDefense2,config.idget("SPY_DEFENSE_MAX"));
			
			this->defended = false;
			this->roll = rand() % 101;
			
			this->actionLog->addText(etoa::d2s(this->roll) + " <= " + etoa::d2s(this->spyDefense));
			if (this->roll <= this->spyDefense) {
				this->defended = true;
			}
			
			if (!this->defended) {
				// Calculate stealth bonus 
				this->tarnDefense = std::max(0.0,std::min((this->tarnLevelDef / this->spyLevelAtt * config.idget("SPY_DEFENSE_FACTOR_TARN")),config.idget("SPY_DEFENSE_MAX")));
				
				// Prepare the message header 
				this->actionMessage->addText("[b]Planet:[/b] ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[b]Besitzer:[/b] ");
				this->actionMessage->addText(this->targetEntity->getUser()->getUserNick(),2);
				
				this->info = false;
				
				// If the spy tech level is high enough show the buildings 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_BUILDINGS") && (rand() % 101) > this->tarnDefense) {
					this->actionMessage->addText(this->targetEntity->getBuildingString(),1);
					this->info = true;
				}
				
				// Same with the technologies 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_RESEARCH") && (rand() % 101) > this->tarnDefense) {
					this->actionMessage->addText(this->targetEntity->getUser()->getTechString(),1);
					this->info = true;
				}
				
				// Next to go flag for support ships
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_SUPPORT") && (rand() % 101) > this->tarnDefense)
					this->support = true;
				else
					this->support = false;
				
				// Next to go are the ships 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_SHIPS") && (rand() % 101) > this->tarnDefense) {
					this->actionMessage->addText("[b]SCHIFFE[/b]:",1);
					this->actionMessage->addText(this->targetEntity->getShipString(this->support),1);
					this->info = true;
				}
		
				// .., the defense, ... 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_DEFENSE") && (rand() % 101) > this->tarnDefense) {
					this->actionMessage->addText("[b]VERTEIDIGUNG[/b]:",1);
					this->actionMessage->addText(this->targetEntity->getDefString(),1);
					this->info = true;
				}
		
				// and at last the resources on the planet 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_RESSOURCEN") && (rand() % 101) > this->tarnDefense) {
					this->actionMessage->addText("[b]RESSOURCEN:[/b]",1);
					this->actionMessage->addText("[table]");
					
					this->actionMessage->addText("[tr][td]Titan[/td][td]");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->targetEntity->getResMetal())));
					this->actionMessage->addText("[/td][/tr]");
					
					this->actionMessage->addText("[tr][td]Silizium[/td][td]");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->targetEntity->getResCrystal())));
					this->actionMessage->addText("[/td][/tr]");
					
					this->actionMessage->addText("[tr][td]PVC[/td][td]");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->targetEntity->getResPlastic())));
					this->actionMessage->addText("[/td][/tr]");
					
					this->actionMessage->addText("[tr][td]Tritium[/td][td]");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->targetEntity->getResFuel())));
					this->actionMessage->addText("[/td][/tr]");
					
					this->actionMessage->addText("[tr][td]Nahrung[/td][td]");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->targetEntity->getResFood())));
					this->actionMessage->addText("[/td][/tr]");
					
					this->actionMessage->addText("[tr][td]Bewohner[/td][td]");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->targetEntity->getResPeople())));
					this->actionMessage->addText("[/td][/tr]");
					this->actionMessage->addText("[/table]");
					this->info = true;
				}
				
				// Finish the spy message 
				if (info) {
					this->actionMessage->addText("",2);
					this->actionMessage->addText("[b]Spionageabwehr:[/b] ");;
					this->actionMessage->addText(etoa::d2s(round(this->spyDefense)));
					this->actionMessage->addText("%\n[b]Tarnung:[/b] ");
					this->actionMessage->addText(etoa::d2s(round(this->tarnDefense)));
					this->actionMessage->addText("%");
				}
				else {
					this->actionMessage->addText("Du konntest leider nichts über den Planeten herausfinden da deine Spionagetechnologie zu wenig weit entwickelt oder der Gegner zu gut getarnt ist!\n\n[b]Spionageabwehr:[/b] ");
					this->actionMessage->addText(etoa::d2s(round(this->spyDefense)));
					this->actionMessage->addText("%\n[b]Tarnung:[/b] ");
					this->actionMessage->addText(etoa::d2s(this->tarnDefense));
					this->actionMessage->addText("%");
				}
		
				// Send the spy message to the fleet user
				std::string subject = "Spionagebericht ";
				subject += this->targetEntity->getCoords();
				
				this->actionMessage->addSubject(subject);
				
				// drop a note to the victim, that someone dudeling around the planet
				Message *victimMessage = new Message();
				victimMessage->addText("Eine fremde Flotte vom Planeten ");
				victimMessage->addText(this->startEntity->getCoords(),1);
				victimMessage->addText(" wurde in der Nähe deines Planeten ");
				victimMessage->addText(this->targetEntity->getCoords(),1);
				victimMessage->addText(" gesichtet!\n\n[b]Spionageabwehr:[/b] ");
				victimMessage->addText(etoa::d2s(round(this->spyDefense)));
				victimMessage->addText("%");
				
				victimMessage->addSubject("Raumüberwachung");
				victimMessage->addUserId(this->targetEntity->getUserId());
				victimMessage->addEntityId(this->targetEntity->getId());
				victimMessage->addFleetId(this->f->getId());
				
				victimMessage->addType((int)config.idget("SHIP_MONITOR_MSG_CAT_ID"));
				
				delete victimMessage;
			}
			// if the mission failed 
			else {
				// Send a message to the fleet user 
				this->actionMessage->addText("Dein Versuch, den Planeten ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText(" auszuspionieren schlug fehl, da du entdeckt wurdest. Deine Sonden kehren ohne Ergebniss zurück!\n\n[b]Spionageabwehr:[/b] ");
				this->actionMessage->addText(etoa::d2s(round(this->spyDefense)));
				this->actionMessage->addText("%");
				
				std::string subject = "Spionage fehlgeschlagen auf ";
				subject += this->targetEntity->getCoords();
				
				this->actionMessage->addSubject(subject);
		
				// send a note to the planet user
				Message *victimMessage = new Message();
				victimMessage->addText("Auf deinem Planeten ");
				victimMessage->addText(this->targetEntity->getCoords(),1);
				victimMessage->addText(" wurde ein Spionageversuch vom Planeten  ");
				victimMessage->addText(this->startEntity->getCoords(),1);
				victimMessage->addText(" erfolgreich verhindert!\n\n[b]Spionageabwehr:[/b] ");
				victimMessage->addText(etoa::d2s(round(this->spyDefense)));
				victimMessage->addText("%");
				
				victimMessage->addSubject("Raumüberwachung");
				victimMessage->addUserId(this->targetEntity->getUserId());
				victimMessage->addEntityId(this->targetEntity->getId());
				victimMessage->addFleetId(this->f->getId());
				
				victimMessage->addType((int)config.idget("SHIP_MONITOR_MSG_CAT_ID"));
				
				delete victimMessage;
			}
		}
		// if there was no spy ship in the fleet 
			// If no ship with the action was in the fleet 
		else {
			this->actionMessage->addText("Dein Versuch, den Planeten [b]",1);
			this->actionMessage->addText(this->targetEntity->getCoords(),1);
			this->actionMessage->addText("[/b] auszuspionieren schlug fehl, da du keine Spionagesonden mitgeschickt hast!");
			
			this->actionMessage->addSubject("Spionage gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		this->f->setReturn();
	}
}
