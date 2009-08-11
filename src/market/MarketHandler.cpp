
#include "MarketHandler.h"

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
		query << "INSERT INTO "
			<< "	user_rating "
			<< "( "
			<< "	id, ";
		if (sell) query << " trades_sell, ";
		else query << " trades_buy, ";
		query << "	trades_rating "
			<< ") "
			<< "VALUES "
			<< "( "
			<< "	'" << userId << "', "
			<< "	'1', "
			<< "	'" << points << "' "
			<< ") "
			<< "ON DUPLICATE KEY "
			<< "	UPDATE "
			<< "		trade_rating=trade_rating+VALUES(trade_rating), ";
		if (sell) query << " trades_sell=trades_sell+VALUES(trades_sell);";
		else query << " trades_buy=trades_buy+VALUES(trades_buy);";
		query.store();
		query.reset();
			
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
		std::time_t time = std::time(0);
	
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	* "
			<< "FROM "
			<< "	market_auction "
			<< "WHERE "
			<< "	date_end<'" << time << "' "
			<< "	OR date_delete!='0';";
		mysqlpp::Result res = query.store();		
		query.reset();

		if (res) {
			unsigned int resSize = res.size();
			//std::cout << "Updating "<< resSize << " passed market auctions\n";
			if (resSize>0) {
			
				std::vector<int> buy_res (5);
				std::vector<int> sell_res (5);
			
				mysqlpp::Row arr;
				//int lastId = 0;
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++)  {
					arr = res.at(i);
					    	
					//Markt Level vom Verkäufer laden
					query << "SELECT "
						<< "	buildlist_current_level "
						<< "FROM "
						<< "	buildlist "
						<< "WHERE "
						<< "	buildlist_entity_id='" << arr["entity_id"] << "' "
						<< "	AND buildlist_building_id='" << (int)config.idget("MARKET_ID") << "' "
						<< "	AND buildlist_current_level>'0' "
						<< "	AND buildlist_user_id='" << arr["user_id"] << "' "
						<< "LIMIT 1;";
					mysqlpp::Result mres = query.store();		
					query.reset();
					
					if (mres) {
						unsigned int mresSize = mres.size();
						mysqlpp::Row marr;
						
						if (mresSize>0) {
							marr = mres.at(0);

							// Definiert den Rückgabefaktor
							float returnFactor = 1 - (1/(marr["buildlist_current_level"]+1));
							int deleteDate = time + ((int)config.nget("market_auction_delay_time", 0) * 3600);
							std::string buyer_user_nick = etoa::get_user_nick((int)arr["current_buyer_id"]);
							std::string partner_user_nick = etoa::get_user_nick((int)arr["user_id"]);

							//überprüfen ob geboten wurde, wenn nicht, Waren dem Verkäufer zurückgeben
							if((int)arr["current_buyer_id"]==0)
							{
								// Ress dem besitzer zurückgeben (mit dem faktor)
								query << "UPDATE "
									<< "	planets "
									<< "SET "
									<< "	planet_res_metal=planet_res_metal+(" << arr["sell_0"]*returnFactor << "), "
									<< "	planet_res_crystal=planet_res_crystal+(" << arr["sell_1"]*returnFactor << "), "
									<< "	planet_res_plastic=planet_res_plastic+(" << arr["sell_2"]*returnFactor << "), "
									<< "	planet_res_fuel=planet_res_fuel+(" << arr["sell_3"]*returnFactor << "), "
									<< "	planet_res_food=planet_res_food+(" << arr["sell_4"]*returnFactor << ") "
									<< "WHERE "
									<< "	id='" << arr["entity_id"] << "' "
									<< "	AND planet_user_id='" << arr["user_id"] << "' "
									<< "LIMIT 1;";
								query.store();		
								query.reset();
								
								MarketReport *report = new MarketReport((int)arr["user_id"],
																		 (int)arr["entity_id"],
																		 (int)arr["id"],
																		 (int)arr["date_end"]);
								report->setSell((int)arr["sell_0"],
												(int)arr["sell_1"],
												(int)arr["sell_2"],
												(int)arr["sell_3"],
												(int)arr["sell_4"],
												0);
								report->setFactor(etoa::s_round(returnFactor,2));
								report->setSubtype("auctioncancel");
								
								delete report;
            

								//Auktion löschen
								query << "DELETE FROM "
									<< "	market_auction "
									<< "WHERE "
									<< "	id='" << arr["id"] << "' "
									<< "LIMIT 1;";
								query.store();		
								query.reset();
							}
					
							//Jemand hat geboten: Waren zum Versenden freigeben und Nachricht schreiben
							else if((int)arr["current_buyer_id"]!=0 and (int)arr["buyable"]==1) {
								// Report an Verkäufer
								MarketReport *report = new MarketReport((int)arr["user_id"],
																		 (int)arr["entity_id"],
																		 (int)arr["id"],
																		 (int)arr["date_end"],
																		 (int)arr["current_buyer_id"]);
								report->setSell((int)arr["sell_0"], 
												(int)arr["sell_1"],
												(int)arr["sell_2"],
												(int)arr["sell_3"],
												(int)arr["sell_4"],
												0);
								report->setBuy((int)arr["buy_0"], 
												(int)arr["buy_1"],
												(int)arr["buy_2"],
												(int)arr["buy_3"],
												(int)arr["buy_4"],
												0);
								report->setSubtype("auctionfinished");
								
								delete report;

								//Report an Käufer
								report = new MarketReport((int)arr["current_buyer_id"],
														  (int)arr["entity_id"],
														  (int)arr["id"],
														  (int)arr["date_end"],
														  (int)arr["user_id"]);
								
								report->setSell((int)arr["sell_0"], 
												(int)arr["sell_1"],
												(int)arr["sell_2"],
												(int)arr["sell_3"],
												(int)arr["sell_4"],
												0);
								report->setBuy((int)arr["buy_0"], 
												(int)arr["buy_1"],
												(int)arr["buy_2"],
												(int)arr["buy_3"],
												(int)arr["buy_4"],
												0);
								report->setSubtype("auctionwon");
								
								delete report;
            

								//Log schreiben, falls dieser Handel regelwidrig ist
								query <<"SELECT "
									<< "	user_multi_multi_user_id "
									<< "FROM "
									<< "	user_multi "
									<< "WHERE "
									<< "	user_multi_user_id='" << arr["user_id"] << "' "
									<< "	AND user_multi_multi_user_id='" << arr["current_buyer_id"] << "' "
									<< "LIMIT 1;";
								mysqlpp::Result multi_res = query.store();		
								query.reset();
				
								query <<"SELECT "
									<< "	user_multi_multi_user_id "
									<< "FROM "
									<< "	user_multi "
									<< "WHERE "
									<< "	user_multi_user_id='" << arr["current_buyer_id"] << "' "
									<< "	AND user_multi_multi_user_id='" << arr["user_id"] << "' "
									<< "LIMIT 1;";
								mysqlpp::Result multi_res2 = query.store();		
								query.reset();
							
								if (multi_res and multi_res2) {
									unsigned int multi_resSize = multi_res.size();
									unsigned int multi_res2Size = multi_res2.size();
    	
									if (multi_resSize>0 or multi_res2Size>0) {
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
								query << "UPDATE "
									<< "	market_auction "
									<< "SET "
									<< "	buyable='0', "
									<< "	date_delete='" << deleteDate << "', "
									<< "	sent='0' "
									<< "WHERE "
									<< "	id='" << arr["id"] << "' "
									<< "LIMIT 1;";
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
						}
					}
				}
				
				// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
				update_config(buy_res, sell_res);				
			
			}
		}
		//Auktionen löschen, welche bereits abgelaufen sind und die Anzeigedauer auch hinter sich haben
		query << "DELETE FROM "
			<< "	market_auction "
			<< "WHERE "
			<< "	date_delete<='" << time << "' "
			<< "	AND sent='1';";
		query.store();		
		query.reset();
	}
	
	
	//
	// Markt Update (Verschicken von allen gekauften/ersteigerten Waren) und berechnen der Roshtoffkurse. Löschen alter Angebote
	//
	void MarketHandler::update()
	{
		Config &config = Config::instance();
		//Auktionen Updaten (beenden)
		MarketHandler::MarketAuctionUpdate();
		
		std::time_t time = std::time(0);
		
		User *buyer;
		User *seller;
		
		// Handelsschiff
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *marketShip = DataHandler.getShipById(config.idget("MARKET_SHIP_ID"));
		
		//
		// Auktionen
		//
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	* "
			<< "FROM "
			<< "	market_auction "
			<< "WHERE "
			<< "	buyable='0' "
			<< "	AND sent='0' "
			<< "	AND date_delete>'" << time << "';";
		mysqlpp::Result res = query.store();		
		query.reset();	
		
		if (res) {
			unsigned int resSize = res.size();
			//std::cout << "updating " << resSize << " market_auction...\n";
			if (resSize>0) {
				mysqlpp::Row arr;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
					arr = res.at(i);
						
					buyer = new User((int)arr["current_buyer_id"]);
					seller = new User((int)arr["user_id"]);
						
					// Add trade points
					int tradepointsBuyer = 1;
					int tradepointsSeller = 1;
					tradepointsSeller = ((int)strlen(arr["text"]) > 15) ? 2 : 1;
					
					std::string textBuyer = "Auktion von ";
					textBuyer += std::string(arr["user_id"]);
					
					std::string textSeller = "Rohstoffverkauf an ";
					textSeller += std::string(arr["current_buyer_id"]);
					
					addTradePoints(std::string(arr["current_buyer_id"]),tradepointsBuyer,0,textBuyer);
					addTradePoints(std::string(arr["user_id"]),tradepointsSeller,1,textSeller);

					//Flotte zum verkäufer der auktion schicken
					int launchtime = time; // Startzeit
					double distance = etoa::calcDistanceByPlanetId(arr["entity_id"],arr["current_buyer_entity_id"]);
					int duration = (int)(distance / (double)marketShip->getSpeed() * 3600) + marketShip->getTime2Start() + marketShip->getTime2Land();
					int sellerLandtime = launchtime + (int)(duration / seller->getSpecialist()->getSpecialistTradeBonus()); // Landezeit
					int buyerLandtime = launchtime + (int)(duration / buyer->getSpecialist()->getSpecialistTradeBonus()); // Landezeit

					query << "INSERT INTO fleet "
						<< "(	user_id, "
						<< "	entity_from, "
						<< "	entity_to, "
						<< "	next_id, "
						<< "	launchtime, "
						<< "	landtime, "
						<< "	action, "
						<< "	res_metal, "
						<< "	res_crystal, "
						<< "	res_plastic, "
						<< "	res_fuel, "
						<< "	res_food) "
						<< "VALUES "
						<< "(	'" << arr["user_id"] << "', "
						<< "	'" << config.get("market_entity", 0) << "', "
						<<		arr["entity_id"] << ", "
						<<		arr["user_id"] << ", "
						<<		launchtime << ", "
						<<		sellerLandtime << ", "
						<< "	'" << FLEET_ACTION_RESS << "', "
						<<		arr["buy_0"] << ", "
						<<		arr["buy_1"] << ", "
						<<		arr["buy_2"] << ", "
						<<		arr["buy_3"] << ", "
						<<		arr["buy_4"] << ");";
					query.store();
					query.reset();
				
					query << "INSERT INTO fleet_ships "
						<< "(	fs_fleet_id, "
						<< "	fs_ship_id, "
						<< "	fs_ship_cnt) "
						<< "VALUES "
						<< "( "
						<< "	'" << con_->insert_id() << "', "
						<< "	'" << config.idget("MARKET_SHIP_ID") << "', "
						<< "	'1');";
					query.store();
					query.reset();
					

					//Flotte zum hochstbietenden schicken (Käufer)
					query << "INSERT INTO fleet "
						<< "(	user_id, "
						<< "	entity_from, "
						<< "	entity_to, "
						<< "	next_id, "
						<< "	launchtime, "
						<< "	landtime, "
						<< "	action, "
						<< "	res_metal, "
						<< "	res_crystal, "
						<< "	res_plastic, "
						<< "	res_fuel, "
						<< "	res_food) "
						<< "VALUES "
						<< "(	'" << arr["current_buyer_id"] << "', "
						<< "	'" << config.get("market_entity", 0) << "', "
						<<		arr["current_buyer_entity_id"] << ", "
						<<		arr["current_buyer_id"] << ", "
						<<		launchtime << ", "
						<<		buyerLandtime << ", "
						<< "	'" << FLEET_ACTION_RESS << "', "
						<<		arr["sell_0"] << ", "
						<<		arr["sell_1"] << ", "
						<<		arr["sell_2"] << ", "
						<<		arr["sell_3"] << ", "
						<<		arr["sell_4"] << ");";
					query.store();
					query.reset();
					

					// Schickt gekaufte Rohstoffe mit Handelsschiff
					query << "INSERT INTO fleet_ships "
						<< "(fs_fleet_id, "
						<< "fs_ship_id, "
						<< "fs_ship_cnt) "
						<< "VALUES "
						<< "( "
						<< "'" << con_->insert_id() << "',"
						<< "'" << config.idget("MARKET_SHIP_ID") << "', "
						<< "'1');";
					query.store();
					query.reset();

					//Waren als "gesendet" markieren
					query << "UPDATE "
						<< "	market_auction "
						<< "SET "
						<< "	sent='1' "
						<< "WHERE "
						<< "	id='" << arr["id"] << "' "
						<< "LIMIT 1;";
					query.store();
					query.reset();
				}	
			}
		}
	}	
}
