#include <iostream>
#include <vector>

#include <time.h>
#include <math.h>
#include <mysql++/mysql++.h>

#include "MarketHandler.h"

namespace market
{
	
	//Markt: Abgelaufene Auktionen löschen
	void MarketHandler::MarketAuctionUpdate(mysqlpp::Con* con_)
	{
	
		std::string msg;
		std::time_t time = std::time(0);
	
		std::cout << "Updating market auctions\n";
	
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_auction ";
		query << "WHERE ";
			query << "auction_end<" << time << " ";
			query << "OR auction_delete_date!='0';";
		mysqlpp::Result res = query.store();		
		query.reset();
	
		if (res) 
		{
			int resSize = res.size();
			bool empty=false;
    	
			if (resSize>0)
			{
		
				int buy_metal_total = 0;
				int buy_crystal_total = 0;
				int buy_plastic_total = 0;
				int buy_fuel_total = 0;
				int buy_food_total = 0;
			
				int sell_metal_total = 0;
				int sell_crystal_total = 0;
				int sell_plastic_total = 0;
				int sell_fuel_total = 0;
				int sell_food_total = 0;
			
				mysqlpp::Row arr;
				int lastId = 0;
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);

					//Markt Level vom Verkäufer laden
					query << "SELECT ";
						query << "buildlist_current_level ";
					query << "FROM ";
						query << "buildlist ";
					query << "WHERE ";
						query << "buildlist_planet_id=" << arr["auction_planet_id"] << " ";
						query << "AND buildlist_building_id='21' "; //ID muss definiert werden oder in config (DEFINE!!)
						query << "AND buildlist_current_level>'0' ";
						query << "AND buildlist_user_id=" << arr["auction_user_id"] << ";";
					mysqlpp::Result mres = query.store();		
					query.reset();
				
					if (mres) 
					{
						int mresSize = mres.size();
						mysqlpp::Row marr;
						
						if (mresSize>0)
						{
							marr = mres.at(0);
                    
							// Definiert den Rückgabefaktor
							float return_factor = 1 - (1/(marr["buildlist_current_level"]+1));

							std::string partner_user_nick = get_user_nick($arr['auction_user_id']); //ToDo
							std::string buyer_user_nick = get_user_nick($arr['auction_current_buyer_id']); //ToDo
							int delete_date = time + (AUCTION_DELAY_TIME * 3600); //ToDo

							//überprüfen ob geboten wurde, wenn nicht, Waren dem Verkäufer zurückgeben
							if(arr["auction_current_buyer_id"]=='0')
							{
								// Ress dem besitzer zurückgeben (mit dem faktor)
								query << "UPDATE ";
									query << "planets ";
								query << "SET ";
									query << "planet_res_metal=planet_res_metal+(" << arr["auction_sell_metal"]*return_factor << "), ";
									query << "planet_res_crystal=planet_res_crystal+(" << arr["auction_sell_crystal"]*return_factor << "), ";
									query << "planet_res_plastic=planet_res_plastic+(" << arr["auction_sell_plastic"]*return_factor << "), ";
									query << "planet_res_fuel=planet_res_fuel+(" << arr["auction_sell_fuel"]*return_factor << "), ";
									query << "planet_res_food=planet_res_food+(" << arr["auction_sell_food"]*return_factor << ") ";
								query << "WHERE ";
									query << "planet_id=" << arr["auction_planet_id"] << " ";
									query << "AND planet_user_id=" << arr["auction_user_id"] << ";";
								query.store();		
								query.reset();

								// Nachricht senden
								msg = "Folgende Auktion ist erfolglos abgelaufen und wurde gelöscht.\n\n"; 
            
								msg += "Start: ";
								msg += .functions::format_time(arr["auction_start"]);
								msg += "\n";
								msg += "Ende: ";
								msg += functions::format_time(arr["auction_end"]);
								msg += "\n\n";
            
								msg += "[b]Waren:[/b]\n";
								msg += "Titan: ";
								msg += functions::nf(arr["auction_sell_metal"]);
								msg += "\nSilizium: ";
								msg += functions::nf(arr["auction_sell_crystal"]);
								msg += "\nPVC: ";
								msg += functions::nf(arr["auction_sell_plastic"]);
								msg += "\nTritium: ";
								msg += functions::nf(arr["auction_sell_fuel"]);
								msg += "\nNahrung: ";
								msg += functions::nf(arr["auction_sell_food"]);
								msg += "\n\n";
            
								msg += "Du erhälst ",
								msg += std::string(round($return_factor,2)*100); //ToDo
								msg += "% deiner Rohstoffe wieder zurück (abgerundet)!\n\n";
            
								msg += "Das Handelsministerium";
								send_msg(arr["auction_user_id"],SHIP_MISC_MSG_CAT_ID,"Auktion beendet",msg); //ToDo

								//Auktion löschen
								query << "DELETE FROM ";
									query << "market_auction ";
								query << "WHERE ";
									query "auction_market_id=" << arr["auction_market_id"] << ";";
								query.store();		
								query.reset();
							}
					
							//Jemand hat geboten: Waren zum Versenden freigeben und Nachricht schreiben
							else if(arr["auction_current_buyer_id"]!='0' and arr["auction_buyable"]=='1')
							{
								// Nachricht an Verkäufer
								msg = "Die Auktion vom ";
								msg += functions::format_time(arr["auction_start"]);
								msg += ", welche am ";
								msg += functions::format_time(arr["auction_end"]);
								msg += " endete, ist erfolgteich abgelaufen und wird nach ".AUCTION_DELAY_TIME." Stunden gelöscht. Die Waren werden nach wenigen Minuten versendet.\n\nDer Spieler " + buyer_user_nick +" hat von dir folgende Rohstoffe ersteigert:\n\n"; //ToDo
            
								msg += "Titan: ";
								msg += functions::nf(arr["auction_sell_metal"]).;
								msg += "\nSilizium: ";
								msg += functions::nf(arr["auction_sell_crystal"]);
								msg += "\nPVC: ";
								msg += functions::nf(arr["auction_sell_plastic"]);
								msg += "\nTritium: ";
								msg += functions::nf(arr["auction_sell_fuel"]);
								msg += "\nNahrung: ";
								msg += functions::nf(arr["auction_sell_food"]);
								msg += "\n\n";
            
								msg += "Dies macht dich um folgende Rohstoffe reicher:\n"; 
								msg += "Titan: ";
								msg += functions::nf(arr["auction_buy_metal"]).;
								msg += "\nSilizium: ";
								msg += functions::nf(arr["auction_buy_crystal"]);
								msg += "\nPVC: ";
								msg += functions::nf(arr["auction_buy_plastic"]);
								msg += "\nTritium: ";
								msg += functions::nf(arr["auction_buy_fuel"]);
								msg += "\nNahrung: ";
								msg += functions::nf(arr["auction_buy_food"]);
								msg += "\n\n";
            
								msg += "Das Handelsministerium";
								send_msg(arr["auction_user_id"],SHIP_MISC_MSG_CAT_ID,"Auktion beendet",msg);

								// Nachricht an Käufer
								msg = "Du warst der höchstbietende in der Auktion vom Spieler " + partner_user_nick + ", welche am ";
								msg += functions::format_time(arr["auction_end"]);
								msg += " zu Ende ging.\n";
								msg += "Du hast folgende Rohstoffe ersteigert:\n\n";
			
								msg += "Titan: ";
								msg += functions::nf(arr["auction_sell_metal"]).;
								msg += "\nSilizium: ";
								msg += functions::nf(arr["auction_sell_crystal"]);
								msg += "\nPVC: ";
								msg += functions::nf(arr["auction_sell_plastic"]);
								msg += "\nTritium: ";
								msg += functions::nf(arr["auction_sell_fuel"]);
								msg += "\nNahrung: ";
								msg += functions::nf(arr["auction_sell_food"]);
								msg += "\n\n";
            
								msg += "Dies hat dich folgende Rohstoffe gekostet:\n\n"; 
				
								msg += "Titan: ";
								msg += functions::nf(arr["auction_buy_metal"]).;
								msg += "\nSilizium: ";
								msg += functions::nf(arr["auction_buy_crystal"]);
								msg += "\nPVC: ";
								msg += functions::nf(arr["auction_buy_plastic"]);
								msg += "\nTritium: ";
								msg += functions::nf(arr["auction_buy_fuel"]);
								msg += "\nNahrung: ";
								msg += functions::nf(arr["auction_buy_food"]);
								msg += "\n\n"; 
				
								msg += "Die Auktion wird nach " + AUCTION_DELAY_TIME + " Stunden gelöscht und die Waren in wenigen Minuten versendet.\n\n"; //ToDo
            
								msg += "Das Handelsministerium";
								send_msg(arr["auction_current_buyer_id"],SHIP_MISC_MSG_CAT_ID,"Auktion beendet",msg);
            

								//Log schreiben, falls dieser Handel regelwidrig ist
								query <<"SELECT ";
									query << "user_multi_multi_user_id ";
								query << "FROM ";
									query << "user_multi ";
								query << "WHERE ";
									query << "user_multi_user_id=" << arr["auction_user_id"] << " ";
									query << "AND user_multi_multi_user_id=" << arr["auction_current_buyer_id"] << ";";
								mysqlpp::Result multi_res = query.store();		
								query.reset();
				
								query <<"SELECT ";
									query << "user_multi_multi_user_id ";
								query << "FROM ";
									query << "user_multi ";
								query << "WHERE ";
									query << "user_multi_user_id=" << arr["auction_current_buyer_id"] << " ";
									query << "AND user_multi_multi_user_id=" << arr["auction_user_id"] << ";";
								mysqlpp::Result multi_res2 = query.store();		
								query.reset();
							
								if (multi_res and multi_res2) 
								{
									int multi_resSize = multi_res.size();
									int multi_res2Size = multi_res2.size();
    	
									if (multi_resSize>0 or multi_res2Size>0)
									{
										std::string log = "[URL=?page=user&sub=edit&user_id=";
										log += arr["auction_current_buyer_id"];
										log += "][B]";
										log += buyer_user_nick;
										log += "[/B][/URL] hat an einer Auktion von [URL=?page=user&sub=edit&user_id=";
										log += arr["auction_user_id"];
										log += "][B]";
										log += partner_user_nick;
										log += "[/B][/URL] gewonnen:\n\nSchiffe:\n";
										log += functions::nf(arr["auction_ship_count"]);
										log += " ";
										log += arr["auction_ship_name"];
										log += "\n\nRohstoffe:\nTitan: ";
										log += functions::nf(arr["auction_sell_metal"]);
										log += "\nSilizium: ";
										log += functions::nf(arr["auction_sell_crystal"]);
										log += "\PVC: ";
										log += functions::nf(arr["auction_sell_plastic"]);
										log += "\Tritium: ";
										log += functions::nf(arr["auction_sell_fuel"]);
										log += "\Nahrung: ";
										log += functions::nf(arr["auction_sell_food"]);
										log += "\n\nDies hat ihn folgende Rohstoffe gekostet:\nTitan: ";
										log += functions::nf(arr["auction_buy_metal"]);
										log += "\nSilizium: ";
										log += functions::nf(arr["auction_buy_crystal"]);
										log += "\PVC: ";
										log += functions::nf(arr["auction_buy_plastic"]);
										log += "\Tritium: ";
										log += functions::nf(arr["auction_buy_fuel"]);
										log += "\Nahrung: ";
										log += functions::nf(arr["auction_buy_food"]);
										add_log(10,log,time);
									}
								}

								// Log schreiben
								std::string log = "Auktion erfolgreich abgelaufen.\nDer Spieler ";
								log += buyer_user_nick;
								log += " hat vom Spieler ";
								log += partner_user_nick;
								log += " folgende Waren ersteigert:\n\nRohstoffe:\nTitan: ";
								log += functions::nf(arr["auction_sell_metal"]);
								log += "\nSilizium: ";
								log += functions::nf(arr["auction_sell_crystal"]);
								log += "\PVC: ";
								log += functions::nf(arr["auction_sell_plastic"]);
								log += "\Tritium: ";
								log += functions::nf(arr["auction_sell_fuel"]);
								log += "\Nahrung: ";
								log += functions::nf(arr["auction_sell_food"]);
								log += "\n\nDies hat ihn folgende Rohstoffe gekostet:\nTitan: "
								log += functions::nf(arr["auction_buy_metal"]);
								log += "\nSilizium: ";
								log += functions::nf(arr["auction_buy_crystal"]);
								log += "\PVC: ";
								log += functions::nf(arr["auction_buy_plastic"]);
								log += "\Tritium: ";
								log += functions::nf(arr["auction_buy_fuel"]);
								log += "\Nahrung: ";
								log += functions::nf(arr["auction_buy_food"]);
								log += "\n\nDie Auktion und wird nach ";
								log += AUCTION_DELAY_TIME;
								log += " Stunden gelöscht.";
								add_log(7,log,time);

								//Auktion noch eine zeit lang anzeigen, aber unkäuflich machen
								query << "UPDATE ";
									query << "market_auction ";
								query << "SET ";
									query << "auction_buyable='0', ";
									query << "auction_delete_date=" << delete_date << ", ";
									query << "auction_sent='0' ";
								query << "WHERE ";
									query << "auction_market_id=" << arr["auction_market_id"] << ";";
								query.store();		
								query.reset();

								// Verkauftse Roshtoffe summieren für Config
								sell_metal_total += int(arr["auction_sell_metal"]);
								sell_crystal_total += int(arr["auction_sell_crystal"]);
								sell_plastic_total += int(arr["auction_sell_plastic"]);
								sell_fuel_total += int(arr["auction_sell_fuel"]);
								sell_food_total += int(arr["auction_sell_food"]);
						
								// Faktor = Kaufzeit - Verkaufzeit (in ganzen Tagen, mit einem Max. von 7)
								// Total = Mengen / Faktor
								int	factor = min( ceil( (time - arr["auction_start"]) / 3600 / 24 ) ,7);
						
								// Summiert gekaufte Rohstoffe für Config
								buy_metal_total += arr["auction_buy_metal"] / factor;
								buy_crystal_total += arr["auction_buy_crystal"] / factor;
								buy_plastic_total += arr["auction_buy_plastic"] / factor;
								buy_fuel_total += arr["auction_buy_fuel"] /	factor;
								buy_food_total += arr["auction_buy_food"] / factor;
						
								// Summiert verkaufte Rohstoffe für Config
								sell_metal_total += arr["auction_sell_metal"] / factor;
								sell_crystal_total += arr["auction_sell_crystal"] / factor;
								sell_plastic_total += arr["auction_sell_plastic"] / factor;
								sell_fuel_total += arr["auction_sell_fuel"] / factor;
								sell_food_total += arr["auction_sell_food"] / factor;

							}
						
							// Waren sind gesendet, jetzt nur noch nachricht schreiben und löschendatum festlegen
							else if(arr["auction_delete_date"]==0 and arr["auction_sent"]==1)
							{
								// Nachricht senden
								msg = "Die Auktion vom ";
								msg += functions::format_time(arr["auction_start"]);
								msg += ", welche am ";
								msg += functions::format_time(arr["auction_end"]);
								msg += " endete, ist erfolgreich abgelaufen und wird nach " + AUCTION_DELAY_TIME + " Stunden gelöscht.\n\n";
				
								msg += "Das Handelsministerium";
								send_msg(arr["auction_user_id"],SHIP_MISC_MSG_CAT_ID,"Auktion abgelaufen",msg); //ToDo
	
								//Auktion noch eine zeit lang anzeigen, aber unkäuflich machen
								query << "UPDATE ";
									query << "market_auction ";
								query << "SET ";
									query << "auction_buyable='0', ";
									query << "auction_delete_date=" << delete_date << " ";
								query << "WHERE ";
									query << "auction_market_id=" << arr["auction_market_id"] << ";";
								query.store();		
								query.reset();          
							}

							//Auktionen löschen, welche bereits abgelaufen sind und die Anzeigedauer auch hinter sich haben
							query << "DELETE FROM ";
								query << "market_auction ";
							query << "WHERE ";
								query << "auction_market_id=" << arr["auction_market_id"] << " ";
								query << "AND auction_delete_date<=" << time << " ";
								query << "AND auction_sent='1';";
							query.store();		
							query.reset();
						}
					}
				}
      
				// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
				// Titan
				query << "UPDATE ";
					query << "config ";
				query << "SET ";
					query << "config_value=config_value+" << round(buy_metal_total) << ", ",
					query << "config_param1=config_param1+" << round(sell_metal_total) << " ";
				query << "WHERE ";
					query << "config_name='market_metal_logger'";
				query.store();
				query.reset();
				
				// Silizium
				query << "UPDATE ";
					query << "config ";
				query << "SET ";
					query << "config_value=config_value+" << round(buy_crystal_total) << ", ",
					query << "config_param1=config_param1+" << round(sell_crystal_total) << " ";
				query << "WHERE ";
					query << "config_name='market_crystal_logger'";
				query.store();
				query.reset();
				
				// PVC
				query << "UPDATE ";
					query << "config ";
				query << "SET ";
					query << "config_value=config_value+" << round(buy_plastic_total) << ", ",
					query << "config_param1=config_param1+" << round(sell_plastic_total) << " ";
				query << "WHERE ";
					query << "config_name='market_plastic_logger'";
				query.store();
				query.reset()
			
				// Tritium
				query << "UPDATE ";
					query << "config ";
				query << "SET ";
					query << "config_value=config_value+" << round(buy_fuel_total) << ", ",
					query << "config_param1=config_param1+" << round(sell_fuel_total) << " ";
				query << "WHERE ";
					query << "config_name='market_fuel_logger'";
				query.store();
				query.reset()			
				
				// Food
				query << "UPDATE ";
					query << "config ";
				query << "SET ";
					query << "config_value=config_value+" << round(buy_food_total) << ", ",
					query << "config_param1=config_param1+" << round(sell_food_total) << " ";
				query << "WHERE ";
					query << "config_name='market_food_logger'";
				query.store();
				query.reset()			
			
			}
		}
	}
	
	
	//
	// Markt Update (Verschicken von allen gekauften/ersteigerten Waren) und berechnen der Roshtoffkurse. Löschen alter Angebote
	//
	void MarketHandler::update()
	{

		//Auktionen Updaten (beenden)
		MarketHandler::MarketAuctionUpdate(this->con_)

		std::string msg;
		std::time_t time = std::time(0);
		
		// Ermittelt die Geschwindigkeit des Handelsschiffes
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "ship_speed, ";
			query << "ship_time2start, ";
			query << "ship_time2land ";
		query << "FROM ";
			query << "ships ";
		query << "WHERE ";
			query << "ship_id='16';";	//Define!!!
		mysqlpp::Result res = query.store();		
		query.reset();	
		
		if (res) 
		{
			int resSize = res.size();
			mysqlpp::Row arr;
			
			if (resSize>0)
			{
				arr = res.at(0);
				
				int ship_speed = int(arr["ship_speed"]);
				int ship_starttime = int(arr["ship_time2start"]);
				int ship_landtime = int(arr["ship_time2land"]);
			}
			else
			{
				int speed = 1;
			}
		}

		//
		// Rohstoffe
		//
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_ressource ";
		query << "WHERE ";
			query << "ressource_buyable='0';";  
		mysqlpp::Result res = query.store();		
		query.reset();	  			
    		
			
		if (res) 
		{
			int resSize = res.size();
			mysqlpp::Row arr;
			
			if (resSize>0)
			{
				mysqlpp::Row arr;
				int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);

					//Flotte zum Verkäufer schicken
					int launchtime = time(); // Startzeit
					double distance = calcDistanceByPlanetId(arr["planet_id"],arr["ressource_buyer_planet_id"]);
					int duration = distance / ship_speed * 3600 + ship_starttime + ship_landtime;
					int landtime = launchtime + duration; // Landezeit

					query << "INSERT INTO fleet ";
						query << "(fleet_user_id, ";
						query << "fleet_cell_from, ";
						query << "fleet_cell_to, ";
						query << "fleet_planet_from, ";
						query << "fleet_planet_to, ";
						query << "fleet_launchtime, ";
						query << "fleet_landtime, ";
						query << "fleet_action, ";
						query << "fleet_res_metal, ";
						query << "fleet_res_crystal, ";
						query << "fleet_res_plastic, ";
						query << "fleet_res_fuel, ";
						query << "fleet_res_food) ";
					query << "VALUES ";
						query << "('0', ";
						query << "'0', ";
						query << arr["cell_id"] << ", ";
						query << "'0', ";
						query << arr["planet_id"] << ", ";
						query << launchtime << ", ";
						query << landtime << ", ";
						query << FLEET_ACTION_RESS << ", "; //ToDo
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
						query << ".mysql_insert_id().",""; //ToDo
						query << "'16', "; //DEFINE!!!
						query << "'1');";
					query.store();		
					query.reset();
					
					//Flotte zum Käufer schicken
					query << "INSERT INTO fleet ";
						query << "(fleet_user_id, ";
						query << "fleet_cell_from, ";
						query << "fleet_cell_to, ";
						query << "fleet_planet_from, ";
						query << "fleet_planet_to, ";
						query << "fleet_launchtime, ";
						query << "fleet_landtime, ";
						query << "fleet_action, ";
						query << "fleet_res_metal, ";
						query << "fleet_res_crystal, ";
						query << "fleet_res_plastic, ";
						query << "fleet_res_fuel, ";
						query << "fleet_res_food) ";
					query << "VALUES ";
						query << "('0', ";
						query << "'0', ";
						query << arr["ressource_buyer_cell_id"] << ", ";
						query << "'0', ";
						query << arr["ressource_buyer_planet_id"] << ", ";
						query << launchtime << ", ";
						query << landtime << ", ";
						query << FLEET_ACTION_RESS << ", ";
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
						query << '".mysql_insert_id()."', //ToDo
						query << "'16', "; //DEFINE!!!
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
		mysqlpp::Result res = query.store();		
		query.reset();	
		
		if (res) 
		{
			int resSize = res.size();
			
			if (resSize>0)
			{
				mysqlpp::Row arr;
				int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);

					//Flotte zum Verkäufer schicken
					int launchtime = time; // Startzeit
					double distance = calcDistanceByPlanetId(arr["planet_id"],arr["ship_buyer_planet_id"]);
					int duration = distance / ship_speed * 3600 + ship_starttime + ship_landtime;
					int landtime = launchtime + duration; // Landezeit

					query << "INSERT INTO fleet ";
						query << "(fleet_user_id, ";
						query << "fleet_cell_from, ";
						query << "fleet_cell_to, ";
						query << "fleet_planet_from, ";
						query << "fleet_planet_to, ";
						query << "fleet_launchtime, ";
						query << "fleet_landtime, ";
						query << "fleet_action, ";
						query << "fleet_res_metal, ";
						query << "fleet_res_crystal, ";
						query << "fleet_res_plastic, ";
						query << "fleet_res_fuel, ";
						query << "fleet_res_food) ";
					query << "VALUES ";
						query << "('0', ";
						query << "'0', ";
						query << arr["cell_id"] << ", ";
						query << "'0', ";
						query << arr["planet_id"] << ", ",
						query << launchtime << ", ";
						query << landtime << ", ";
						query << FLEET_ACTION_RESS << ", ";
						query << arr["sell_metal"] << ", ";
						query << arr["ship_costs_metal"] << ", ";
						query << arr["ship_costs_crystal"] << ", ";
						query << arr["ship_costs_plastic"] << ", ";
						query << arr["ship_costs_food"] << ");";
					query.store();
					query.reset();
				
					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << '".mysql_insert_id()."', //ToDo
						query << "'16', "; //DEFINE!!!
						query << "'1');";
					query.store();
					query.reset();

					//Flotte zum Käufer schicken
					query << "INSERT INTO fleet ";
						query << "(fleet_user_id, ";
						query << "fleet_cell_from, ";
						query << "fleet_cell_to, ";
						query << "fleet_planet_from, ";
						query << "fleet_planet_to, ";
						query << "fleet_launchtime, ";
						query << "fleet_landtime, ";
						query << "fleet_action, ";
						query << "fleet_res_metal, ";
						query << "fleet_res_crystal, ";
						query << "fleet_res_plastic, ";
						query << "fleet_res_fuel, ";
						query << "fleet_res_food) ";
					query << "VALUES ";
						query << "('0', ";
						query << "'0', ";
						query << arr["ship_buyer_cell_id"] << ", ";
						query << "'0', ";
						query << arr["ship_buyer_planet_id"] << ", ",
						query << launchtime << ", ";
						query << landtime << ", ";
						query << "'mo', "; //DEFINE
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
						query << '".mysql_insert_id()."', //ToDo
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

		//
		// Auktionen
		//
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_auction ";
		query << "WHERE ";
			query << "auction_buyable='0' ";
			query << "AND auction_sent='0' ";
			query << "AND auction_delete_date>" << time << ";";
			mysqlpp::Result res = query.store();		
			query.reset();	
		
			if (res) 
			{
				int resSize = res.size();
			
				if (resSize>0)
				{
					mysqlpp::Row arr;
					int lastId = 0;
				
					for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
					{
						arr = res.at(i);

					//Flotte zum verkäufer der auktion schicken
					int launchtime = time; // Startzeit
					double distance = calcDistanceByPlanetId(arr["auction_planet_id"],arr["auction_current_buyer_planet_id"]);
					int duration = distance / ship_speed * 3600 + ship_starttime + ship_landtime;
					int landtime = launchtime + duration; // Landezeit

					query << "INSERT INTO fleet ";
						query << "(fleet_user_id, ";
						query << "fleet_cell_from, ";
						query << "fleet_cell_to, ";
						query << "fleet_planet_from, ";
						query << "fleet_planet_to, ";
						query << "fleet_launchtime, ";
						query << "fleet_landtime, ";
						query << "fleet_action, ";
						query << "fleet_res_metal, ";
						query << "fleet_res_crystal, ";
						query << "fleet_res_plastic, ";
						query << "fleet_res_fuel, ";
						query << "fleet_res_food) ";
					query << "VALUES ";
						query << "('0', ";
						query << "'0', ";
						query << arr["auction_cell_id"] << ", ";
						query << "'0', ";
						query << arr["auction_planet_id"] << ", ",
						query << launchtime << ", ";
						query << landtime << ", ";
						query << "'mo', "; //DEFINE
						query << arr["auction_buy_metal"] << ", ";
						query << arr["auction_buy_crystal"] << ", ";
						query << arr["auction_buy_plastic"] << ", ";
						query << arr["auction_buy_fuel"] << ", ";
						query << arr["auction_buy_food"] << ");";
					query.store();
					query.reset();
				
					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << '".mysql_insert_id()."', //ToDo
						query << MARKET_SHIP_ID << ", ";
						query << "'1');";
					query.store();
					query.reset();

					//Flotte zum hochstbietenden schicken (Käufer)
					query << "INSERT INTO fleet ";
						query << "(fleet_user_id, ";
						query << "fleet_cell_from, ";
						query << "fleet_cell_to, ";
						query << "fleet_planet_from, ";
						query << "fleet_planet_to, ";
						query << "fleet_launchtime, ";
						query << "fleet_landtime, ";
						query << "fleet_action, ";
						query << "fleet_res_metal, ";
						query << "fleet_res_crystal, ";
						query << "fleet_res_plastic, ";
						query << "fleet_res_fuel, ";
						query << "fleet_res_food) ";
					query << "VALUES ";
						query << "('0', ";
						query << "'0', ";
						query << arr["auction_current_buyer_cell_id"] << ", ";
						query << "'0', ";
						query << arr["auction_current_buyer_planet_id"] << ", ",
						query << launchtime << ", ";
						query << landtime << ", ";
						query << "'mo', "; //DEFINE
						query << arr["auction_sell_metal"] << ", ";
						query << arr["auction_sell_crystal"] << ", ";
						query << arr["auction_sell_plastic"] << ", ";
						query << arr["auction_sell_fuel"] << ", ";
						query << arr["auction_sell_food"] << ");";
					query.store();
					query.reset();

					// Schickt gekaufte Rohstoffe mit Handelsschiff
					query << "INSERT INTO fleet_ships ";
						query << "(fs_fleet_id, ";
						query << "fs_ship_id, ";
						query << "fs_ship_cnt) ";
					query << "VALUES ";
						query << "( ";
						query << '".mysql_insert_id()."', //ToDo
						query << MARKET_SHIP_ID << ", ";
						query << "'1');";
					query.store();
					query.reset();
        

					//Waren als "gesendet" markieren
					query << "UPDATE ";
						query << "market_auction ";
					query << "SET ";
						query << "auction_sent='1' ";
					query << "WHERE ";
						query << "auction_market_id=" << arr["auction_market_id"] << ";";
					query.store();
					query.reset();
				}	
			}
		}


		//
		// Rohstoffkurs Berechnung & Update (Der Schiffshandel beeinflusst die Kurse nicht!)
		//
											
		// Berechnet die neuen Kurse -> Kurs = Gekaufte Rohstoffe / Verkaufte Rohstoffe
		// conf V = Gekaufte Rohstoffe
		// conf p1 = Verkaufte Rohstoffe
		// conf p2 = Startwert
		float metal_tax = round(($conf['market_metal_logger']['v'] + $conf['market_metal_logger']['p2']) / ($conf['market_metal_logger']['p1'] + $conf['market_metal_logger']['p2']),2);
		float crystal_tax = round(($conf['market_crystal_logger']['v'] + $conf['market_crystal_logger']['p2']) / ($conf['market_crystal_logger']['p1'] + $conf['market_crystal_logger']['p2']),2);
		float plastic_tax = round(($conf['market_plastic_logger']['v'] + $conf['market_plastic_logger']['p2']) / ($conf['market_plastic_logger']['p1'] + $conf['market_plastic_logger']['p2']),2);
		float fuel_tax = round(($conf['market_fuel_logger']['v'] + $conf['market_fuel_logger']['p2']) / ($conf['market_fuel_logger']['p1'] + $conf['market_fuel_logger']['p2']),2);
		float food_tax = round(($conf['market_food_logger']['v'] + $conf['market_food_logger']['p2']) / ($conf['market_food_logger']['p1'] + $conf['market_food_logger']['p2']),2);

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

		// Löscht alte Rohstoffangebote
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_ressource ";
		query << "WHERE ";
			query << "datum<=(" << time-$conf['market_response_time']['v']*3600*24 << ");"; //ToDo
		mysqlpp::Result res = query.store();
		query.reset();
		
		if (res) 
		{
			int resSize = res.size();

			if (resSize>0)
			{
				mysqlpp::Row arr;
				int lastId = 0;

				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);

					// Markt Level vom Verkäufer laden
					query << "SELECT ";
						query << "buildlist_current_level ";
					query << "FROM ";
						query << "buildlist ";
					query << "WHERE ",
						query << "buildlist_planet_id=" << arr["planet_id"] << " ";
						query << "AND buildlist_building_id='21' "; //DEFINE!!!
						query << "AND buildlist_current_level>'0' ";
						query << "AND buildlist_user_id= " << arr["user_id"] << ";";
					mysql::Result mres = query.store();
					query.reset();
					
					if (mres) 
					{
						int mresSize = mres.size();

						if (mresSize>0)
						{
							mysqlpp::Row marr;

							arr = res.at(0);
          
							// Definiert den Rückgabefaktor
							 float return_factor = 1 - (1/(marr["buildlist_current_level"]+1));
          
							// Ress dem besitzer zurückgeben (mit dem faktor)
							query << "UPDATE ";
								query << "planets ";
							query << "SET ";
								query << "planet_res_metal=planet_res_metal+" << floor(int(arr["sell_metal"])*return_factor)) << ", ";
								query << "planet_res_crystal=planet_res_crystal+" << floor(int(arr["sell_crystal"])*return_factor)) << ", ";
								query << "planet_res_plastic=planet_res_plastic+" << floor(int(arr["sell_plastic"])*return_factor)) << ", ";
								query << "planet_res_fuel=planet_res_fuel+" << floor(int(arr["sell_fuel"])*return_factor)) << ", ";
								query << "planet_res_food=planet_res_food+" << floor(int(arr["sell_food"])*return_factor)) << " ";
							query << "WHERE ";
								query << "planet_id=" << arr["planet_id"] << " ";
								query << "AND planet_user_id=" << arr["user_id"] << ";";
							query.store();
							query.reset();

							// Nachricht senden
							msg = "Folgendes Rohstoffangebot wurde nicht innerhalb von ";
							msg += conf['market_response_time']['v']; //ToDo
							msg += " Tagen gekauft und deshalb gelöscht.\n\n"; 
                    
							msg += "[b]Angebot:[/b]\n";
							msg += "Titan: ";
							msg += functions::nf(arr["sell_metal"]).;
							msg += "\nSilizium: ";
							msg += functions::nf(arr["sell_crystal"]);
							msg += "\nPVC: ";
							msg += functions::nf(arr["sell_plastic"]);
							msg += "\nTritium: ";
							msg += functions::nf(arr["sell_fuel"]);
							msg += "\nNahrung: ";
							msg += functions::nf(arr["sell_food"]);
							msg += "\n\n";
          
							msg += "[b]Preis:[/b]\n";
							msg += "Titan: ";
							msg += functions::nf(arr["buy_metal"]).;
							msg += "\nSilizium: ";
							msg += functions::nf(arr["buy_crystal"]);
							msg += "\nPVC: ";
							msg += functions::nf(arr["buy_plastic"]);
							msg += "\nTritium: ";
							msg += functions::nf(arr["buy_fuel"]);
							msg += "\nNahrung: ";
							msg += functions::nf(arr["buy_food"]);
							msg += "\n\n";
          
							msg += "Du erhälst ";
							msg += round($return_factor,2)*100; //ToDo
							msg += "% deiner Rohstoffe wieder zurück (abgerundet)!\n\n";
          
							msg += "Das Handelsministerium";
							send_msg(arr["user_id"],SHIP_MISC_MSG_CAT_ID,"Angebot gelöscht",msg); //ToDo

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
    	}


		// Löscht alte Schiffsangebote
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "market_ship ";
		query << "WHERE ";
			query << "datum<=(" << time-conf['market_response_time']['v']*3600*24 << ");"); //ToDo
		mysqlpp::Result res = query.store();
		query.reset();
		
		if (res) 
		{
			int resSize = res.size();

			if (resSize>0)
			{
				mysqlpp::Row arr;
				int lastId = 0;

				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					arr = res.at(i);
					
					// Markt Level vom Verkäufer laden
					query << "SELECT ";
						query << "buildlist_current_level ";
					query << "FROM ";
						query << "buildlist ";
					query << "WHERE ";
						query << "buildlist_planet_id=" << arr["planet_id"] << " ";
						query << "AND buildlist_building_id='21' ";
						query << "AND buildlist_current_level>'0' ";
						query << "AND buildlist_user_id=" << arr["user_id"] << ";";
					mysqlpp::Result mres = query.store();
					query.reset();
					
					mysqlpp:Row marr;
					
					marr = mres.at(0);

					// Definiert den Rückgabefaktor
					float return_factor = 1 - (1/(marr["buildlist_current_level"]+1));
          
					// Schiffe dem besitzer zurückgeben (mit dem faktor)
					query << "UPDATE ";
						query << "shiplist ";
					query << "SET ";
						query << "shiplist_count=shiplist_count+" << floor(int(arr["ship_count"])*return_factor)) << " "; 
					query << "WHERE "; 
						query << "shiplist_user_id=" << arr["user_id"] << " "; 
						query << "AND shiplist_planet_id=" << arr["planet_id"] << " ", 
						query << "AND shiplist_ship_id=" << arr["ship_id"] << ";";
					query.store();
					query.reset();

					// Nachricht senden
					msg = "Folgendes Schiffsangebot wurde nicht innerhalb von ";
					msg += conf['market_response_time']['v']; //ToDo
					msg += " Tagen gekauft und deshalb gelöscht.\n\n"; 
                    
					msg +=std::string(arr["ship_name"]);
					msg += ": ";
					msg += functions::nf(arr["ship_count"]); //ToDo
					msg += "\n\n";  
                  
					msg += "[b]Preis:[/b]\n";
					msg += "Titan: ";
					msg += functions::nf(arr["ship_costs_metal"]).;
					msg += "\nSilizium: ";
					msg += functions::nf(arr["ship_costs_crystal"]);
					msg += "\nPVC: ";
					msg += functions::nf(arr["ship_costs_plastic"]);
					msg += "\nTritium: ";
					msg += functions::nf(arr["ship_costs_fuel"]);
					msg += "\nNahrung: ";
					msg += functions::nf(arr["ship_costs_food"]);
					msg += "\n\n";
          
					msg += "Du erhälst ";
					msg += round(return_factor,2)*100); //ToDo
					msg += "% deiner Schiffe wieder zurück (abgerundet)!\n\n";
          
					msg += "Das Handelsministerium";
					send_msg(arr["user_id"],SHIP_MISC_MSG_CAT_ID,"Angebot gelöscht",msg);

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
	}	
}
