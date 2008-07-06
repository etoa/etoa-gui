#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "AsteroidHandler.h"
#include "../../MysqlHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../functions/Functions.h"

namespace asteroid
{
	void AsteroidHandler::update()
	{
	
		/**
		* Fleet-Action: Collect asteroids
		*/ 

		//Init
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		std::string action = "collectmetal";

		// Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND (";
		query << "		ship_actions LIKE '%," << action << "'";
		query << "		OR ship_actions LIKE '" << action << ",%'";
		query << "		OR ship_actions LIKE '%," << action << ",%'";
		query << "		OR ship_actions LIKE '" << action << "');";
		mysqlpp::Result fsRes = query.store();
		query.reset();
							
		if (fsRes)
		{
			int fsSize = fsRes.size();
			
			if (fsSize > 0)
			{
				mysqlpp::Row fsRow = fsRes.at(0);
				// ist das asteroiden feld noch vorhanden?
				query << "SELECT ";
				query << " res_metal, ";
				query << " res_crystal, ";
				query << " res_plastic, ";
				query << " res_fuel, ";
				query << " res_food, ";
				query << " res_power ";
				query << "FROM ";
				query << "	asteroids ";
				query << "WHERE ";
				query << "	id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result asteroidRes = query.store();
				query.reset();
				
				if (asteroidRes)
				{
					int asteroidSize = asteroidRes.size();
			
					if (asteroidSize > 0)
					{
						mysqlpp::Row asteroidRow = asteroidRes.at(0);

						double fleetCapa;
						query << "SELECT ";
						query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
						query << "FROM ";
						query << "	fleet_ships ";
						query << "INNER JOIN ";
						query << "	ships ON fs_ship_id = ship_id ";
						query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
						query << "	AND fs_ship_faked='0';";
						mysqlpp::Result capaRes = query.store();
						query.reset();	
						
						if (capaRes)
						{
							int capaSize = capaRes.size();
							
							if (capaSize > 0)
							{
								mysqlpp::Row capaRow = capaRes.at(0);
								fleetCapa = (double)capaRow["capa"] - (double)fleet_["res_metal"] - (double)fleet_["res_crystal"] - (double)fleet_["res_plastic"] - (double)fleet_["res_fuel"] - (double)fleet_["res_food"];
							}
						}

						double capa = std::min(fleetCapa,(double)fsRow["capa"]);
						capa /= 3;

						//80% Chance das das sammeln klappt
						double goOrNot=rand()%101;
				
						if (goOrNot > config.nget("asteroid_action",0) * 100)
						{

							int asteroid = config.nget("asteroid_action",1) + (rand() % (int)(capa - config.nget("asteroid_action",1) + 1));
							int metal = round(std::min(asteroid,(int)asteroidRow["res_metal"]));
					
							asteroid = config.nget("asteroid_action",1) + (rand() % (int)(capa - config.nget("asteroid_action",1) + 1));
							int crystal = round(std::min(asteroid,(int)asteroidRow["res_crystal"]));
					
							asteroid = config.nget("asteroid_action",1) + (rand() % (int)(capa - config.nget("asteroid_action",1) + 1));
							int plastic = round(std::min(asteroid,(int)asteroidRow["res_plastic"]));

							query << "UPDATE ";
							query << "	asteroids ";
							query << "SET ";
							query << " res_metal = res_metal - '" << metal << "', ";
							query << " res_crystal = res_crystal - '" << crystal << "', ";
							query << " res_plastic = res_plastic - '" << plastic << "' ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							query.store();
							query.reset();

							//
							//Wenn Asteroidenfeld keine ress mehr hat -> löschen und neues erstellen
							//
							query << "SELECT ";
							query << " (res_metal+res_crystal+res_plastic+res_fuel+res_food) as res ";
							query << "FROM ";
							query << "	asteroids ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result checkRes = query.store();
							query.reset();
					
							if (checkRes)
							{
								int checkSize = checkRes.size();

								if (checkSize > 0)
								{
									mysqlpp::Row checkRow = checkRes.at(0);
									
									if ((int)checkRow["res"] < config.nget("asteroid_action",1))
									{
										// altes "löschen"
										query << "UPDATE ";
										query << "	entities ";
										query << "SET ";
										query << "	code='e' ";
										query << "WHERE ";
										query << "	id='" << fleet_["entity_to"] << "';";
										query.store();
										query.reset();
								
										//nebula löschen
										query << "DELETE FROM";
										query << "	asteroids ";
										query << "WHERE ";
										query << " id='" << fleet_["entity_to"] << "';";
										query.store();
										query.reset();
								
										//space erstellen
										query << "INSERT INTO ";
										query << " space ";
										query << "(";
										query << "	id, ";
										query << "	lastvisited ";
										query << ") ";
										query << "VALUES ";
										query << "(";
										query << "'" << fleet_["entity_to"] << "', ";
										query << "'0');";
										query.store();
										query.reset();


										// neues erstellen //
										double metal = config.nget("asteroid_ress",1) + (rand() % (int)(config.nget("asteroid_ress",2) - config.nget("asteroid_ress",1) + 1));
										metal /= 3;
										double crystal = config.nget("asteroid_ress",1) + (rand() % (int)(config.nget("asteroid_ress",2) - config.nget("asteroid_ress",1) + 1));
										crystal /= 3;
										double plastic = config.nget("asteroid_ress",1) + (rand() % (int)(config.nget("asteroid_ress",2) - config.nget("asteroid_ress",1) + 1));
										plastic /= 3;
										
										// hat es noch leere felder?
										query << "SELECT ";
										query << "	id ";
										query << "FROM ";
										query << "	entities ";
										query << "WHERE ";
										query << "	code='e' ";
										query << "ORDER BY ";
										query << " RAND() ";
										query << "LIMIT 1;";
										mysqlpp::Result searchRes = query.store();
										query.reset();
								
										if (searchRes)
										{
											int searchSize = searchRes.size();
									
											//wenn ja...
											if (searchSize > 0)
											{
												mysqlpp::Row searchRow = searchRes.at(0);

												// neues erstellen
												query << "UPDATE ";
												query << "	entities ";
												query << "SET ";
												query << "	code='a' ";
												query << "WHERE ";
												query << "	id='" << searchRow["id"] << "';";
												query.store();
												query.reset();
										
												query << "INSERT INTO ";
												query << "	asteroids ";
												query << "(";
												query << "	id, ";
												query << "	res_metal, ";
												query << "	res_crystal, ";
												query << "	res_plastic ";
												query << ") ";
												query << "VALUES ";
												query << "(";
												query << "'" << searchRow["id"] << "',";
												query << "'" << metal << "' ";
												query << "'" << crystal << "' ";
												query << "'" << plastic << "');";
												query.store();
												query.reset();

												query << "DELETE FROM ";
												query << "	space ";
												query << "WHERE ";
												query << " id='" << searchRow["id"] << "';";
												query.store();
												query.reset();
											}
										}
									}
							
									//Summiert Rohstoffe zu der Ladung der Flotte
									metal += (double)fleet_["res_metal"];
									crystal += (double)fleet_["res_crystal"];
									plastic += (double)fleet_["res_plastic"];
									
									int sum = metal + crystal + plastic;
								
									// Flotte zurückschicken
									fleetReturn(1,metal,crystal,plastic,-1,-1,-1);

									//Nachricht senden
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += functions::formatCoords((int)fleet_["entity_from"],0);
									msg += "[/b]\nhat [b]ein Asteroidenfeld[/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n erreicht und Rohstoffe gesammelt.\n";
							
									msgRes = "\n[b]ROHSTOFFE:[/b]\n\nTitan: ";
									msgRes += functions::nf(functions::d2s(metal));
									msgRes += "\nSilizium: ";
									msgRes += functions::nf(functions::d2s(crystal));
									msgRes += "\nPVC: ";
									msgRes += functions::nf(functions::d2s(plastic));
									msgRes += "\n";
									msg += msgRes;
							
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Asteroiden gesammelt",msg);

									//Erbeutete Rohstoffsumme speichern
									query << "UPDATE ";
									query << "	users ";
									query << "SET ";
									query << "	user_res_from_asteroid=user_res_from_asteroid+'" << sum << "' ";
									query << "WHERE ";
									query << "	user_id='" << fleet_["user_id"] << "';";
									query.store();
									query.reset();  

									//Log schreiben
									std::string log = "Eine Flotte des Spielers [B]";
									log += functions::getUserNick((int)fleet_["user_id"]);
									log += "[/B] vom Planeten [b]";
									log += functions::formatCoords((int)fleet_["entity_from"],0);
									log += "[/b] hat [b]ein Asteroidenfeld[/b] um [b]",
									log += functions::formatTime((int)fleet_["landtime"]);
									log += "[/b]\n erreicht und Rohstoffe gesammelt.";
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
							msg += functions::formatCoords((int)fleet_["entity_from"],0);
							msg += "[/b]\n wurde bei einem Asteroidenfeld abgeschossen.";

							functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte abgeschossen",msg);

							//Log schreiben
							std::string log = "Eine Flotte des Spielers [B]";
							log += functions::getUserNick((int)fleet_["user_id"]),
							log += "[/B] vom Planeten [b]";
							log += functions::formatCoords((int)fleet_["entity_from"],0);
							log += "[/b] wurde bei einem Asteroidenfeld abgeschossen.";
					
							functions::addLog(13,log,time);

							// Flotte-Schiffe-Verknüpfungen löschen
							fleetDelete();
						}
					}
					// Asteroiden feld nicht mehr vorhanden
					else
					{
						// Flotte zurückschicken
						fleetReturn(1);

						// Nachricht senden
						std::string msg = "Die Flotte vom Planeten \n[b]";
						msg += functions::formatCoords((int)fleet_["entity_from"],0);
						msg += "[/b]\n fand kein Asteroidenfeld mehr vor.\n";
				
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Asteroidenfeld aufgelöst",msg);

						//Log schreiben
						std::string log = "Eine Flotte des Spielers [B]";
						log += functions::getUserNick((int)fleet_["user_id"]);
						log += "[/B] vom Planeten [b]";
						log += functions::formatCoords((int)fleet_["entity_from"],0);
						log += "[/b] fand kein Asteroidenfeld mehr vor.";

						functions::addLog(13,log,time);
					}
				}
			}
			else
			{
				std::string text = "Eine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["entity_from"],0);
				text += " versuchte, Asteroiden zu sammeln. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Asteroidensammeln gescheitert",text);
				
				fleetReturn(1);
			}
		}
	}
}
