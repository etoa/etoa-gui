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

		//Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << std::setprecision(18);
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
		query << "	AND fs_ship_faked='0'";
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

						this->destroyedShips = "";
						this->destroy = 0;
						this->one = rand() % 101;
						this->two = (double)config.nget("asteroid_action",0) * 100;

						if (this->one  < this->two)	// 20 % Chance dass Schiffe überhaupt zerstört werden
						{
							this->destroy = rand() % (int)(config.nget("asteroid_action",1) * 100);		// 0 <= X <= 10 Prozent an Schiffen werden Zerstört					
						}

						if(this->destroy>0)
						{
							query << "SELECT ";
							query << "	s.ship_name, ";
							query << "	fs.fs_ship_id, ";
							query << "	fs.fs_ship_cnt ";
							query << "FROM ";
							query << "(";
							query << "	fleet_ships AS fs ";
							query << "INNER JOIN ";
							query << "	fleet AS f ";
							query << "	ON fs.fs_fleet_id = f.id ";
							query << ")"; 
							query << "INNER JOIN ";
							query << "	ships AS s ";
							query << "	ON fs.fs_ship_id = s.ship_id ";
							query << "	AND f.id='" << fleet_["id"] << "' ";
							query << "GROUP BY ";
							query << "fs.fs_ship_id;";
							mysqlpp::Result cntRes = query.store();
							query.reset();
							
							if (cntRes)
							{
								int cntSize = cntRes.size();
							
								if (cntSize > 0)
								{
									mysqlpp::Row cntRow = cntRes.at(0);
				
									for (mysqlpp::Row::size_type i = 0; i<cntSize; i++) 
									{
										cntRow = cntRes.at(i);

										//Berechnet wie viele Schiffe von jedem Typ zerstört werden
										this->shipDestroy = (int)floor((int)cntRow["fs_ship_cnt"] * this->destroy / 100);
					
										if(this->shipDestroy>0)
										{
											// "Zerstörte" Schiffe aus der Flotte löschen
											query << "UPDATE ";
											query << "	fleet_ships ";
											query << "SET ";
											query << "	fs_ship_cnt=fs_ship_cnt-'" << this->shipDestroy << "' ";
											query << "WHERE ";
											query << "	fs_fleet_id='" << fleet_["id"] << "' ";
											query << "	AND fs_ship_id='" << cntRow["fs_ship_id"] << "';";
											query.store();
											query.reset();
											this->destroyedShips += functions::d2s(this->shipDestroy);
											this->destroyedShips += " ";
											this->destroyedShips += std::string(cntRow["ship_name"]);
											this->destroyedShips += "\n";
										}
									}
								}
							}
							
							if(this->shipDestroy > 0)
							{
								this->destroyedShipsMsg = "\n\nAufrund einer Kolision mit einem Asteroiden sind einige deiner Schiffe zerst&ouml;rt worden:\n\n";
								this->destroyedShipsMsg += this->destroyedShips;
							}
						}
						else
						{
							this->destroyedShipsMsg = "";
						}
				
						this->fleetCapa = 0;
						this->asteroidCapa = 0;
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
						mysqlpp::Result asteroidRes = query.store();
						query.reset();
						
						if (asteroidRes)
						{
							int asteroidSize = asteroidRes.size();
						
							if (asteroidSize > 0)
							{
								mysqlpp::Row asteroidRow = asteroidRes.at(0);
									
								this->asteroidCapa = (int)asteroidRow["capa"];
							}
						}

						//Wenn noch Asteroidensammler vorhanden sind
						if (this->asteroidCapa > 0)
						{
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
									this->fleetCapa = (double)capaRow["capa"] - (double)fleet_["res_metal"] - (double)fleet_["res_crystal"] - (double)fleet_["res_plastic"] - (double)fleet_["res_fuel"] - (double)fleet_["res_food"];
								}
							}

							this->capa = std::min(this->fleetCapa, this->asteroidCapa);
							this->capa /= 3;

							this->asteroid = config.nget("asteroid_action",2) + (rand() % (int)(this->capa - config.nget("asteroid_action",1) + 1));
							this->metal = round(std::min(this->asteroid,(int)asteroidRow["res_metal"]));
					
							this->asteroid = config.nget("asteroid_action",2) + (rand() % (int)(this->capa - config.nget("asteroid_action",1) + 1));
							this->crystal = round(std::min(this->asteroid,(int)asteroidRow["res_crystal"]));
					
							this->asteroid = config.nget("asteroid_action",2) + (rand() % (int)(this->capa - config.nget("asteroid_action",1) + 1));
							this->plastic = round(std::min(this->asteroid,(int)asteroidRow["res_plastic"]));

							query << "UPDATE ";
							query << "	asteroids ";
							query << "SET ";
							query << " res_metal = res_metal - '" << this->metal << "', ";
							query << " res_crystal = res_crystal - '" << this->crystal << "', ";
							query << " res_plastic = res_plastic - '" << this->plastic << "' ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							query.store();
							query.reset();

							//
							//Wenn Asteroidenfeld keine ress mehr hat -> löschen und neues erstellen
							//
							query << "SELECT ";
							query << " SUM(res_metal+res_crystal+res_plastic+res_fuel+res_food) as res ";
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
									

							
									if ((int)checkRow["res"] < config.nget("asteroid_action",2))
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

										//asteroid löschen
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
										this->newMetal = config.nget("asteroid_ress",1) + (rand() % (int)(config.nget("asteroid_ress",2) - config.nget("asteroid_ress",1) + 1));
										this->newMetal /= 3;
										this->newCrystal = config.nget("asteroid_ress",1) + (rand() % (int)(config.nget("asteroid_ress",2) - config.nget("asteroid_ress",1) + 1));
										this->newCrystal /= 3;
										this->newPlastic = config.nget("asteroid_ress",1) + (rand() % (int)(config.nget("asteroid_ress",2) - config.nget("asteroid_ress",1) + 1));
										this->newPlastic /= 3;

										
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
												query << "'" << this->newMetal << "', ";
												query << "'" << this->newCrystal << "', ";
												query << "'" << this->newPlastic << "');";
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
									this->metal += (double)fleet_["res_metal"];
									this->crystal += (double)fleet_["res_crystal"];
									this->plastic += (double)fleet_["res_plastic"];
							
									this->sum = this->metal + this->crystal + this->plastic;
															// Flotte zurückschicken
									fleetReturn(1,this->metal,this->crystal,this->plastic,-1,-1,-1);

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
									msg += this->destroyedShipsMsg;
							
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Asteroiden gesammelt",msg);

									//Erbeutete Rohstoffsumme speichern
									query << "UPDATE ";
									query << "	users ";
									query << "SET ";
									query << "	user_res_from_asteroid=user_res_from_asteroid+'" << this->sum << "' ";
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
									log += "[/b]erreicht und Rohstoffe gesammelt.";
									log += msgRes;
									log += this->destroyedShipsMsg;
							
									functions::addLog(13,log,time);
								}
							}
						}

						//Wenn keine Asteroidensammler mehr vorhanden sind
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
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Asteroidensammeln gescheitert",msg);

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
			//Wenn keine Asteroidensammler vorhanden sind
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
