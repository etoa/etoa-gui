#include <iostream>

#include <mysql++/mysql++.h>

#include "ColonializeHandler.h"
#include "../../MysqlHandler.H"
#include "../../config/ConfigHandler.h"
#include "../../functions/Functions.h"

namespace colonialize
{
	void ColonializeHandler::update()
	{
	
		/**
		* Fleet-Action: Colonialize
		*/
		Config &config = Config::instance();
		
		// Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["fleet_id"] << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND ship_colonialize='1';";
		mysqlpp::Result fsRes = query.store();
		query.reset();
		
					
		if (fsRes)
		{
			int fsSize = fsRes.size();
			
			if (fsSize > 0)
			{
	
				// Planet auf Besitzer prüfen
				query << "SELECT  ";
				query << "	planet_user_id ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	planet_user_id>0 ";
				query << "	AND id='" << fleet_["fleet_entity_to"] << "';";
				mysqlpp::Result uRes = query.store();
				query.reset();
		
				if (uRes)
				{
					int uSize = uRes.size();
			
					//Planet ist bereits kolonialisiert
					if (uSize > 0)
					{
						mysqlpp::Row uRow = uRes.at(0);
				
						//Planet wurde bereits vom gleichen User kolonialisiert
						if((int)uRow["planet_user_id"] == (int)fleet_["fleet_user_id"])
						{
							//Flotte stationieren & Waren ausladen (ohne abzug eines Kolonieschiffes)
							fleetLand(1,1);

							fleetDelete();

							//Nachricht senden
							std::string msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
							msg += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
							msg += "\n[b]Zeit:[/b] ";
							msg += functions::formatTime((int)fleet_["fleet_landtime"]);
							msg += "\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
							msg += msgAllShips;
							msg += msgRes;
							
							functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte angekommen",msg);
						}
	  
						//Planet gehört bereits an einem anderen User
						else
						{
							//Nachricht senden
							std::string msg = "Die Flotte kann den Planeten nicht kolonialisieren, da er bereits von einem anderen Volk kolonialisiert wurde!\n";
					
							functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Landung nicht möglich",msg);

							// Flotte zurückschicken
							fleetReturn("koc");
						}
					}
			
					// Planet ist noch frei und kann kolonialisiert werden
					else
					{
						// Auf eigene Maximalanzahl prüfen
						query << "SELECT ";
						query << "	COUNT(planet_user_id) AS cnt ";
						query << "FROM ";
						query << "	planets ";
						query << "WHERE ";
						query << "	planet_user_id='" << fleet_["fleet_user_id"] << "';";
						mysqlpp::Result uRes = query.store();
						query.reset();
				
						if (uRes)
						{
							int uSize = uRes.size();
					
							if (uSize > 0)
							{
								mysqlpp::Row uRow = uRes.at(0);
		
								// Spieler hat bereits maximalanzahl an Planeten
								if ((int)uRow["cnt"] >= config.nget("user_max_planets",0))
								{
									//Nachricht senden
									std::string msg = "Die Flotte kann den Planeten nicht kolonialisieren, da die maximale Zahl an Planeten auf denen du regieren darfst, bereits erreicht worden ist!\n";
							
									functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Landung nicht möglich",msg);
	
									// Flotte zurückschicken
									fleetReturn("koc");
								}
    
								//Kolonie erfolgreich gewonnen
								else
								{
									//Planet zurücksetzen
									functions::resetPlanet((int)fleet_["fleet_entity_to"]); //ToDo

									// Planet übernehmen
									query << "UPDATE ";
									query << "	planets ";
									query << "SET ";
									query << "	planet_user_id='" << fleet_["fleet_user_id"] << "', ";
									query << "	planet_name='Unbenannt' ";
									query << "WHERE ";
									query << "	id='" << fleet_["fleet_entity_to"] << "';";
									query.store();
									query.reset();

									//Flotte stationieren & Waren ausladen (mit abzug eines Kolonieschiffes)
									fleetLand(1);

									// Flotte-Schiffe-Verknüpfungen löschen
									fleetDelete();

									//Nachricht senden
									std::string msg = "Die Flottehat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
									msg += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
									msg += "\n[b]Zeit:[/b] ",
									msg	+= functions::formatTime((int)fleet_["fleet_landtime"]);
									msg += "\n";
									msg += "[b]Bericht:[/b] Die Flotte hat eine neue Kolonie errichtet! Dabei wurde ein Besiedlungsschiff verbraucht.\n";
									msg += msgAllShips;
									msg += msgRes;
						
									functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Planet kolonialisiert",msg);
								}
							}
						}
					}
				}
			}
			else
			{
				std::string text = "Eine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
				text += " versuchte, eine Kolonie zu errichten. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Kolonisieren gescheitert",text);
				
				fleetReturn("kr");
			}
		}
	}
}

