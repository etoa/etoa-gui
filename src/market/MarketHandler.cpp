#include <iostream>
#include <vector>

#include <time.h>
#include <math.h>
#include <string.h>
#define MYSQLPP_MYSQL_HEADERS_BURIED

#include <mysql++/mysql++.h>
#include "../util/Functions.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"

#include "MarketHandler.h"

const char* SHIP_MISC_MSG_CAT_ID ="5";
const char* MARKET_SHIP_ID = "16";
const char* MARKET_USER_ID = "1";
const char* FLEET_ACTION_RESS = "market";
const char* TRADE_POINTS_PER_TRADE = "1";
const char* TRADE_POINTS_PER_AUCTION = "1";
const char* TRADE_POINTS_PER_TRADETEXT = "1";
const char* TRADE_POINTS_TRADETEXT_MIN_LENGTH = "15";

namespace market
{
	void MarketHandler::addTradePoints(std::string userId,int points,bool sell,std::string reason)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	id ";
		query << "FROM ";
		query << "	user_ratings ";
		query << "WHERE ";
		query << "	id='" << userId << "';";
		mysqlpp::Result uRes = query.store();
		query.reset();
		
		if (uRes)
		{
			unsigned int uSize = uRes.size();
			
			if (uSize > 0)
			{
				query << "UPDATE ";
				query << "	user_ratings ";
				query << "SET ";
				query << "	trade_rating=trade_rating+" << points << ", ";
				if (sell)
				{
					query << " trades_sell=trades_sell+'1' ";
				}
				else
				{
					query << " trades_buy=trades_buy+'1' ";
				}
				query << "WHERE ";
				query << "	id=" << userId << ";";
				query.store();
				query.reset();
			}
			else
			{
				query << "INSERT INTO ";
				query << "	user_ratings ";
				query << "(";
				query << "	id, ";
				if (sell)
				{
					query << "	trades_sell, ";
				}
				else
				{
					query << " trades_buy, ";
				}
				query << "	trade_rating ";
				query << ")";
				query << "VALUES ";
				query << "(";
				query << "	'" << userId << "', ";
				query << "	'1', ";
				query << "	'" << points << "' ";
				query << ");";
				query.store();
				query.reset();
			}
		}
			
