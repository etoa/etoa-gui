#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "NebulaHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace nebula
{
	void NebulaHandler::update()
	{
	
		/**
		* Fleet action: Collect nebula gas
		*/

		Config &config = Config::instance();
		std::time_t time = std::time(0);
		std::string action = "collectcrystal";

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
				double nebulaCapa = (double)fsRow["capa"];
				if (nebulaCapa > 0)
				{
			
					// ist das nebel feld noch vorhanden?
					query << "SELECT ";
					query << "	resources ";
					query << "FROM ";
					query << "nebulas "; 
					query << "WHERE ";
					query << "	id='" << fleet_["entity_to"] << "';";
					mysqlpp::Result nebulaRes = query.store();
					query.reset();
		
					if (nebulaRes)
					{
						int nebulaSize = nebulaRes.size();
			
						if (nebulaSize > 0)
						{
							mysqlpp::Row nebulaRow = nebulaRes.at(0);
							double fleetCapa = 0;
							
							query << "SELECT ";
							query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
							query << "FROM ";
							query << "	fleet_ships ";
							query << "INNER JOIN ";
							query << "	ships ON fs_ship_id = ship_id ";
							query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
							query << "	AND fs_ship_faked='0' ";
							mysqlpp::Result capaRes = query.store();
							query.reset();
					
							if (capaRes)
							{
								int capaSize = capaRes.size();
			
								if (capaSize > 0)
								{
									mysqlpp::Row capaRow = capaRes.at(0);
									fleetCapa = (double)capaRow["capa"];
								}
							}
							
							double capa = std::min(fleetCapa,nebulaCapa);

							//80% Chance das das sammeln klappt
							double goOrNot = rand() % 101;
							std::cout << "go " << goOrNot << " -> " << config.nget("nebula_action",0) * 100 << "\n";
							if (goOrNot > config.nget("nebula_action",0) * 100)
							{

								double maxRess = (int)nebulaRow["resources"];
	
								double nebula = config.nget("nebula_action",1) + (rand() % (int)(capa - config.nget("nebula_action",1) + 1));
								double crystal = round(std::min(nebula,maxRess));

								double resTotal = (int)nebulaRow["resources"] -crystal;

								query << "UPDATE ";
								query << "	nebulas ";
								query << "SET ";
								query << "	resources=resources-'" << resTotal << "' ";
								query << "WHERE ";
								query << "	id='" << fleet_["entity_to"] << "';";
								query.store();
								query.reset();
								
								//
								//Wenn nebula feld keine ress mehr hat -> löschen und neues erstellen
								//
								query << "SELECT ";
								query << "	resources ";
								query << "FROM ";
								query << "	nebulas ";
								query << "WHERE ";
								query << "id='" << fleet_["entity_to"] << "';";
								mysqlpp::Result checkRes = query.store();
								query.reset();
					
								if (checkRes)
								{
									int checkSize = checkRes.size();
						
									if (checkSize > 0)
									{
										mysqlpp::Row checkRow = checkRes.at(0);
							
										if ((int)checkRow["resources"] < config.nget("nebula_action",1))
										{
											// altes "löschen" //
											query << "UPDATE ";
											query << "	entities ";
											query << "SET ";
											query << "	code=e ";
											query << "WHERE ";
											query << "	id='" << fleet_["entity_to"] << "';";
											query.store();
											query.reset();
								
											//nebula löschen
											query << "DELETE FROM";
											query << "	nebulas ";
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
											query << "'" << fleet_["entity_to"] << "' ";
											query << "'0';";
											query.store();
											query.reset();

											// neues erstellen //
											double newRes = config.nget("nebula_ress",1) + (rand() % (int)(config.nget("nebula_ress",2) - config.nget("nebula_ress",1) + 1));

											// hat es noch leere felder?
											query << "SELECT ";
											query << "	id ";
											query << "FROM ";
											query << "	entities ";
											query << "WHERE ";
											query << "	code=e ";
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
													query << "	code=n ";
													query << "WHERE ";
													query << "	id='" << searchRow["id"] << "';";
													query.store();
													query.reset();
										
													query << "INSERT INTO ";
													query << "	nebulas ";
													query << "(";
													query << "	id, ";
													query << "	resources ";
													query << ") ";
													query << "VALUES ";
													query << "(";
													query << "'" << searchRow["id"] << "',";
													query << "'" << newRes << "';";
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
									}
								}

								//Summiert Rohstoffe zu der Ladung der Flotte
								crystal += (double)fleet_["res_crystal"];
	
								// Flotte zurückschicken
								fleetReturn(1,-1,crystal,-1,-1,-1,-1);

								//Nachricht senden
								std::string msg = "Eine Flotte vom Planeten \n[b]";
								msg += functions::formatCoords((int)fleet_["entity_from"],0);
								msg +="[/b]\nhat [b]ein Intergalaktisches Nebelfeld [/b]\num [b]";
								msg += functions::formatTime((int)fleet_["landtime"]);
								msg += "[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
								msgRes = "\n[b]ROHSTOFFE:[/b]\n\nSilizium: ";
								msgRes += functions::nf(functions::d2s(crystal));
								msgRes += "\n";
								msg += msgRes;
							
								functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelfeld erkunden",msg);

								//Erbeutete Rohstoffsumme speichern
								query << "UPDATE ";
								query << "	users ";
								query << "SET ";
								query << "	user_res_from_nebula=user_res_from_nebula+'" << crystal << "' ";
								query << "WHERE ";
								query << "	user_id='" << fleet_["user_id"] << "';";
								query.store();
								query.reset(); 

								//Log schreiben
								std::string log = "Eine Flotte des Spielers [B]";
								log += functions::getUserNick((int)fleet_["user_id"]);
								log += "[/B] vom Planeten [b]";
								log += functions::formatCoords((int)fleet_["entity_from"],0);
								log += "[/b] at [b]ein Intergalaktisches Nebelfeld [/b] um [b]";
								log += functions::formatTime((int)fleet_["landtime"]);
								log += "[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
								log += msgRes;
								functions::addLog(13,log,time);
							}

							//20% Chance das die flotte zerstört wird
							else
							{
								//Nachricht senden
								std::string msg = "Eine Flotte vom Planeten \n[b]";
								msg += functions::formatCoords((int)fleet_["entity_from"],0);
								msg += "[/b]\n hatte bei ihrer Erkundung eines Intergalaktischen Nebelfeldes eine starke magnetische Störung, welche zu einem Systemausfall führte.\nZu der Flotte ist jeglicher Kontakt abgebrochen.";
                    
								functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte verschollen",msg);

								//Log schreiben
								std::string log = "Eine Flotte des Spielers [B]";
								log += functions::getUserNick((int)fleet_["user_id"]);
								log += "[/B] vom Planeten [b]";
								log += functions::formatCoords((int)fleet_["entity_from"],0);
								log += "[/b] wurde bei einem Intergalaktisches Nebelfeld zerst&ouml;rt.";
								functions::addLog(13,log,time);

								// Flotte-Schiffe-Verknüpfungen löschen
								fleetDelete();
							}
						}
						
						// nebula feld nicht mehr vorhanden
						else
						{
							// Flotte zurückschicken
							fleetReturn(1);

							//Nachricht senden
							std::string msg = "Die Flotte vom Planeten \n[b]";
							msg += functions::formatCoords((int)fleet_["entity_from"],0);
							msg += "[/b]\n konnte kein Intergalaktisches Nebelfeld orten.\n";
				
							functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelfeld verschwunden",msg);

							//Log schreiben
							std::string log = "Eine Flotte des Spielers [B]";
							log += functions::getUserNick((int)fleet_["user_id"]);
							log += "[/B] vom Planeten [b]";
							log += functions::formatCoords((int)fleet_["entity_from"],0);
							log += "[/b] konnte kein Intergalaktisches Nebelfeld orten.";
							functions::addLog(13,log,time);
						}
					}
				}
				else
				{
					std::string text = "Eine Flotte vom Planeten ";
					text += functions::formatCoords((int)fleet_["entity_from"],0);
					text += " versuchte, in einem Nebelfeld zu saugen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
					functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelsaugen gescheitert",text);
				
					fleetReturn(1);
				}
			}
			else
			{
				std::string text = "Eine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["entity_from"],0);
				text += " versuchte, in einem Nebelfeld zu saugen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
				
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelsaugen gescheitert",text);
			
				fleetReturn(1);
			}
		}
	}
}
