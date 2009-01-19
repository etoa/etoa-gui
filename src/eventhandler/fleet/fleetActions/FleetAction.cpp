
#include "FleetAction.h"

	void FleetAction::fleetLand(int fleetAction)
	{
		mysqlpp::Query query = con_->query();
		
		//Flotte wird stationiert und Waren werden ausgeladen
		if(fleetAction==1) {
			//Rohstoffnachricht für den User
			this->msgRes += "\n\n[b]WAREN[/b]\n\n[b]Titan:[/b] "
				+ functions::nf(functions::d2s(this->f->getResMetal()))
				+ "\n[b]Silizium:[/b] "
				+ functions::nf(functions::d2s(this->f->getResCrystal()))
				+ "\n[b]PVC:[/b] "
				+ functions::nf(functions::d2s(this->f->getResPlastic()))
				+ "\n[b]Tritium:[/b] "
				+ functions::nf(functions::d2s(this->f->getResFuel()))
				+ "\n[b]Nahrung:[/b] "
				+ functions::nf(functions::d2s(this->f->getResFood()))
				+ "\n[b]Bewohner:[/b] "
				+ functions::nf(functions::d2s(this->f->getResPeople()))
				+ "\n";
			
			this->targetEntity->addResMetal(this->f->unloadResMetal());
			this->targetEntity->addResCrystal(this->f->unloadResCrystal());
			this->targetEntity->addResPlastic(this->f->unloadResPlastic());
			this->targetEntity->addResFuel(this->f->unloadResFuel());
			this->targetEntity->addResFood(this->f->unloadResFood());
			this->targetEntity->addResPower(this->f->unloadResPower());
			this->targetEntity->addResPeople(this->f->unloadResPeople());
			
			// Flotte stationieren
			if (this->f->getCount()) {
				DataHandler &DataHandler = DataHandler::instance();
				query << "INSERT INTO " 
						<< "	`shiplist` "
						<< "(`shiplist_user_id` , "
						<< "	`shiplist_ship_id` , "
						<< "	`shiplist_entity_id` , "
						<< "	`shiplist_count` , "
						<< "	`shiplist_special_ship` , "
						<< "	`shiplist_special_ship_level` , "
						<< "	`shiplist_special_ship_exp` , "
						<< "	`shiplist_special_ship_bonus_weapon` , "
						<< "	`shiplist_special_ship_bonus_structure` , "
						<< "	`shiplist_special_ship_bonus_shield` , "
						<< "	`shiplist_special_ship_bonus_heal` , "
						<< "	`shiplist_special_ship_bonus_capacity` , "
						<< "	`shiplist_special_ship_bonus_speed` , "
						<< "	`shiplist_special_ship_bonus_pilots` , "
						<< "	`shiplist_special_ship_bonus_tarn` , "
						<< "	`shiplist_special_ship_bonus_antrax` , "
						<< "	`shiplist_special_ship_bonus_forsteal` , "
						<< "	`shiplist_special_ship_bonus_build_destroy` , "
						<< "	`shiplist_special_ship_bonus_antrax_food` , "
						<< "	`shiplist_special_ship_bonus_deactivade` "
						<< ") VALUES ";
				
				std::vector<Object*>::iterator ot;
				int set = false;
				for (ot = this->f->objects.begin() ; ot < this->f->objects.end(); ot++) {
					if ((*ot)->getCount()) {
						if (set) query << ",";
						set = true;
						query << "('" << this->f->getUserId() << "', '";
						query << (*ot)->getTypeId() << "', '";
						query << this->targetEntity->getId() << "', '";
						query << (*ot)->getCount() << "', '";
						query << (*ot)->getSpecial() << "', '";
						query << (*ot)->getSLevel() << "', '";
						query << (*ot)->getSExp() << "', '";
						query << (*ot)->getSBonusWeapon() << "', '";
						query << (*ot)->getSBonusStructure() << "', '";
						query << (*ot)->getSBonusShield() << "', '";
						query << (*ot)->getSBonusHeal() << "', '";
						query << (*ot)->getSBonusCapacity() << "', '";
						query << (*ot)->getSBonusSpeed() << "', '";
						query << (*ot)->getSBonusPilots() << "', '";
						query << (*ot)->getSBonusTarn() << "', '";
						query << (*ot)->getSBonusAntrax() << "', '";
						query << (*ot)->getSBonusForsteal() << "', '";
						query << (*ot)->getSBonusBuildDestroy() << "', '";
						query << (*ot)->getSBonusAntraxFood() << "', '";
						query << (*ot)->getSBonusDeactivade() << "' ";
						query << ")";
						
						ShipData::ShipData *data = DataHandler.getShipById((*ot)->getTypeId());
						this->msgShips += "\n[b]"
										+ data->getName()
										+ ":[/b] "
										+ functions::nf(functions::d2s((*ot)->getCount()));
					}
				}
				query << " ON DUPLICATE KEY "
						<< "	UPDATE shiplist.`shiplist_count` = shiplist.`shiplist_count` + VALUES(shiplist.`shiplist_count`);";
				query.store();
				query.reset();
				
			}
			if (this->msgShips=="") {
				this->msgShips = "\n\n[b]SCHIFFE[/b]\n[i]Keine weiteren Schiffe in der Flotte![/i]\n";					
			}
			else {
				this->msgShips = "\n\n[b]SCHIFFE[/b]\n" + this->msgShips + "\n";
			}
			
			//Delete Fleet
			this->f->setPercentSurvive(0);
		}
		
		//Waren werden ausgeladen
		else if(fleetAction==2) {
			//Rohstoffnachricht für den User
			this->msgRes += "\n\n[b]WAREN[/b]\n\n[b]Titan:[/b] "
				+ functions::nf(functions::d2s(this->f->getResMetal()))
				+ "\n[b]Silizium:[/b] "
				+ functions::nf(functions::d2s(this->f->getResCrystal()))
				+ "\n[b]PVC:[/b] "
				+ functions::nf(functions::d2s(this->f->getResPlastic()))
				+ "\n[b]Tritium:[/b] "
				+ functions::nf(functions::d2s(this->f->getResFuel()))
				+ "\n[b]Nahrung:[/b] "
				+ functions::nf(functions::d2s(this->f->getResFood()))
				+ "\n[b]Bewohner:[/b] "
				+ functions::nf(functions::d2s(this->f->getResPeople()))
				+ "\n";
			
			this->targetEntity->addResMetal(this->f->unloadResMetal());
			this->targetEntity->addResCrystal(this->f->unloadResCrystal());
			this->targetEntity->addResPlastic(this->f->unloadResPlastic());
			this->targetEntity->addResFuel(this->f->unloadResFuel(false));
			this->targetEntity->addResFood(this->f->unloadResFood(false));
			this->targetEntity->addResPower(this->f->unloadResPower());
			this->targetEntity->addResPeople(this->f->unloadResPeople(false));
		}
		//Fehler, die Flotte hat eine ungültige Aktion
		else {
			this->msgRes = "Fehler, die Flotte hat eine ungültige Aktion!<br>";
		}
	}
