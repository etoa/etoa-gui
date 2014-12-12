
#include "FleetAction.h"

void FleetAction::fleetLand(int fleetAction)
{
    mysqlpp::Query query = con_->query();

    //Flotte wird stationiert und Waren werden ausgeladen
    if (fleetAction==1)
    {
        this->targetEntity->addResMetal(this->f->unloadResMetal());
        this->targetEntity->addResCrystal(this->f->unloadResCrystal());
        this->targetEntity->addResPlastic(this->f->unloadResPlastic());
        this->targetEntity->addResFuel(this->f->unloadResFuel(true));
        this->targetEntity->addResFood(this->f->unloadResFood(true));
        this->targetEntity->addResPower(this->f->unloadResPower());
        this->targetEntity->addResPeople(this->f->unloadResPeople(true));

        // Flotte stationieren
        if (this->f->getCount())
	{
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
            for (ot = this->f->objects.begin() ; ot < this->f->objects.end(); ot++)
	    {
                if ((*ot)->getCount())
		{
                    if (set) query << ",";
                    set = true;
                    query << "('" << this->targetEntity->getUserId() << "', '";
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
                }
            }
            query << " ON DUPLICATE KEY "
            << "	UPDATE "
            << "		shiplist.`shiplist_count` = shiplist.`shiplist_count` + VALUES(shiplist.`shiplist_count`),"
            << "		shiplist.`shiplist_special_ship_level` = VALUES(shiplist.`shiplist_special_ship_level`) , "
            << "		shiplist.`shiplist_special_ship_exp` = VALUES(shiplist.`shiplist_special_ship_exp`) , "
            << "		shiplist.`shiplist_special_ship_bonus_weapon` = VALUES(shiplist.`shiplist_special_ship_bonus_weapon`) , "
            << "		shiplist.`shiplist_special_ship_bonus_structure` = VALUES(shiplist.`shiplist_special_ship_bonus_structure`) , "
            << "		shiplist.`shiplist_special_ship_bonus_shield` = VALUES(shiplist.`shiplist_special_ship_bonus_shield`) , "
            << "		shiplist.`shiplist_special_ship_bonus_heal` = VALUES(shiplist.`shiplist_special_ship_bonus_heal`) , "
            << "		shiplist.`shiplist_special_ship_bonus_capacity` = VALUES(shiplist.`shiplist_special_ship_bonus_capacity`) , "
            << "		shiplist.`shiplist_special_ship_bonus_speed` = VALUES(shiplist.`shiplist_special_ship_bonus_speed`) , "
            << "		shiplist.`shiplist_special_ship_bonus_pilots` = VALUES(shiplist.`shiplist_special_ship_bonus_pilots`) , "
            << "		shiplist.`shiplist_special_ship_bonus_tarn` = VALUES(shiplist.`shiplist_special_ship_bonus_tarn`) , "
            << "		shiplist.`shiplist_special_ship_bonus_antrax` = VALUES(shiplist.`shiplist_special_ship_bonus_antrax`) , "
            << "		shiplist.`shiplist_special_ship_bonus_forsteal` = VALUES(shiplist.`shiplist_special_ship_bonus_forsteal`) , "
            << "		shiplist.`shiplist_special_ship_bonus_build_destroy` = VALUES(shiplist.`shiplist_special_ship_bonus_build_destroy`) , "
            << "		shiplist.`shiplist_special_ship_bonus_antrax_food` = VALUES(shiplist.`shiplist_special_ship_bonus_antrax_food`) , "
            << "		shiplist.`shiplist_special_ship_bonus_deactivade` = VALUES(shiplist.`shiplist_special_ship_bonus_deactivade`);";
            query.store();
            query.reset();
        }
        //Delete Fleet
        this->f->setPercentSurvive(0);
    }

    //Waren werden ausgeladen
    else if (fleetAction==2)
    {
        this->targetEntity->addResMetal(this->f->unloadResMetal());
        this->targetEntity->addResCrystal(this->f->unloadResCrystal());
        this->targetEntity->addResPlastic(this->f->unloadResPlastic());
        this->targetEntity->addResFuel(this->f->unloadResFuel(false));
        this->targetEntity->addResFood(this->f->unloadResFood(false));
        this->targetEntity->addResPower(this->f->unloadResPower());
        this->targetEntity->addResPeople(this->f->unloadResPeople(false));
    }
}
