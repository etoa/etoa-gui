
#include "../MysqlHandler.h"

#include "ConfigHandler.h"

	/** Liefert die Configwerte als string **/
	std::string Config::get(std::string name, int value)
	{
		std::vector<std::string> temp (3);
		temp = cConfig.at(sConfig[name]);
		return(temp[value]);
	}
	
	/** Liefert die Configwerte als double **/
	double Config::nget(std::string name, int value)
	{
		std::string temp = get(name, value);
		double var = atof(temp.data());
		return(var);
	}
	
	/** Liefert die Zahlenwerte gespeicherter Werte **/
	double Config::idget(std::string name)
	{
		return(idConfig[name]);
	}
	
	/** Liefert die Zahlenwerte gespeicherter Werte **/
	short Config::getAction(std::string action)
	{
		return(actions[action]);
	}
	
	std::string Config::getActionName(std::string action)
	{
		return(actionName[action]);
	}
	
	// Setzt den Rundenname
	void Config::setRoundName(std::string name)
	{
		this->gameRound = name;
		loadConfig();
	}
	
	// Liefert den Frontendpfad zurück
	std::string Config::getFrontendPath()
	{
		return "/var/www/" + this->gameRound + ".etoa.ch/htdocs/config";
	}

	void Config::reloadConfig()
	{
		this->sConfig.clear();
		this->idConfig.clear();
		this->cConfig.clear();
		this->actions.clear();
		this->actionName.clear();
		
		loadConfig();
}
	
	/**	Initialisiert die Configwerte **/
	void Config::loadConfig ()
	{
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		
		int counter = 0;
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	config_name, ";
		query << "	config_value, ";
		query << "	config_param1, ";
		query << "	config_param2 ";
		query << "FROM ";
		query << "	config;";
		mysqlpp::Result res = query.store();	
		query.reset();
		if (res) 
		{
			unsigned int resSize = res.size();
			if (resSize>0) {
				mysqlpp::Row row;
				cConfig.reserve(resSize);
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
					row = res.at(i);
					sConfig[std::string(row["config_name"]) ] =  (int)i;
					std::vector<std::string> temp (3);
					temp[1]=std::string(row["config_param1"]);
					temp[2]=std::string(row["config_param2"]);
					temp[0]=std::string(row["config_value"]);
					cConfig.push_back(temp);
					counter = i;
				}
			}
		}
		
		this->calcCollectFuelValues();
		
		query << "SELECT ";
		query << " id ";
		query << "FROM ";
		query << "	entities ";
		query << "WHERE ";
		query << "	code='m' ";
		query << "LIMIT 1;";
		mysqlpp::Result mRes = query.store();
		query.reset();
		
		if (mRes) {
			int mSize = mRes.size();
			
			if (mSize > 0) {
				counter++;
				mysqlpp::Row mRow = mRes.at(0);
				sConfig["market_entity"] = counter;
				std::vector<std::string> temp (3);
				temp[1]=std::string("0");
				temp[2]=std::string("0");
				temp[0]=std::string(mRow["id"]);
				cConfig.push_back(temp);
			}
		}
		
		/** ID Werte (müssen manuel hier reingeschrieben werden) **/
		//->Nachrichten
		idConfig["SHIP_WAR_MSG_CAT_ID"] = 3;
		idConfig["SHIP_MONITOR_MSG_CAT_ID"] = 4;
		idConfig["SHIP_MISC_MSG_CAT_ID"] = 5;
		idConfig["SHIP_SPY_MSG_CAT_ID"] = 2;
		
		//->Schiffe
		idConfig["MARKET_SHIP_ID"] = 16;
		
		//->Technologien
		idConfig["SPY_TECH_ID"] = 7;
		idConfig["STRUCTURE_TECH_ID"] = 9;
		idConfig["TARN_TECH_ID"] = 11;
		idConfig["SHIELD_TECH_ID"] = 10;
		idConfig["WEAPON_TECH_ID"] = 8;
		idConfig["BOMB_TECH_ID"] = 15;
		idConfig["Gifttechnologie"] = 18;
		idConfig["REGENA_TECH_ID"] = 19;
		
		//->Gebäude
		idConfig["FLEET_CONTROL_ID"] = 11;
		idConfig["FACTORY_ID"] = 10;
		idConfig["SHIPYARD_ID"] = 9;
		idConfig["BUILD_MISSILE_ID"] = 25;
		idConfig["BUILD_CRYPTO_ID"] = 24;		
		
		//->Spionage
		idConfig["SPY_DEFENSE_FACTOR_TECH"] = 20;
		idConfig["SPY_DEFENSE_FACTOR_SHIPS"] = 0.5;
		idConfig["SPY_DEFENSE_MAX"] = 90;
		idConfig["SPY_DEFENSE_FACTOR_TARN"] = 10;
			
		idConfig["SPY_ATTACK_SHOW_BUILDINGS"] = 1;
		idConfig["SPY_ATTACK_SHOW_RESEARCH"] = 3;
		idConfig["SPY_ATTACK_SHOW_DEFENSE"] = 5;
		idConfig["SPY_ATTACK_SHOW_SHIPS"] = 7;
		idConfig["SPY_ATTACK_SHOW_RESSOURCEN"] = 9;
		idConfig["SPY_ATTACK_SHOW_SUPPORT"] = 9;
		
		//->Flottenaktionen
		actions["analyze"] = 1;
		actions["antrax"] = 2;
		actions["attack"] = 3;
		actions["bombard"] = 4;
		actions["collectmetal"] = 5;
		actions["collectcrystal"] = 6;
		actions["collectdebris"] = 7;
		actions["collectfuel"] = 8;
		actions["colonize"] = 9;
		actions["createdebris"] = 10;
		actions["delivery"] = 11;		
		actions["emp"] = 12;
		actions["explore"] = 13;
		actions["fetch"] = 14;
		actions["gasattack"] = 15;
		actions["invade"] = 16;
		actions["market"] = 17;
		actions["position"] = 18;
		actions["spy"] = 19;
		actions["spyattack"] = 20;
		actions["stealthattack"] = 21;
		actions["support"] = 22;
		actions["transport"] = 23;
		actions["alliance"] = 24;
		
		actionName["analyze"] = "Analysieren";
		actionName["antrax"] = "Antraxangriff";
		actionName["attack"] = "Angriff";
		actionName["bombard"] = "Bombardierung";
		actionName["collectmetal"] = "Asteroiden sammeln";
		actionName["collectcrystal"] = "Sternennebel sammeln";
		actionName["collectdebris"] = "Trümmer sammeln";
		actionName["collectfuel"] = "Gas saugen";
		actionName["colonize"] = "Kolonialisieren";
		actionName["createdebris"] = "Trümmerfeld erstellen";
		actionName["delivery"] = "Allianzlieferung";
		actionName["flight"] = "Flug";
		actionName["emp"] = "EMP-Attacke";
		actionName["explore"] = "Erkunden";
		actionName["fakeattack"] = "Täuschungsangriff";
		actionName["fetch"] = "Waren abholen";
		actionName["gasattack"] = "Gasangriff";
		actionName["invade"] = "Invasion";
		actionName["market"] = "Marktlieferung";
		actionName["position"] = "Stationieren";
		actionName["spy"] = "Ausspionieren";
		actionName["spyattack"] = "Spionageangriff";
		actionName["stealthattack"] = "Tarnangriff";
		actionName["support"] = "Unterstützen";
		actionName["transport"] = "Waren transportieren";
		actionName["alliance"] = "Allianzangriff";
	}

	void Config::calcCollectFuelValues()
	{
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		
		std::map<int,double> capaContainer;
		
		mysqlpp::Query query = con->query();
		query << "SELECT "
			<< "	(shiplist_count*ship_capacity) AS sl_capa, "
			<< "	shiplist_user_id "
			<< "FROM "
			<< "	shiplist "
			<< "INNER JOIN "
			<< "	ships "
			<< "ON "
			<< "	ship_id=shiplist_ship_id "
			<< "	AND ship_actions LIKE '%collectfuel%' "
			<< "GROUP BY "
			<< "	shiplist_user_id";
		mysqlpp::Result res = query.store();
		query.reset();
		
		if (res)
		{
			unsigned int resSize = res.size();
			
			if (resSize>0)
			{
	    		mysqlpp::Row row;
	    		for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
	    			row = res.at(i);
					
					capaContainer[(int)row["shiplist_user_id"] ] = (double)row["sl_capa"];
				}
			}
		}
		
		query << "SELECT "
			<< "	(fs_ship_cnt*ship_capacity) AS fs_capa, "
			<< "	user_id "
			<< "FROM "
			<< "	fleet "
			<< "INNER JOIN "
			<< "	fleet_ships "
			<< "ON "
			<< "	id=fs_fleet_id "
			<< "INNER JOIN "
			<< "	ships "
			<< "ON "
			<< "	fs_ship_id=ship_id "
			<< "	AND	ship_actions LIKE '%collectfuel%' "
			<< "GROUP BY "
			<< "	user_id ";
		res = query.store();
		query.reset();
		
		if (res)
		{
			unsigned int resSize = res.size();
			
			if (resSize>0)
			{
	    		mysqlpp::Row row;
	    		for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
	    			row = res.at(i);
					
					capaContainer[(int)row["user_id"] ] += (double)row["fs_capa"];
				}
			}
		}
		
		double maxCapa = this->nget("gasplanet",2) * this->nget("planet_fields",2);
		double hoursToRefill = maxCapa / this->nget("gasplanet",1);
		std::map<int,double>::iterator it;
		
		for ( it=capaContainer.begin() ; it != capaContainer.end(); it++ )
			maxCapa = std::max((*it).second,maxCapa);

		cConfig[sConfig["gasplanet"]][1] = etoa::toString(maxCapa / hoursToRefill);
		cConfig[sConfig["gasplanet"]][2] = etoa::toString(maxCapa/this->nget("planet_fields",2));
	}

