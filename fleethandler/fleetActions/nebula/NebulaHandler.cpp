#include <iostream>

#include <mysql++/mysql++.h>

#include "NebulaHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace nebula
{
	void NebulaHandler::update()
	{
	
		/**
		* Fleet action: Collect nebula gas
		*/
		Config &config = Config::instance(); //ToDo init time

		// ist das nebel feld noch vorhanden?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ressources ";
		query << "FROM ";
		query << "nebulas "; 
		query << "WHERE ";
		query << "	id='" << fleet_["fleet_target_to"] << "';";
		mysqlpp::Result nebulaRes = query.store();
		query.reset();
		
		if (nebulaRes)
		{
			int nebulaSize = nebulaRes.size();
			
			if (nebulaSize > 0)
			{
				mysqlpp::Row nebulaRow = nebulaRes.at(0);
				
                double capa = min((int)fleet_["fleet_capacity_nebula"],(int)fleet_["fleet_capacity"]);

                //80% Chance das das sammeln klappt
                double goOrNot=mt_rand(1,100); //ToDo
                if (goOrNot>20) //ToDo Config
                {

                    double maxRess = (int)nebulaRow["resources"];

                    double nebula = mt_rand(1000,capa); //ToDo
                    double crystal = round(min(nebula,maxRess));
					double capacity = (double)fleet_["fleet_capacity"]-crystal;

                    double resTotal = (int)nebulaRow["resources"] -crystal;

                    query << "UPDATE ";
					query << "	nebulas ";
					query << "SET ";
					query << "	resources=resources-'" << resTotal << "' ";
					query << "WHERE ";
					query << "	id='" << fleet_["fleet_taret_to"] << "';";
					query.store();
					query.reset();


                    //
                    //Wenn nebula feld keine ress mehr hat -> löschen und neues erstellen
                    //
                    query << "SELECT ";
					query << "	resource ";
					query << "FROM ";
					query << "	nebulas ";
					query << "WHERE ";
					query << "id='" << fleet_["fleet_target_to"] << "';";
					mysqlpp::Result checkRes = query.store();
					query.reset();
					
					if (checkRes)
					{
						int checkSize = checkRes.size();
						
						if (checkSize > 0)
						{
							mysqlpp::Row checkRow = checkRes.at(0);
							
							if ((int)checkRow["ressources"]<1000)
							{
								// altes "löschen" //
								query << "UPDATE ";
								query << "	entities "
								query << "SET ";
								query << "	type=e ";
								query << "WHERE ";
								query << "	id='" << fleet_["fleet_target_to"] << "';";
								query.store();
								query.reset();
								
								//nebula löschen
								query << "DELETE ";
								query << "	nebulas ";
								query << "WHERE ";
								query << " id='" << fleet_["fleet_target_to"] << "';";
								query.store();
								query.reset();

								// neues erstellen //
								double newRes = mt_rand(config.nget("nebula_ress",1),config.nget("nebula_ress",2)); //ToDo

                                // hat es noch leere felder?
								query << "SELECT ";
								query << "	id ";
								query << "FROM ";
								query << "	entities ";
								query << "WHERE ";
								query << "	type=e ";
								query << "ORDER BY ";
								query << " RAND() ";
								query << "LIMIT 1;";
								mysqlpp::Result searchRes = query.store();
								query.reset();
								
								if (searchRes)
								{
									int searchSize = searchRes.size();
									
									//wenn ja...
									if (searchsize > 0)
									{
										mysqlpp::Row searchRow = searchRes.at(0);

										// neues erstellen
										query << "UPDATE ";
										query << "	entities ";
										query << "SET ";
										query << "	type=n ";
										query << "WHERE ";
										query << "	id='" << searchRow["id"] << "';";
										query.store();
										query.reset();
										
										query << "INSERT INTO ";
										query << "(";
										query << "	id, ";
										query << "	resources ";
										query << ") ";
										query << "VALUES ";
										query << "(";
										query << "'" << searchRow["id"] << "'";
										query << "'" << newRes << "';";
										query.store();
										query.reset();
									}
								}
							}

							//Summiert Rohstoffe zu der Ladung der Flotte
							crystal += fleet_["fleet_res_crystal"];

							// Flotte zurückschicken
							fleetReturn("nr","",crystal,"","","","",capacity);

							//Nachricht senden
							std::string msg = "Eine Flotte vom Planeten \n[b]";
							msg += functions::formatCoords((int)["fleet_target_from"]);
							msg +="[/b]\nhat [b]ein Intergalaktisches Nebelfeld [/b]\num [b]";
							msg += functions::formatTime((int)fleet_["fleet_landtime"]);
							msg += "[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
							std:stringmsgRes = "\n[b]ROHSTOFFE:[/b]\n\nSilizium: ";
							msgRes += functions::nf(crystal);
							msgRes += "\n";
							msg += msgRes;
							
							functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_MISC_MSG_CAT_ID,"Nebelfeld erkunden",msg);

							//Erbeutete Rohstoffsumme speichern
							query << "UPDATE ";
							query << "	users ";
							query << "SET ";
							query << "	user_res_from_nebula=user_res_from_nebula+'" << crystal << "' ";
							query << "WHERE ";
							query << "	user_id='" << fleet_["fleet_user_id"] << "';";
							query.store();
							query.reset(); 

							//Log schreiben
							std::string log = "Eine Flotte des Spielers [B]";
							log += functions::getUserNick((int)fleet_["fleet_user_id"]);
							log += "[/B] vom Planeten [b]";
							log += functions::formatCoords((int)fleet_["fleet_planet_from"]);
							log += "[/b] at [b]ein Intergalaktisches Nebelfeld [/b] um [b]";
							log += functions::formatTime((int)fleet_["fleet_landtime"]);
							log += "[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
							log += msgRes;
							functions::addLog(13,log,time);
						}
					}
                }

                //20% Chance das die flotte zerstört wird
                else
                {
                	//Nachricht senden
                    std::string msg = "Eine Flotte vom Planeten \n[b]";
					msg += functions::formatCoords((int)fleet_["fleet_target_from"]);
					msg += "[/b]\n hatte bei ihrer Erkundung eines Intergalaktischen Nebelfeldes eine starke magnetische Störung, welche zu einem Systemausfall führte.\nZu der Flotte ist jeglicher Kontakt abgebrochen.";
                    
					functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_MISC_MSG_CAT_ID,"Flotte verschollen",msg);

                    //Log schreiben
					std::string log = "Eine Flotte des Spielers [B]";
					log += functions::getUserNick((int)fleet_["fleet_user_id"]);
					log += "[/B] vom Planeten [b]";
					log += functions::formatCoords((int)fleet_["fleet_target_from"]);
					log += "[/b] wurde bei einem Intergalaktisches Nebelfeld zerst&ouml;rt.";
                    functions::addLog(13,log,time);

                    // Flotte-Schiffe-Verknüpfungen löschen
					deleteFleet();
				}
			}

      		// nebula feld nicht mehr vorhanden
			else
			{
            	// Flotte zurückschicken
                fleetReturn("nr");

				//Nachricht senden
                std::string msg = "Die Flotte vom Planeten \n[b]";
				msg += functions::formatCoords((int)fleet_["fleet_target_from"]);
				msg += "[/b]\n konnte kein Intergalaktisches Nebelfeld orten.\n";
				
                functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_MISC_MSG_CAT_ID,"Nebelfeld verschwunden",msg);

                //Log schreiben
				std::string log = "Eine Flotte des Spielers [B]";
				log += functions::getUserNick((int)fleet_["fleet_user_id"]);
				log += "[/B] vom Planeten [b]";
				log += functions::formatCoords((int)fleet_["fleet_planet_from"]);
				log += "[/b] konnte kein Intergalaktisches Nebelfeld orten.";
                add_log(13,log,time);
			}
		}
	}
}