		std::string text = "Der Spieler ";
		text += userId;
		text += " erhält ";
		text += points;
		text += " Handelspunkte. Grund. ";
		text += reason;
		std::time_t time = std::time(0);
		etoa::add_log(17,text,time);
	}	
		
	//Configwerte des Marktes werden aktualisiert
	void MarketHandler::update_config(std::vector<int> buy_res, std::vector<int> sell_res)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		std::vector<std::string> ressource (5);
		ressource[0] = "metal";
		ressource[1] = "crystal";
		ressource[2] = "plastic";
		ressource[3] = "fuel";
		ressource[4] = "food";
		
		mysqlpp::Query query = con_->query();

                // TODO: This doest not work correctly, user market_rating instead
                /*
		int i=0;
		while (i<5)
		{
			query << "UPDATE ";
				query << "config ";
			query << "SET ";
				query << "config_value=config_value+" << buy_res[i] << ", ";
				query << "config_param1=config_param1+" << sell_res[i] << " ";
			query << "WHERE ";
				query << "config_name='market_" << ressource[i] << "_logger'";
			query.store();
			query.reset();
			i++;
		}
                 */
	}
	
	//Markt: Abgelaufene Auktionen löschen
	void MarketHandler::MarketAuctionUpdate()
	{
		Config &config = Config::instance();
		std::string msg;
		std::time_t time = std::time(0);
	
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_auction ";
		query << "WHERE ";
			query << "date_end<" << time << " ";
			query << "OR date_delete!='0';";
		mysqlpp::Result res = query.store();		
		query.reset();

		if (res) 
		{
			unsigned int resSize = res.size();
			std::cout << "Updating "<< resSize << " passed market auctions\n";
			if (resSize>0)
			{
			
				std::vector<int> buy_res (5);
				std::vector<int> sell_res (5);
			
				mysqlpp::Row arr;
				//int lastId = 0;
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);
					    	
					//Markt Level vom Verkäufer laden
					query << "SELECT ";
						query << "buildlist_current_level ";
					query << "FROM ";
						query << "buildlist ";
					query << "WHERE ";
						query << "buildlist_entity_id=" << arr["entity_id"] << " ";
						query << "AND buildlist_building_id='21' "; //ID muss definiert werden oder in config (DEFINE!!)
						query << "AND buildlist_current_level>'0' ";
						query << "AND buildlist_user_id=" << arr["user_id"] << ";";
					mysqlpp::Result mres = query.store();		
					query.reset();
					
					if (mres) 
					{
						unsigned int mresSize = mres.size();
						mysqlpp::Row marr;
						
						if (mresSize>0)
						{
							marr = mres.at(0);

							// Definiert den Rückgabefaktor
							float return_factor = 1 - (1/(marr["buildlist_current_level"]+1));

							std::string partner_user_nick = etoa::get_user_nick((int)arr["user_id"]);
							std::string buyer_user_nick = etoa::get_user_nick((int)arr["current_buyer_id"]);
							int delete_date = time + ((int)config.nget("market_auction_delay_time", 0) * 3600);

							//überprüfen ob geboten wurde, wenn nicht, Waren dem Verkäufer zurückgeben
							if((int)arr["current_buyer_id"]==0)
							{
								// Ress dem besitzer zurückgeben (mit dem faktor)
								query << "UPDATE ";
									query << "planets ";
								query << "SET ";
									query << "planet_res_metal=planet_res_metal+(" << arr["sell_0"]*return_factor << "), ";
									query << "planet_res_crystal=planet_res_crystal+(" << arr["sell_1"]*return_factor << "), ";
									query << "planet_res_plastic=planet_res_plastic+(" << arr["sell_2"]*return_factor << "), ";
									query << "planet_res_fuel=planet_res_fuel+(" << arr["sell_3"]*return_factor << "), ";
									query << "planet_res_food=planet_res_food+(" << arr["sell_4"]*return_factor << ") ";
								query << "WHERE ";
									query << "id=" << arr["entity_id"] << " ";
									query << "AND planet_user_id=" << arr["user_id"] << ";";
								query.store();		
								query.reset();

								// Nachricht senden
								msg = "Folgende Auktion ist erfolglos abgelaufen und wurde gelöscht.\n\n"; 
            
								msg += "Start: ";
								msg += etoa::format_time(arr["date_start"]);
								msg += "\n";
								msg += "Ende: ";
								msg += etoa::format_time(arr["date_end"]);
								msg += "\n\n";
            
								msg += "[b]Waren:[/b]\n";
								msg += "Titan: ";
								msg += etoa::nf(std::string(arr["sell_0l"]));
								msg += "\nSilizium: ";
								msg += etoa::nf(std::string(arr["sell_1l"]));
								msg += "\nPVC: ";
								msg += etoa::nf(std::string(arr["sell_2"]));
								msg += "\nTritium: ";
								msg += etoa::nf(std::string(arr["sell_3"]));
								msg += "\nNahrung: ";
								msg += etoa::nf(std::string(arr["sell_4"]));
								msg += "\n\n";
            
								msg += "Du erhälst ";
								double tmp = etoa::s_round(return_factor,2)*100;
								msg += etoa::toString(tmp);
								msg += "% deiner Rohstoffe wieder zurück (abgerundet)!\n\n";
            
								msg += "Das Handelsministerium";
								etoa::send_msg((int)arr["user_id"],atoi(SHIP_MISC_MSG_CAT_ID),"Auktion beendet",msg);

								//Auktion löschen
								query << "DELETE FROM ";
									query << "market_auction ";
								query << "WHERE ";
									query << "id=" << arr["id"] << ";";
								query.store();		
								query.reset();
							}
					
							//Jemand hat geboten: Waren zum Versenden freigeben und Nachricht schreiben
							else if((int)arr["current_buyer_id"]!=0 and (int)arr["buyable"]==1)
							{
								// Nachricht an Verkäufer
								msg = "Die Auktion vom ";
								msg += etoa::format_time(arr["date_start"]);
								msg += ", welche am ";
								msg += etoa::format_time(arr["date_end"]);
								msg += " endete, ist erfolgteich abgelaufen und wird nach ";
								msg += config.get("market_auction_delay_time", 0);
								msg += " Stunden gelöscht. Die Waren werden nach wenigen Minuten versendet.\n\nDer Spieler ";
								msg += buyer_user_nick;
								msg += " hat von dir folgende Rohstoffe ersteigert:\n\n";
            
								msg += "Titan: ";
								msg += etoa::nf(std::string(arr["sell_0"]));
								msg += "\nSilizium: ";
								msg += etoa::nf(std::string(arr["sell_1"]));
								msg += "\nPVC: ";
								msg += etoa::nf(std::string(arr["sell_2"]));
								msg += "\nTritium: ";
								msg += etoa::nf(std::string(arr["sell_3"]));
								msg += "\nNahrung: ";
								msg += etoa::nf(std::string(arr["sell_4"]));
								msg += "\n\n";
            
								msg += "Dies macht dich um folgende Rohstoffe reicher:\n"; 
								msg += "Titan: ";
								msg += etoa::nf(std::string(arr["buy_0"]));
								msg += "\nSilizium: ";
								msg += etoa::nf(std::string(arr["buy_1"]));
								msg += "\nPVC: ";
								msg += etoa::nf(std::string(arr["buy_2"]));
								msg += "\nTritium: ";
								msg += etoa::nf(std::string(arr["buy_3"]));
								msg += "\nNahrung: ";
								msg += etoa::nf(std::string(arr["buy_4"]));
								msg += "\n\n";
            
								msg += "Das Handelsministerium";
								etoa::send_msg((int)arr["user_id"],atoi(SHIP_MISC_MSG_CAT_ID),"Auktion beendet",msg);

								// Nachricht an Käufer
								msg = "Du warst der höchstbietende in der Auktion vom Spieler " + partner_user_nick + ", welche am ";
								msg += etoa::format_time(arr["date_end"]);
								msg += " zu Ende ging.\n\n";
								msg += "Du hast folgende Rohstoffe ersteigert:\n\n";
			
								msg += "Titan: ";
								msg += etoa::nf(std::string(arr["sell_0"]));
								msg += "\nSilizium: ";
								msg += etoa::nf(std::string(arr["sell_1"]));
								msg += "\nPVC: ";
								msg += etoa::nf(std::string(arr["sell_2"]));
								msg += "\nTritium: ";
								msg += etoa::nf(std::string(arr["sell_3"]));
								msg += "\nNahrung: ";
								msg += etoa::nf(std::string(arr["sell_4"]));
								msg += "\n\n";
            
								msg += "Dies hat dich folgende Rohstoffe gekostet:\n\n"; 
				
								msg += "Titan: ";
								msg += etoa::nf(std::string(arr["buy_0"]));
								msg += "\nSilizium: ";
								msg += etoa::nf(std::string(arr["buy_1"]));
								msg += "\nPVC: ";
								msg += etoa::nf(std::string(arr["buy_2"]));
								msg += "\nTritium: ";
								msg += etoa::nf(std::string(arr["buy_3"]));
								msg += "\nNahrung: ";
								msg += etoa::nf(std::string(arr["buy_4"]));
								msg += "\n\n"; 
				
								msg += "Die Auktion wird nach ";
								msg += config.get("market_auction_delay_time", 0);
								msg += " Stunden gelöscht und die Waren in wenigen Minuten versendet.\n\n";
            
								msg += "Das Handelsministerium";
								etoa::send_msg((int)arr["current_buyer_id"],atoi(SHIP_MISC_MSG_CAT_ID),"Auktion beendet",msg);
            

								//Log schreiben, falls dieser Handel regelwidrig ist
								query <<"SELECT ";
									query << "user_multi_multi_user_id ";
								query << "FROM ";
									query << "user_multi ";
								query << "WHERE ";
									query << "user_multi_user_id=" << arr["user_id"] << " ";
									query << "AND user_multi_multi_user_id=" << arr["current_buyer_id"] << ";";
								mysqlpp::Result multi_res = query.store();		
								query.reset();
				
								query <<"SELECT ";
									query << "user_multi_multi_user_id ";
								query << "FROM ";
									query << "user_multi ";
								query << "WHERE ";
									query << "user_multi_user_id=" << arr["current_buyer_id"] << " ";
									query << "AND user_multi_multi_user_id=" << arr["user_id"] << ";";
								mysqlpp::Result multi_res2 = query.store();		
								query.reset();
							
								if (multi_res and multi_res2) 
								{
									unsigned int multi_resSize = multi_res.size();
									unsigned int multi_res2Size = multi_res2.size();
    	
									if (multi_resSize>0 or multi_res2Size>0)
									{
										std::string log = "[URL=?page=user&sub=edit&user_id=";
										log += std::string(arr["current_buyer_id"]);
										log += "][B]";
										log += buyer_user_nick;
										log += "[/B][/URL] hat an einer Auktion von [URL=?page=user&sub=edit&user_id=";
										log += std::string(arr["user_id"]);
										log += "][B]";
										log += partner_user_nick;
										log += "[/B][/URL] gewonnen:\n\nSchiffe:\n";
										log += etoa::nf(std::string(arr["ship_count"]));
										log += " Schiff-ID";
										log += std::string(arr["ship_id"]);
										log += "\n\nRohstoffe:\nTitan: ";
										log += etoa::nf(std::string(arr["sell_0"]));
										log += "\nSilizium: ";
										log += etoa::nf(std::string(arr["sell_1"]));
										log += "\nPVC: ";
										log += etoa::nf(std::string(arr["sell_2"]));
										log += "\nTritium: ";
										log += etoa::nf(std::string(arr["sell_3"]));
										log += "\nNahrung: ";
										log += etoa::nf(std::string(arr["sell_4"]));
										log += "\n\nDies hat ihn folgende Rohstoffe gekostet:\nTitan: ";
										log += etoa::nf(std::string(arr["buy_1"]));
										log += "\nSilizium: ";
										log += etoa::nf(std::string(arr["buy_2"]));
										log += "\nPVC: ";
										log += etoa::nf(std::string(arr["buy_3"]));
										log += "\nTritium: ";
										log += etoa::nf(std::string(arr["buy_4"]));
										log += "\nNahrung: ";
										log += etoa::nf(std::string(arr["buy_5"]));
										etoa::add_log(10,log,time);
									}
								}

								// Log schreiben
								std::string log = "Auktion erfolgreich abgelaufen.\nDer Spieler ";
								log += buyer_user_nick;
								log += " hat vom Spieler ";
								log += partner_user_nick;
								log += " folgende Waren ersteigert:\n\nRohstoffe:\nTitan: ";
								log += etoa::nf(std::string(arr["sell_0"]));
								log += "\nSilizium: ";
								log += etoa::nf(std::string(arr["sell_1"]));
								log += "\nPVC: ";
								log += etoa::nf(std::string(arr["sell_2"]));
								log += "\nTritium: ";
								log += etoa::nf(std::string(arr["sell_3"]));
								log += "\nNahrung: ";
								log += etoa::nf(std::string(arr["sell_4"]));
								log += "\n\nDies hat ihn folgende Rohstoffe gekostet:\nTitan: ";
								log += etoa::nf(std::string(arr["buy_0"]));
								log += "\nnSilizium: ";
								log += etoa::nf(std::string(arr["buy_1"]));
								log += "\nPVC: ";
								log += etoa::nf(std::string(arr["buy_2"]));
								log += "\nTritium: ";
								log += etoa::nf(std::string(arr["buy_3"]));
								log += "\nNahrung: ";
								log += etoa::nf(std::string(arr["buy_4"]));
								log += "\n\nDie Auktion und wird nach ";
								log += config.get("market_auction_delay_time", 0);
								log += " Stunden gelöscht.";
								etoa::add_log(7,log,time);

								//Auktion noch eine zeit lang anzeigen, aber unkäuflich machen
								query << "UPDATE ";
									query << "market_auction ";
								query << "SET ";
									query << "buyable='0', ";
									query << "date_delete=" << delete_date << ", ";
									query << "sent='0' ";
								query << "WHERE ";
									query << "id=" << arr["id"] << ";";
								query.store();		
								query.reset();

								// Verkauftse Roshtoffe summieren für Config
								sell_res[0] += int(arr["sell_0"]);
								sell_res[1] += int(arr["sell_1"]);
								sell_res[2] += int(arr["sell_2"]);
								sell_res[3] += int(arr["sell_3"]);
								sell_res[4] += int(arr["sell_4"]);
						
								// Faktor = Kaufzeit - Verkaufzeit (in ganzen Tagen, mit einem Max. von 7)
								// Total = Mengen / Faktor
								int	factor = std::min((int)ceil( (time - arr["date_start"]) / 3600 / 24 ) ,7);
						
								// Summiert gekaufte Rohstoffe für Config
								buy_res[0] += arr["buy_0"] / factor;
								buy_res[1] += arr["buy_1"] / factor;
								buy_res[2] += arr["buy_2"] / factor;
								buy_res[3] += arr["buy_3"] /	factor;
								buy_res[4] += arr["buy_4"] / factor;
						
								// Summiert verkaufte Rohstoffe für Config
								sell_res[0] += arr["sell_0"] / factor;
								sell_res[1] += arr["sell_1"] / factor;
								sell_res[2] += arr["sell_2"] / factor;
								sell_res[3] += arr["sell_3"] / factor;
								sell_res[4] += arr["sell_4"] / factor;

							}
						
							// Waren sind gesendet, jetzt nur noch nachricht schreiben und löschendatum festlegen
                                                        else if((int)arr["date_delete"]==0 and (int)arr["sent"]==1)
							{
								// Nachricht senden
								msg = "Die Auktion vom ";
								msg += etoa::format_time(arr["date_start"]);
								msg += ", welche am ";
								msg += etoa::format_time(arr["date_end"]);
								msg += " endete, ist erfolgreich abgelaufen und wird nach ";
								msg += config.get("market_auction_delay_time", 0);
								msg += " Stunden gelöscht.\n\n";
				
								msg += "Das Handelsministerium";
								etoa::send_msg((int)arr["user_id"],atoi(SHIP_MISC_MSG_CAT_ID),"Auktion abgelaufen",msg);
	
								//Auktion noch eine zeit lang anzeigen, aber unkäuflich machen
								query << "UPDATE ";
									query << "market_auction ";
								query << "SET ";
									query << "buyable='0', ";
									query << "date_delete=" << delete_date << " ";
								query << "WHERE ";
									query << "id=" << arr["id"] << ";";
								query.store();		
								query.reset();          
							}

							//Auktionen löschen, welche bereits abgelaufen sind und die Anzeigedauer auch hinter sich haben
							query << "DELETE FROM ";
								query << "market_auction ";
							query << "WHERE ";
								query << "id=" << arr["id"] << " ";
								query << "AND date_delete<=" << time << " ";
								query << "AND sent='1';";
							query.store();		
							query.reset();
						}
					}
				}
				
				// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
				update_config(buy_res, sell_res);				
			
			}
		}
	}
	
	
	//
	// Markt Update (Verschicken von allen gekauften/ersteigerten Waren) und berechnen der Roshtoffkurse. Löschen alter Angebote
	//
	void MarketHandler::update()
	{
		Config &config = Config::instance();
		//Auktionen Updaten (beenden)
		MarketHandler::MarketAuctionUpdate();

		std::string msg;
		std::time_t time = std::time(0);
		int ship_speed=0, ship_starttime=0, ship_landtime=0;
		
		User *buyer;
		User *seller;
		
		// Ermittelt die Geschwindigkeit des Handelsschiffes
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "ship_speed, ";
			query << "ship_time2start, ";
			query << "ship_time2land ";
		query << "FROM ";
			query << "ships ";
		query << "WHERE ";
			query << "ship_id='" << MARKET_SHIP_ID << "';";
		mysqlpp::Result res = query.store();		
		query.reset();	
		
		if (res) 
		{
			unsigned int resSize = res.size();
			mysqlpp::Row arr;
			
			if (resSize>0)
			{
				arr = res.at(0);
				
				ship_speed = int(arr["ship_speed"]);
				ship_starttime = int(arr["ship_time2start"]);
				ship_landtime = int(arr["ship_time2land"]);
			}
			else
			{
				ship_speed = 1;
			}
		}


    		
		/*
		//
		// Rohstoffe
		//
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_ressource ";
		query << "WHERE ";
			query << "ressource_buyable='0';";  
		res = query.store();		
		query.reset();	  			

		if (res) 
		{
			unsigned int resSize = res.size();
			std::cout << "updating " << resSize << " market_ress...\n";
			if (resSize>0)
			{
				mysqlpp::Row arr;
				//int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);
					
					buyer = new User((int)arr["ressource_buyer_id"]);
					seller = new User((int)arr["user_id"]);
					
					// Add trade points
					int tradepointsBuyer = 1;
					int tradepointsSeller = 1;
					if ((int)strlen(arr["ressource_text"]) > 15) 
					{
						tradepointsSeller += 1;
					}
					
					
					std::string textBuyer = "Rohstoffkauf von ";
					textBuyer += std::string(arr["user_id"]);
					
					std::string textSeller = "Rohstoffverkauf an ";
					textSeller += std::string(arr["ressource_buyer_id"]);
					
					addTradePoints(std::string(arr["ressource_buyer_id"]),tradepointsBuyer,0,textBuyer);
					addTradePoints(std::string(arr["user_id"]),tradepointsSeller,1,textSeller);

					//Flotte zum Verkäufer schicken
					int launchtime = std::time(0); // Startzeit
					double distance = etoa::calcDistanceByPlanetId(arr["planet_id"],arr["ressource_buyer_planet_id"]);
					
					// TODO I've added some typecasts and (). Please check if it's calculating correctly
					int duration = ((int) (distance / (double)ship_speed * 3600) + ship_starttime + ship_landtime);
					int sellerLandtime = launchtime + (int)(duration / seller->getSpecialist()->getSpecialistTradeBonus()); // Landezeit
					int buyerLandtime = launchtime + (int)(duration / buyer->getSpecialist()->getSpecialistTradeBonus()); // Landezeit

					
					query << "INSERT INTO fleet ";
						query << "(user_id, ";
						query << "entity_from, ";
						query << "entity_to, ";
						query << "next_id, ";
						query << "launchtime, ";
						query << "landtime, ";
						query << "action, ";
						query << "res_metal, ";
						query << "res_crystal, ";
						query << "res_plastic, ";
						query << "res_fuel, ";
						query << "res_food) ";
					query << "VALUES ";
						query << "('" << arr["user_id"] << "', ";
						query << "'" << config.get("market_entity", 0) << "', ";
						query << arr["planet_id"] << ", ";
						query << arr["user_id"] << ", ";
						query << launchtime << ", ";
						query << sellerLandtime << ", ";
						query << "'" << FLEET_ACTION_RESS << "', ";
						query << arr["buy_metal"] << ", ";
						query << arr["buy_crystal"] << ", ";
						query << arr["buy_plastic"] << ", ";
						query << arr["buy_fuel"] << ", ";
						query << arr["buy_food"] << ");";
					query.store();		
					query.reset();

					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << "'" << con_->insert_id() << "',";
						query << "'" << MARKET_SHIP_ID << "', ";
						query << "'1');";
					query.store();		
					query.reset();
					
					//Flotte zum Käufer schicken
					query << "INSERT INTO fleet ";
						query << "(user_id, ";
						query << "entity_from, ";
						query << "entity_to, ";
						query << "next_id, ";
						query << "launchtime, ";
						query << "landtime, ";
						query << "action, ";
						query << "res_metal, ";
						query << "res_crystal, ";
						query << "res_plastic, ";
						query << "res_fuel, ";
						query << "res_food) ";
					query << "VALUES ";
						query << "('" << arr["ressource_buyer_id"] << "', ";
						query << "'" << config.get("market_entity", 0) << "', ";
						query << arr["ressource_buyer_planet_id"] << ", ";
						query << arr["ressource_buyer_id"] << ", ";
						query << launchtime << ", ";
						query << buyerLandtime << ", ";
						query << "'" << FLEET_ACTION_RESS << "', ";
						query << arr["sell_metal"] << ", ";
						query << arr["sell_crystal"] << ", ";
						query << arr["sell_plastic"] << ", ";
						query << arr["sell_fuel"] << ", ";
						query << arr["sell_food"] << ");";
					query.store();
					query.reset();
					
					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << "'" << con_->insert_id() << "',";
						query << "'" << MARKET_SHIP_ID << "', ";
						query << "'1');";
					query.store();
					query.reset();

					//Angebot löschen
					query << "DELETE FROM ";
						query << "market_ressource ";
					query << "WHERE ";
						query << "ressource_market_id=" << arr["ressource_market_id"] << ";";
					query.store();
					query.reset();
				}
		  	}
		}
		
		//
		// Schiffe
		//
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_ship ";
		query << "WHERE ";
			query << "ship_buyable='0';";
		res = query.store();		
		query.reset();	
		
		if (res) 
		{
			unsigned int resSize = res.size();
			std::cout << "updating " << resSize << " market_ship\n";
			if (resSize>0)
			{
				mysqlpp::Row arr;
				//int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);
					
					buyer = new User((int)arr["ship_buyer_id"]);
					seller = new User((int)arr["user_id"]);
					
					// Add trade points
					int tradepointsBuyer = 1;
					int tradepointsSeller = 1;
					if ((int)strlen(arr["ship_text"]) > 15) 
					{
						tradepointsSeller += 1;
					}
					
					std::string textBuyer = "Schiffkauf von ";
					textBuyer += std::string(arr["user_id"]);
					
					std::string textSeller = "Schiffverkauf an ";
					textSeller += std::string(arr["ship_buyer_id"]);
					
					addTradePoints(std::string(arr["ship_buyer_id"]),tradepointsBuyer,0,textBuyer);
					addTradePoints(std::string(arr["user_id"]),tradepointsSeller,1,textSeller);

					//Flotte zum Verkäufer schicken
					int launchtime = time; // Startzeit
					double distance = etoa::calcDistanceByPlanetId(arr["planet_id"],arr["ship_buyer_planet_id"]);
					int duration = (int)(distance / (double)ship_speed * 3600) + ship_starttime + ship_landtime;
					int sellerLandtime = (int)launchtime + (int)(duration / seller->getSpecialist()->getSpecialistTradeBonus()); // Landezeit
					int buyerLandtime = (int)launchtime + (int)(duration / buyer->getSpecialist()->getSpecialistTradeBonus()); // Landezeit

					query << "INSERT INTO fleet ";
						query << "(user_id, ";
						query << "entity_from, ";
						query << "entity_to, ";
						query << "next_id, ";
						query << "launchtime, ";
						query << "landtime, ";
						query << "action, ";
						query << "res_metal, ";
						query << "res_crystal, ";
						query << "res_plastic, ";
						query << "res_fuel, ";
						query << "res_food) ";
					query << "VALUES ";
						query << "('" << arr["user_id"] << "', ";
						query << "'" << config.get("market_entity", 0) << "', ";
						query << arr["planet_id"] << ", ";
						query << arr["user_id"] << ", ";
						query << launchtime << ", ";
						query << sellerLandtime << ", ";
						query << "'" << FLEET_ACTION_RESS << "', ";
						query << arr["ship_costs_metal"] << ", ";
						query << arr["ship_costs_crystal"] << ", ";
						query << arr["ship_costs_plastic"] << ", ";
						query << arr["ship_costs_fuel"] << ", ";
						query << arr["ship_costs_food"] << ");";
					query.store();
					query.reset();
				
					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << "'" << con_->insert_id() << "',";
						query << "'" << MARKET_SHIP_ID << "', ";
						query << "'1');";
					query.store();
					query.reset();

					//Flotte zum Käufer schicken
					query << "INSERT INTO fleet ";
						query << "(user_id, ";
						query << "entity_from, ";
						query << "entity_to, ";
						query << "next_id, ";
						query << "launchtime, ";
						query << "landtime, ";
						query << "action, ";
						query << "res_metal, ";
						query << "res_crystal, ";
						query << "res_plastic, ";
						query << "res_fuel, ";
						query << "res_food) ";
					query << "VALUES ";
						query << "('" << arr["ship_buyer_id"] << "', ";
						query << "'" << config.get("market_entity", 0) << "', ";
						query << arr["ship_buyer_planet_id"] << ", ";
						query << arr["ship_buyer_id"] << ", ";
						query << launchtime << ", ";
						query << buyerLandtime << ", ";
						query << "'" << FLEET_ACTION_RESS << "', ";
						query << "'0', ";
						query << "'0', ";
						query << "'0', ";
						query << "'0', ";
						query << "'0');";
					query.store();
					query.reset();

					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << "'" << con_->insert_id() << "',";
						query << arr["ship_id"] << ", ";
						query << arr["ship_count"] << ");";
					query.store();
					query.reset();

					//Angebot löschen
					query << "DELETE FROM ";
						query << "market_ship ";
					query << "WHERE ";
						query << "ship_market_id=" << arr["ship_market_id"] << ";";
					query.store(),
					query.reset();
				}
			}
		}

		*/

		//
		// Auktionen
		//
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	market_auction ";
		query << "WHERE ";
		query << "	buyable='0' ";
		query << "	AND sent='0' ";
		query << "	AND date_delete>" << time << ";";
		res = query.store();		
		query.reset();	
		
		if (res) 
		{
			unsigned int resSize = res.size();
			std::cout << "updating " << resSize << " market_auction...\n";
			if (resSize>0)
			{
				mysqlpp::Row arr;
				//int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);
						
					buyer = new User((int)arr["current_buyer_id"]);
					seller = new User((int)arr["user_id"]);
						
					// Add trade points
					int tradepointsBuyer = 1;
					int tradepointsSeller = 1;
					if ((int)strlen(arr["text"]) > 15) 
					{
						tradepointsSeller += 1;
					}
					
					std::string textBuyer = "Auktion von ";
					textBuyer += std::string(arr["user_id"]);
					
					std::string textSeller = "Rohstoffverkauf an ";
					textSeller += std::string(arr["current_buyer_id"]);
					
					addTradePoints(std::string(arr["current_buyer_id"]),tradepointsBuyer,0,textBuyer);
					addTradePoints(std::string(arr["user_id"]),tradepointsSeller,1,textSeller);

					//Flotte zum verkäufer der auktion schicken
					int launchtime = time; // Startzeit
					double distance = etoa::calcDistanceByPlanetId(arr["entity_id"],arr["current_buyer_entity_id"]);
					int duration = (int)(distance / (double)ship_speed * 3600) + ship_starttime + ship_landtime;
					int sellerLandtime = launchtime + (int)(duration / seller->getSpecialist()->getSpecialistTradeBonus()); // Landezeit
					int buyerLandtime = launchtime + (int)(duration / buyer->getSpecialist()->getSpecialistTradeBonus()); // Landezeit

					query << "INSERT INTO fleet ";
						query << "(user_id, ";
						query << "entity_from, ";
						query << "entity_to, ";
						query << "next_id, ";
						query << "launchtime, ";
						query << "landtime, ";
						query << "action, ";
						query << "res_metal, ";
						query << "res_crystal, ";
						query << "res_plastic, ";
						query << "res_fuel, ";
						query << "res_food) ";
					query << "VALUES ";
						query << "('" << arr["user_id"] << "', ";
						query << "'" << config.get("market_entity", 0) << "', ";
						query << arr["entity_id"] << ", ";
						query << arr["user_id"] << ", ";
						query << launchtime << ", ";
						query << sellerLandtime << ", ";
						query << "'" << FLEET_ACTION_RESS << "', ";
						query << arr["buy_0"] << ", ";
						query << arr["buy_1"] << ", ";
						query << arr["buy_2"] << ", ";
						query << arr["buy_3"] << ", ";
						query << arr["buy_4"] << ");";
					query.store();
					query.reset();
				
					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << "'" << con_->insert_id() << "', ";
						query << "'" << MARKET_SHIP_ID << "', ";
						query << "'1');";
					query.store();
					query.reset();
					

					//Flotte zum hochstbietenden schicken (Käufer)
					query << "INSERT INTO fleet ";
						query << "(user_id, ";
						query << "entity_from, ";
						query << "entity_to, ";
						query << "next_id, ";
						query << "launchtime, ";
						query << "landtime, ";
						query << "action, ";
						query << "res_metal, ";
						query << "res_crystal, ";
						query << "res_plastic, ";
						query << "res_fuel, ";
						query << "res_food) ";
					query << "VALUES ";
						query << "('" << arr["current_buyer_id"] << "', ";
						query << "'" << config.get("market_entity", 0) << "', ";
						query << arr["current_buyer_entity_id"] << ", ";
						query << arr["current_buyer_id"] << ", ";
						query << launchtime << ", ";
						query << buyerLandtime << ", ";
						query << "'" << FLEET_ACTION_RESS << "', ";
						query << arr["sell_0"] << ", ";
						query << arr["sell_1"] << ", ";
						query << arr["sell_2"] << ", ";
						query << arr["sell_3"] << ", ";
						query << arr["sell_4"] << ");";
					query.store();
					query.reset();
					

					// Schickt gekaufte Rohstoffe mit Handelsschiff
					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << "'" << con_->insert_id() << "',";
						query << "'" << MARKET_SHIP_ID << "', ";
						query << "'1');";
					query.store();
					query.reset();

					//Waren als "gesendet" markieren
					query << "UPDATE ";
						query << "market_auction ";
					query << "SET ";
						query << "sent='1' ";
					query << "WHERE ";
						query << "id=" << arr["id"] << ";";
					query.store();
					query.reset();
				}	
			}
		}
		
		/*
		
		
		//
		// Rohstoffkurs Berechnung & Update (Der Schiffshandel beeinflusst die Kurse nicht!)
		//
											
		// Berechnet die neuen Kurse -> Kurs = Gekaufte Rohstoffe / Verkaufte Rohstoffe
		// conf V = Gekaufte Rohstoffe
		// conf p1 = Verkaufte Rohstoffe
		// conf p2 = Startwert
		std::cout << "Updating config...\n";
		query << "SELECT ";
		query << "	config_value, ";
		query << "	config_param1, ";
		query << "	config_param2 ";
		query << "FROM ";
		query << "	config ";
		query << "WHERE ";
		query << "	 `config_name` LIKE '%logger%'";
		query << "ORDER BY ";
		query << "	`config`.`config_id` ASC;";
		res = query.store();
		query.reset();
		
		mysqlpp::Row row = res.at(0);
		float metal_tax = etoa::s_round((((double)row["config_value"] + (int)row["config_param2"]) / ((int)row["config_param1"] + (int)row["config_param2"])),2);
		row = res.at(1);
		float crystal_tax = etoa::s_round((((double)row["config_value"] + (int)row["config_param2"]) / ((int)row["config_param1"] + (int)row["config_param2"])),2);
		row = res.at(2);
		float plastic_tax = etoa::s_round((((double)row["config_value"] + (int)row["config_param2"]) / ((int)row["config_param1"] + (int)row["config_param2"])),2);
		row = res.at(3);
		float fuel_tax = etoa::s_round((((double)row["config_value"] + (int)row["config_param2"]) / ((int)row["config_param1"] + (int)row["config_param2"])),2);
		row = res.at(4);
		float food_tax = etoa::s_round((((double)row["config_value"] + (int)row["config_param2"]) / ((int)row["config_param1"] + (int)row["config_param2"])),2);
		
		// Update der Kurse
		// Titan
		query << "UPDATE ";
			query << "config ";
		query << "SET ";
			query << "config_value=" << metal_tax << " ";
		query << "WHERE ";
			query << "config_name='market_metal_factor';";
		query.store();
		query.reset();
			
		// Silizium
		query << "UPDATE ";
			query << "config ";
		query << "SET ";
			query << "config_value=" << crystal_tax << " ";
		query << "WHERE ";
			query << "config_name='market_crystal_factor';";
		query.store();
		query.reset();
	
		// PVC
		query << "UPDATE ";
			query << "config ";
		query << "SET ";
			query << "config_value=" << plastic_tax << " ";
		query << "WHERE ";
			query << "config_name='market_plastic_factor';";
		query.store();
		query.reset();
	
		// Tritium
		query << "UPDATE ";
			query << "config ";
		query << "SET ";
			query << "config_value=" << fuel_tax << " ";
		query << "WHERE ";
			query << "config_name='market_fuel_factor';";
		query.store();
		query.reset();

		// Food
		query << "UPDATE ";
			query << "config ";
		query << "SET ";
			query << "config_value=" << food_tax << " ";
		query << "WHERE ";
			query << "config_name='market_food_factor';";
		query.store();
		query.reset();
		
		*/
		
		int responseTime = (int)config.nget("market_response_time", 0);
		// Löscht alte Rohstoffangebote
		/*std::cout << "Deleting old ones\n";
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_ressource ";
		query << "WHERE ";
			query << "datum<=(" << time-responseTime*3600*24 << ");";
		res = query.store();
		query.reset();
		
		if (res) 
		{
			unsigned int resSize = res.size();
			std::cout << "Size: " << resSize << "\n";
			if (resSize>0)
			{
				mysqlpp::Row arr;
				//int lastId = 0;

				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
				std::cout << i << "\n";
					arr = res.at(i);
					
					// Markt Level vom Verkäufer laden
					query << "SELECT ";
						query << "buildlist_current_level ";
					query << "FROM ";
						query << "buildlist ";
					query << "WHERE ";
						query << "buildlist_entity_id=" << arr["planet_id"] << " ";
						query << "AND buildlist_building_id='21' "; //DEFINE!!!
						query << "AND buildlist_current_level>'0' ";
						query << "AND buildlist_user_id= " << arr["user_id"] << ";";
					mysqlpp::Result mres = query.store();
					query.reset();
					
					if (mres) 
					{
						unsigned int mresSize = mres.size();

						if (mresSize>0);
						{
							mysqlpp::Row marr;

							marr = mres.at(0);
          
							// Definiert den Rückgabefaktor
							 float return_factor = 1 - (1/(marr["buildlist_current_level"]+1));
          
							// Ress dem besitzer zurückgeben (mit dem faktor)
							query << "UPDATE ";
								query << "planets ";
							query << "SET ";
								query << "planet_res_metal=planet_res_metal+" << floor(int(arr["sell_metal"])*return_factor) << ", ";
								query << "planet_res_crystal=planet_res_crystal+" << floor(int(arr["sell_crystal"])*return_factor) << ", ";
								query << "planet_res_plastic=planet_res_plastic+" << floor(int(arr["sell_plastic"])*return_factor) << ", ";
								query << "planet_res_fuel=planet_res_fuel+" << floor(int(arr["sell_fuel"])*return_factor) << ", ";
								query << "planet_res_food=planet_res_food+" << floor(int(arr["sell_food"])*return_factor) << " ";
							query << "WHERE ";
								query << "id=" << arr["planet_id"] << " ";
								query << "AND planet_user_id=" << arr["user_id"] << ";";
							query.store();
							query.reset();

							// Nachricht senden
							msg = "Folgendes Rohstoffangebot wurde nicht innerhalb von ";
							msg += config.get("market_response_time", 0);
							msg += " Tagen gekauft und deshalb gelöscht.\n\n"; 
                    
							msg += "[b]Angebot:[/b]\n";
							msg += "Titan: ";
							msg += etoa::nf(std::string(arr["sell_metal"]));
							msg += "\nSilizium: ";
							msg += etoa::nf(std::string(arr["sell_crystal"]));
							msg += "\nPVC: ";
							msg += etoa::nf(std::string(arr["sell_plastic"]));
							msg += "\nTritium: ";
							msg += etoa::nf(std::string(arr["sell_fuel"]));
							msg += "\nNahrung: ";
							msg += etoa::nf(std::string(arr["sell_food"]));
							msg += "\n\n";
          
							msg += "[b]Preis:[/b]\n";
							msg += "Titan: ";
							msg += etoa::nf(std::string(arr["buy_metal"]));
							msg += "\nSilizium: ";
							msg += etoa::nf(std::string(arr["buy_crystal"]));
							msg += "\nPVC: ";
							msg += etoa::nf(std::string(arr["buy_plastic"]));
							msg += "\nTritium: ";
							msg += etoa::nf(std::string(arr["buy_fuel"]));
							msg += "\nNahrung: ";
							msg += etoa::nf(std::string(arr["buy_food"]));
							msg += "\n\n";
          
							msg += "Du erhälst ";
							msg += etoa::toString(etoa::s_round(return_factor,2)*100);
							msg += "% deiner Rohstoffe wieder zurück (abgerundet)!\n\n";
							
							msg += "Das Handelsministerium";
							etoa::send_msg((int)arr["user_id"],atoi(SHIP_MISC_MSG_CAT_ID),"Angebot gelöscht",msg);

							// Angebot löschen
							query << "DELETE FROM ";
								query << "market_ressource ";
							query << "WHERE ";
								query << "ressource_market_id=" << arr["ressource_market_id"] << ";";
							query.store();
							query.reset();
						}
					}
				}
			}
    	}*/

		responseTime = (int)config.nget("market_response_time", 0);
		std::cout << "Deleting ships\n";
		// Löscht alte Schiffsangebote
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_ship ";
		query << "WHERE ";
			query << "datum<=(" << time-responseTime*3600*24 << ");";
		res = query.store();
		query.reset();
		
		if (res) 
		{
			unsigned int resSize = res.size();

			if (resSize>0)
			{
				mysqlpp::Row arr;
				//int lastId = 0;

				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);
					
					// Markt Level vom Verkäufer laden
					query << "SELECT ";
						query << "buildlist_current_level ";
					query << "FROM ";
						query << "buildlist ";
					query << "WHERE ";
						query << "buildlist_entity_id=" << arr["entity_id"] << " ";
						query << "AND buildlist_building_id='21' ";
						query << "AND buildlist_current_level>'0' ";
						query << "AND buildlist_user_id=" << arr["user_id"] << ";";
					mysqlpp::Result mres = query.store();
					query.reset();
					
					mysqlpp::Row marr;
					
					marr = mres.at(0);

					// Definiert den Rückgabefaktor
					float return_factor = 1 - (1/(marr["buildlist_current_level"]+1));
          
					// Schiffe dem besitzer zurückgeben (mit dem faktor)
					query << "UPDATE ";
						query << "shiplist ";
					query << "SET ";
						query << "shiplist_count=shiplist_count+" << floor(int(arr["ship_count"])*return_factor) << " "; 
					query << "WHERE "; 
						query << "shiplist_user_id=" << arr["user_id"] << " "; 
						query << "AND shiplist_entity_id=" << arr["entity_id"] << " "; 
						query << "AND shiplist_ship_id=" << arr["ship_id"] << ";";
					query.store();
					query.reset();

					// Nachricht senden
					msg = "Folgendes Schiffsangebot wurde nicht innerhalb von ";
					msg += config.get("market_response_time", 1);
					msg += " Tagen gekauft und deshalb gelöscht.\n\n"; 
                    
					msg +=std::string(arr["ship_name"]);
					msg += ": ";
					msg += etoa::nf(std::string(arr["ship_count"]));
					msg += "\n\n";  
                  
					msg += "[b]Preis:[/b]\n";
					msg += "Titan: ";
					msg += etoa::nf(std::string(arr["ship_costs_metal"]));
					msg += "\nSilizium: ";
					msg += etoa::nf(std::string(arr["ship_costs_crystal"]));
					msg += "\nPVC: ";
					msg += etoa::nf(std::string(arr["ship_costs_plastic"]));
					msg += "\nTritium: ";
					msg += etoa::nf(std::string(arr["ship_costs_fuel"]));
					msg += "\nNahrung: ";
					msg += etoa::nf(std::string(arr["ship_costs_food"]));
					msg += "\n\n";
          
					msg += "Du erhälst ";
					msg += etoa::toString(etoa::s_round(return_factor,2)*100);
					msg += "% deiner Schiffe wieder zurück (abgerundet)!\n\n";
          
					msg += "Das Handelsministerium";
					etoa::send_msg((int)arr["user_id"],atoi(SHIP_MISC_MSG_CAT_ID),"Angebot gelöscht",msg);

					// Angebot löschen
					query << "DELETE FROM ";
						query << "market_ship ";
					query << "WHERE ";
						query << "ship_market_id=" << arr["ship_market_id"] << ";";
					query.store();
					query.reset();
				}
			}
		}
		std::cout << "done\n";
	}	
}
