#include <iostream>
#include <string>

#include <mysql++/mysql++.h>

#include "FleetHandler.h"
#include "MysqlHandler.h"
#include "functions/Functions.h"


	void FleetHandler::fleetLand(int fleetAction,bool alreadyColonialized,bool alreadyInvaded)
	{
		
		mysqlpp::Query query = con_->query();
		
		//Flotte wird stationiert und Waren werden ausgeladen
		if(fleetAction==1) {
            // Waren entladen
            double people = this->f->getPilots() + this->f->getResPeople();
			query << std::setprecision(18);
            query << "UPDATE ";
			query << "	planets ";
			query << "SET ";
			query << "	planet_res_metal=planet_res_metal+'" << this->f->getResMetal() << "', ";
			query << "	planet_res_crystal=planet_res_crystal+'" << this->f->getResCrystal() << "', ";
			query << "	planet_res_plastic=planet_res_plastic+'" << this->f->getResPlastic() << "', ";
			query << "	planet_res_fuel=planet_res_fuel+'" << this->f->getResFuel() << "', ";
			query << "	planet_res_food=planet_res_food+'" << this->f->getResFood() << "', ";
			query << "	planet_people=planet_people+'" << people << "' ";
			query << "WHERE ";
			query << "	id='" << this->f->getEntityTo() << "';";
			query.store();
			query.reset();
			
			//Rohstoffnachricht für den User
			msgRes= "\n\n[b]WAREN[/b]\n\n[b]Titan:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_metal"]));
			msgRes += "\n[b]Silizium:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_crystal"]));
			msgRes += "\n[b]PVC:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_plastic"]));
			msgRes += "\n[b]Tritium:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_fuel"]));
			msgRes += "\n[b]Nahrung:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_food"]));
			msgRes += "\n[b]Bewohner:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_people"]));
			msgRes += "\n";
			
			// Flotte stationieren
            // Laden der Schiffsdaten
            query << "SELECT ";
			query << "	fs.fs_ship_cnt, ";
			query << "	fs.fs_ship_id, ";
			query << "	fs.fs_special_ship, ";
			query << "	fs.fs_special_ship_level, ";
			query << "	fs.fs_special_ship_exp, ";
			query << "	fs.fs_special_ship_bonus_weapon, ";
			query << "	fs.fs_special_ship_bonus_structure, ";
			query << "	fs.fs_special_ship_bonus_shield, ";
			query << "	fs.fs_special_ship_bonus_heal, ";
			query << "	fs.fs_special_ship_bonus_capacity, ";
			query << "	fs.fs_special_ship_bonus_speed, ";
			query << "	fs.fs_special_ship_bonus_pilots, ";
			query << "	fs.fs_special_ship_bonus_tarn, ";
			query << "	fs.fs_special_ship_bonus_antrax, ";
			query << "	fs.fs_special_ship_bonus_forsteal, ";
			query << "	fs.fs_special_ship_bonus_build_destroy, ";
			query << "	fs.fs_special_ship_bonus_antrax_food, ";
			query << "	fs.fs_special_ship_bonus_deactivade, ";
			query << "	s.ship_name, ";
			query << "	s.ship_actions ";
			query << "FROM ";
			query << "	fleet_ships AS fs ";
			query << "INNER JOIN ";
			query << "	ships AS s ON fs.fs_ship_id = s.ship_id ";
			query << "	AND fs.fs_fleet_id='" << this->f->getId() << "' ";
			query << "		AND fs.fs_ship_faked='0'; ";
			mysqlpp::Result fsRes = query.store();
			query.reset();
			
			if (fsRes) {
				int fsSize = fsRes.size();
				
				if (fsSize > 0) {
					mysqlpp::Row fsRow;
					msgShips = "";
					
					for (mysqlpp::Row::size_type i = 0; i<fsSize; i++)  {
						fsRow = fsRes.at(i);
						
						double shipCnt = (double)fsRow["fs_ship_cnt"];
						
						//Kolonisieren und Invasieren aus den Schiffsaktionen auslesen
						bool canColonize = 0;
						bool canInvade = 0;
						char str[] = "";
						strcpy( str, fsRow["ship_actions"]);
						char * pch;
						pch = strtok (str,",");
						do
						{
							if (!strcmp(pch,"colonize"))
								canColonize = 1;
							else if (!strcmp(pch,"invade"))
								canInvade = 1;
							pch = strtok (NULL, ",");
						} while  (pch != NULL);
						
						// Ein Koloschiff subtrahieren, falls kolonialisieren gewählt ist (einmalig)
						if (canColonize==1 && alreadyColonialized==0 && this->f->getAction()=="colonize") {
							shipCnt = (double)fsRow["fs_ship_cnt"]-1;
							alreadyColonialized=1;
						}

						// Ein Invasionsschiff subtrahieren, falls invasieren gewählt ist (einmalig)
						if (canInvade==1 && alreadyInvaded==0 && this->f->getAction()=="invade") {
							shipCnt = (double)fsRow["fs_ship_cnt"]-1;
							alreadyInvaded=1;
						}
						
						//Sucht einen bestehenden Datensatz auf dem Zielplanet aus
						//Achtung: In dem Query darf NICHT auch noch nach der User-ID gefragt werden, weil Handelsschiffe die User-ID=0 haben!
						query << "SELECT ";
						query << "	shiplist_id ";
						query << "FROM ";
						query << "	shiplist ";							
						query << "WHERE ";
						query << "	shiplist_ship_id='" << fsRow["fs_ship_id"] << "' ";
						query << "	AND shiplist_entity_id='" << this->f->getEntityTo() << "';";
						mysqlpp::Result slRes = query.store();
						query.reset();
						
						if (slRes) {
							int slSize = slRes.size();
							
							if (slSize > 0) {
								mysqlpp::Row slRow = slRes.at(0);
								
								//Bestehender Datensatz gefunden -> Stationiert die Schiffe mit all ihren Werten (Update)
								query << "UPDATE ";
								query << "	shiplist ";
								query << "SET ";
								query << "	shiplist_count=shiplist_count+'" << shipCnt << "', ";
								query << "	shiplist_special_ship='" << fsRow["fs_special_ship"] << "', ";
								query << "	shiplist_special_ship_level='" << fsRow["fs_special_ship_level"] << "', ";
								query << "	shiplist_special_ship_exp='" << fsRow["fs_special_ship_exp"] << "', ";
								query << "	shiplist_special_ship_bonus_weapon='" << fsRow["fs_special_ship_bonus_weapon"] << "', ";
								query << "	shiplist_special_ship_bonus_structure='" << fsRow["fs_special_ship_bonus_structure"] << "', ";
								query << "	shiplist_special_ship_bonus_shield='" << fsRow["fs_special_ship_bonus_shield"] << "', ";
								query << "	shiplist_special_ship_bonus_heal='" << fsRow["fs_special_ship_bonus_heal"] << "', ";
								query << "	shiplist_special_ship_bonus_capacity='" << fsRow["fs_special_ship_bonus_capacity"] << "', ";
								query << "	shiplist_special_ship_bonus_speed='" << fsRow["fs_special_ship_bonus_speed"] << "', ";
								query << "	shiplist_special_ship_bonus_pilots='" << fsRow["fs_special_ship_bonus_pilots"] << "', ";
								query << "	shiplist_special_ship_bonus_tarn='" << fsRow["fs_special_ship_bonus_tarn"] << "', ";
								query << "	shiplist_special_ship_bonus_antrax='" << fsRow["fs_special_ship_bonus_antrax"] << "', ";
								query << "	shiplist_special_ship_bonus_forsteal='" << fsRow["fs_special_ship_bonus_forsteal"] << "', ";
								query << "	shiplist_special_ship_bonus_build_destroy='" << fsRow["fs_special_ship_bonus_build_destroy"] << "', ";
								query << "	shiplist_special_ship_bonus_antrax_food='" << fsRow["fs_special_ship_bonus_antrax_food"] << "', ";
								query << "	shiplist_special_ship_bonus_deactivade='" << fsRow["fs_special_ship_bonus_deactivade"] << "' ";
								query << "WHERE ";
								query << "	shiplist_id='" << slRow["shiplist_id"] << "';";
								query.store();
								query.reset();
							}
							//Keinen bestehenden Datensatz gefunden -> Stationiert die Schiffe mit all ihren Werten (Insert)
							else {
								int userId;
								
								//überprüft, ob die Flotte eine User ID besitzt, sonst eine generieren durch Planet ID (z.b. für Handelsschiffe)
								if(this->f->getUserId()!=0) {
									userId = this->f->getUserId();
								}
								else {
									userId = this->f->getEntityToUserId();
								}
								
								query << "INSERT INTO ";
								query << "	shiplist ( ";
								query << "	shiplist_user_id, ";
								query << "	shiplist_ship_id, ";
								query << "	shiplist_entity_id, ";
								query << "	shiplist_count, ";
								query << "	shiplist_special_ship, ";
								query << "	shiplist_special_ship_level, ";
								query << "	shiplist_special_ship_exp, ";
								query << "	shiplist_special_ship_bonus_weapon, ";
								query << "	shiplist_special_ship_bonus_structure, ";
								query << "	shiplist_special_ship_bonus_shield, ";
								query << "	shiplist_special_ship_bonus_heal, ",
								query << "	shiplist_special_ship_bonus_capacity, ";
								query << "	shiplist_special_ship_bonus_speed, ";
								query << "	shiplist_special_ship_bonus_pilots, ";
								query << "	shiplist_special_ship_bonus_tarn, ";
								query << "	shiplist_special_ship_bonus_antrax, ";
								query << "	shiplist_special_ship_bonus_forsteal, ";
								query << "	shiplist_special_ship_bonus_build_destroy, ";
								query << "	shiplist_special_ship_bonus_antrax_food, ";
								query << "	shiplist_special_ship_bonus_deactivade  ";
								query << ") ";
								query << "VALUES ( ";
								query << "	'" << userId << "', ";
								query << "	'" << fsRow["fs_ship_id"] << "', ";
								query << "	'" << this->f->getEntityTo() << "', ";
								query << "	'" << shipCnt << "', ";
								query << "	'" << fsRow["fs_special_ship"] << "', ";
								query << "	'" << fsRow["fs_special_ship_level"] << "', ";
								query << "	'" << fsRow["fs_special_ship_exp"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_weapon"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_structure"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_shield"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_heal"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_capacity"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_speed"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_pilots"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_tarn"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_antrax"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_forsteal"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_build_destroy"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_antrax_food"] << "', ";
								query << "	'" << fsRow["fs_special_ship_bonus_deactivade"] << "' ";
								query << ");";
								query.store();
								query.reset();
							}
						}
						
						//Schreibt alle Schiffe mit deren Anzahl in ein Array (für Nachricht an den User)
						if (shipCnt>0) {
							msgShips += "\n[b]";
							msgShips += std::string(fsRow["ship_name"]);
							msgShips += ":[/b] ";
							msgShips += functions::nf(functions::d2s(shipCnt));
						}
					}
					if (msgShips=="") {
						msgShips = "\n\n[b]SCHIFFE[/b]\n[i]Keine weiteren Schiffe in der Flotte![/i]\n";					
					}
					else {
						msgAllShips = "\n\n[b]SCHIFFE[/b]\n";
						msgAllShips += msgShips;
						msgAllShips += "\n";
					}
				}
			}
		}
		
		//Waren werden ausgeladen
		else if(fleetAction==2) {
            // Waren entladen
            double people = this->f->getPilots() + this->f->getResPeople();
			query << std::setprecision(18);
            query << "UPDATE ";
			query << "	planets ";
			query << "SET ";
			query << "	planet_res_metal=planet_res_metal+'" << this->f->getResMetal() << "', ";
			query << "	planet_res_crystal=planet_res_crystal+'" << this->f->getResCrystal() << "', ";
			query << "	planet_res_plastic=planet_res_plastic+'" << this->f->getResPlastic() << "', ";
			query << "	planet_res_fuel=planet_res_fuel+'" << this->f->getResFuel() << "', ";
			query << "	planet_res_food=planet_res_food+'" << this->f->getResFood() << "', ";
			query << "	planet_people=planet_people+'" << people << "' ";
			query << "WHERE ";
			query << "	id='" << this->f->getEntityTo() << "';";
			query.store();
			query.reset();

			//Rohstoffnachricht für den User
			msgRes= "\n\n[b]WAREN[/b]\n\n[b]Titan:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_metal"]));
			msgRes += "\n[b]Silizium:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_crystal"]));
			msgRes += "\n[b]PVC:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_plastic"]));
			msgRes += "\n[b]Tritium:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_fuel"]));
			msgRes += "\n[b]Nahrung:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_food"]));
			msgRes += "\n[b]Bewohner:[/b] ";
			msgRes += functions::nf(std::string(fleet_["res_people"]));
			msgRes += "\n";
		}
		//Fehler, die Flotte hat eine ungültige Aktion
		else {
			msgRes = "Fehler, die Flotte hat eine ungültige Aktion!<br>";
		}
	}


	void FleetHandler::fleetReturn(int status,double resMetal,double resCrystal,double resPlastic,double resFuel,double resFood,double resPeople)
	{

        // Flotte zurückschicken
		int duration = (int)fleet_["landtime"] - (int)fleet_["launchtime"];
        int launchtime = (int)fleet_["landtime"];
        int landtime = launchtime + duration;
		
		mysqlpp::Query query = con_->query();
		query << std::setprecision(18);
		query << "UPDATE ";
		query << "	fleet ";
		query << "SET ";
		query << "	entity_from='" << this->f->getEntityTo() << "', ";
		query << "	entity_to='" << this->f->getEntityFrom() << "', ";
		query << "	status='" << status << "', ";
		query << "	launchtime='" << launchtime << "', ";
		query << "	landtime='" << landtime << "' ";
		
			if (resMetal>-1) 
				query << ", res_metal='" << resMetal << "'";
			if (resCrystal>-1) 
				query << ", res_crystal='" << resCrystal << "'";
			if (resPlastic>-1) 
				query << ", res_plastic='" << resPlastic << "'";
			if (resFuel>-1) 
				query << ", res_fuel='" << resFuel << "'";
			if (resFood>-1) 
				query << ", res_food='" << resFood << "'";
			if (resPeople>-1) 
				query << ", res_people='" << resPeople << "'";
		
		query << " WHERE ";
		query << "	id='" << this->f->getId() << "' ";
		query << "	OR leader_id='" << this->f->getId() << "';";
		query.store();
		query.reset();
	}

	void FleetHandler::fleetDelete()
	{
		// Flotte-Schiffe-Verknüpfungen löschen
		mysqlpp::Query query = con_->query();
		query << "DELETE FROM ";
		query << "	fleet_ships ";
		query << "WHERE ";
		query << "	fs_fleet_id='" << this->f->getId() << "';";
		query.store();
		query.reset();
		
		// Flotte aufheben
		query << "DELETE FROM ";
		query << "	fleet ";
		query << "WHERE ";
		query << "	id='" << this->f->getId() << "';";
		query.store();
		query.reset();			
	}
	
	void FleetHandler::fleetSendMain(int userId)
	{
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	planets.id ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		if (userId==0)
			query << "	planets.planet_user_id='" << this->f->getUserId() << "' ";
		else
			query << "	planets.planet_user_id='" << userId << "' ";
		query << "	AND planets.planet_user_main='1';";
		mysqlpp::Result mainRes = query.store();
		query.reset();
					
		if (mainRes) {
			int mainSize = mainRes.size();
			
			if (mainSize > 0) {
				int landtime = 2 * this->f->getLandtime() - this->f->getLaunchtime();
				mysqlpp::Row mainRow = mainRes.at(0);
				query << "UPDATE ";
				query << "	fleet ";
				query << "SET ";
				query << "	entity_from=entity_to, ";
				query << " entity_to='" << mainRow["id"] << "', ";
				query << "	launchtime=landtime, ";
				query << "	landtime='" << landtime << "', ";
				query << "	status='2' ";
				query << "WHERE ";
				query << "	id='" << this->f->getId() << "';";
				query.store();
				query.reset();
			}
		}
	}
