#include <vector>
#include <iostream>

#include <mysql++/mysql++.h>
#include <math.h>

#include "PlanetManager.h"
#include "../planet/Planet.h"
#include "../functions/Functions.h"

namespace planet
{
	PlanetManager::PlanetManager(mysqlpp::Connection* con, std::vector<int>* planetIds)
	{
		this->con_ = con;
		std::vector<int>* planetIds_ = planetIds;
		std::cout << "PlanetManager initialized...\n";
	}
	
	void PlanetManager::updateValues(std::vector<int>* planetIds)
	{
		
		std::vector<int>* planetIds_ = planetIds;
		for (int i=0; i<planetIds_->size(); i++)
		{
			int planetId = planetIds_->at(i);
			int fieldsUsed = 0;
			int fieldsExtra = 0;
			std::vector<int> store (6);
			std::vector<double> cnt (8);
			
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "    planets.*, ";
			query << "    space_cells.*, ";
			query << "    races.race_f_metal, ";
			query << "    races.race_f_crystal, ";
			query << "    races.race_f_plastic, ";
			query << "    races.race_f_fuel, ";
			query << "    races.race_f_food, ";
			query << "    races.race_f_power, ";
			query << "    races.race_f_population, ";
			query << "    races.race_f_researchtime, ";
			query << "    races.race_f_buildtime, ";
			query << "    sol_types.type_name as sol_type_name, ";
			query << "    sol_types.type_f_metal as sol_type_f_metal, ";
			query << "    sol_types.type_f_crystal as sol_type_f_crystal, ";
			query << "    sol_types.type_f_plastic as sol_type_f_plastic, ";
			query << "    sol_types.type_f_fuel as sol_type_f_fuel, ";
			query << "    sol_types.type_f_food as sol_type_f_food, ";
			query << "    sol_types.type_f_power as sol_type_f_power, ";
			query << "    sol_types.type_f_population as sol_type_f_population, ";
			query << "    sol_types.type_f_researchtime as sol_type_f_researchtime, ";
			query << "    sol_types.type_f_buildtime as sol_type_f_buildtime, ";
			query << "    planet_types.type_name as planet_type_name, ";
			query << "    planet_types.type_f_metal as planet_type_f_metal, ";
			query << "    planet_types.type_f_crystal as planet_type_f_crystal, ";
			query << "    planet_types.type_f_plastic as planet_type_f_plastic, ";
			query << "    planet_types.type_f_fuel as planet_type_f_fuel, ";
			query << "    planet_types.type_f_food as planet_type_f_food, ";
			query << "    planet_types.type_f_power as planet_type_f_power, ";
			query << "    planet_types.type_f_population as planet_type_f_population, ";
			query << "    planet_types.type_f_researchtime as planet_type_f_researchtime, ";
			query << "    planet_types.type_f_buildtime as planet_type_f_buildtime ";
			query << "FROM  ";
			query << "  ( ";
			query << "  	( ";
			query << "			( ";
			query << "					planets ";
			query << "						INNER JOIN  ";
			query << "            	planet_types ";
			query << "            ON planets.planet_type_id = planet_types.type_id ";
			query << "            AND planets.planet_id='"<< planetId << "' ";
			query << "				) ";
			query << "        INNER JOIN  ";
			query << "        (	 ";
			query << "        	space_cells ";
			query << "            INNER JOIN sol_types ON space_cells.cell_solsys_solsys_sol_type = sol_types.type_id ";
			query << "        ) ";
			query << "        ON planets.planet_solsys_id = space_cells.cell_id ";
			query << "			) ";
			query << "  		INNER JOIN  ";
			query << "  			users  ";
			query << "  		ON planets.planet_user_id = users.user_id ";
			query << ") ";
			query << "INNER JOIN  ";
			query << "	races  ";
			query << "ON users.user_race_id = races.race_id;";
			mysqlpp::Result res = query.store();		
				query.reset();
			mysqlpp::Row row = res.at(0);
	      	
			updateFields(planetId, fieldsUsed, fieldsExtra);
			updateStorage(planetId, store);
			updateProductionRates(planetId, cnt, row);
			save(planetId, store, cnt, fieldsUsed, fieldsExtra);
			std::cout << fieldsUsed << ":" << fieldsExtra << ":" << store[0] << ":" << cnt[0] << "\n";
		}
				
	}

	

	void PlanetManager::updateFields(int planetId, int& fieldsUsed, int& fieldsExtra)
	{
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "SUM(buildings.building_fields*buildlist.buildlist_current_level) AS f, ";
			query << "SUM(round(buildings.building_fieldsprovide * pow(buildings.building_production_factor,buildlist.buildlist_current_level-1))) AS e ";
		query << "FROM ";
			query << "buildings, ";
			query << "buildlist ";
		query << "WHERE ";
			query << "buildlist.buildlist_building_id=buildings.building_id ";
			query << "AND buildlist.buildlist_current_level>'0' ";
			query << "AND buildlist.buildlist_planet_id=" << planetId << ";";
		mysqlpp::Result bres = query.store();		
			query.reset();

		if (bres) 
		{
			int bresSize = bres.size();
			if (bresSize>0)
			{
				mysqlpp::Row brow;
				for (mysqlpp::Row::size_type i = 0; i<bresSize; i++) 
				{
					brow = bres.at(i);
					fieldsUsed+=int(brow["f"]);
					fieldsExtra+=int(brow["e"]);
				}
			}
		}
			
		query << "SELECT ";
			query << "SUM(defense.def_fields*deflist.deflist_count) AS f ";
		query << "FROM ";
			query << "deflist, ";
			query << "defense ";
		query << "WHERE ";
			query << "deflist.deflist_def_id=defense.def_id ";
			query << "AND deflist.deflist_planet_id='" << planetId << "';";
		mysqlpp::Result dres = query.store();		
			query.reset();

		if (dres) 
		{
			int dresSize = dres.size();
			if (dresSize>0)
			{	
				mysqlpp::Row drow;
				for (mysqlpp::Row::size_type i = 0; i<dresSize; i++) 
				{
					drow = dres.at(i);
					std::string stemp = std::string(drow["f"]);
					if (stemp!="NULL")
					{
						int itemp = drow["f"];
						fieldsUsed += itemp;
					}
		
				}
			}
		}
	}

	void PlanetManager::updateStorage(int planetId, std::vector<int>& store)
	{
		// Basic store capacity
		store[0] = 200000; //=intval($conf['def_store_capacity']['v']);
		store[1] = 200000; //=intval($conf['def_store_capacity']['v']);
		store[2] = 200000; //=intval($conf['def_store_capacity']['v']);
		store[3] = 200000; //=intval($conf['def_store_capacity']['v']);
		store[4] = 200000; //=intval($conf['def_store_capacity']['v']);
		store[5] = 250; //=intval($conf['user_start_people']['p1']);

		// Storage capacity provided by buildings
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "buildings.building_people_place, ";
			query << "buildings.building_store_metal, ";
			query << "buildings.building_store_crystal, ";
			query << "buildings.building_store_plastic, ";
			query << "buildings.building_store_fuel, ";
			query << "buildings.building_store_food, ";
			query << "buildings.building_store_factor, ";
			query << "buildlist.buildlist_current_level ";
		query << "FROM ";
			query << "buildings ";
			query << "INNER JOIN ";
			query << "buildlist ";
			query << "ON buildings.building_id = buildlist.buildlist_building_id ";
			query << "AND buildlist.buildlist_planet_id=" << planetId << " ";
			query << "AND buildlist.buildlist_current_level>0 ";
			query << "AND (buildings.building_store_metal>0 ";
			query << "OR buildings.building_store_crystal>0 ";
			query << "OR buildings.building_store_plastic>0 ";
			query << "OR buildings.building_store_fuel>0 ";
			query << "OR buildings.building_store_food>0 ";
			query << "OR buildings.building_people_place>0);";
		mysqlpp::Result sres = query.store();		
			query.reset();
				
		if (sres) 
		{
			int sresSize = sres.size();
			if (sresSize>0)
			{	
				mysqlpp::Row srow;
				for (mysqlpp::Row::size_type i = 0; i<sresSize; i++) 
				{
					srow = sres.at(i);
					int level = srow["buildlist_current_level"]-1;
					store[0] += functions::round(srow["building_store_metal"] * pow(srow["building_store_factor"],level));
					store[1] += functions::round(srow["building_store_crystal"] * pow(srow["building_store_factor"],level));
					store[2] += functions::round(srow["building_store_plastic"] * pow(srow["building_store_factor"],level));
					store[3] += functions::round(srow["building_store_fuel"] * pow(srow["building_store_factor"],level));
					store[4] += functions::round(srow["building_store_food"] * pow(srow["building_store_factor"],level));
					store[5] += functions::round(srow["building_people_place"] * pow(srow["building_store_factor"],level));
				}
			}
		}
	}
	
	void PlanetManager::updateProductionRates(int planetId, std::vector<double>& cnt, mysqlpp::Row& row)
	{
	
				// production rates
			/*
			// Spezialisten-Boni laden
			$sres = dbquery("
			SELECT
				* 
			FROM
			
			WHERE
			;");
			*/

		// Produktionsraten berechnen
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "buildings.building_prod_metal, ";
			query << "buildings.building_prod_crystal, ";
			query << "buildings.building_prod_plastic, ";
			query << "buildings.building_prod_fuel, ";
			query << "buildings.building_prod_food, ";
			query << "buildings.building_prod_power, ";
			query << "buildings.building_production_factor, ";
			query << "buildings.building_power_use, ";
			query << "buildlist.buildlist_prod_percent, ";
			query << "buildlist.buildlist_current_level ";
		query << "FROM ";
			query << "buildings ";
		query << "INNER JOIN ";
			query << "buildlist ";
			query  << "ON buildings.building_id = buildlist.buildlist_building_id ";
			query << "AND buildlist.buildlist_planet_id=" << planetId << " ";
			query << "AND buildlist.buildlist_current_level>0 ";
			query << "AND (buildings.building_prod_metal>0 ";
			query << "OR buildings.building_prod_crystal>0 ";
			query << "OR buildings.building_prod_plastic>0 ";
			query << "OR buildings.building_prod_fuel>0 ";
			query << "OR buildings.building_prod_food>0 ";
			query << "OR buildings.building_prod_power>0 ";
			query << "OR buildings.building_power_use>0) ";
		query << "ORDER BY ";
			query << "buildings.building_type_id, ";
			query << "buildings.building_order;";
		mysqlpp::Result bres = query.store();		
			query.reset();

		if (bres) 
		{
			int bresSize = bres.size();
			if (bresSize>0)
			{
				mysqlpp::Row brow;
				for (mysqlpp::Row::size_type i = 0; i<bresSize; i++) 
				{
					brow = bres.at(i);
					int level = brow["buildlist_current_level"] - 1;
					cnt[0] += ceil(float(brow["building_prod_metal"]) * float(brow["buildlist_prod_percent"]) * pow(float(brow["building_production_factor"]),level));
					cnt[1] += ceil(float(brow["building_prod_crystal"]) * float(brow["buildlist_prod_percent"]) * pow(float(brow["building_production_factor"]),level));
					cnt[2] += ceil(float(brow["building_prod_plastic"]) * float(brow["buildlist_prod_percent"]) * pow(float(brow["building_production_factor"]),level));
					cnt[3] += ceil(float(brow["building_prod_fuel"]) * float(brow["buildlist_prod_percent"]) * pow(float(brow["building_production_factor"]),level));
					cnt[4] += ceil(float(brow["building_prod_food"]) * float(brow["buildlist_prod_percent"]) * pow(float(brow["building_production_factor"]),level));
					cnt[6] += floor(float(brow["building_prod_power"]) * float(brow["buildlist_prod_percent"]) * pow(float(brow["building_production_factor"]),level));
					cnt[7] += float(brow["building_power_use"]) * float(brow["buildlist_prod_percent"]) * pow(float(brow["building_production_factor"]),level);
				}
			}
		}

		query << "SELECT ";
			query << "shiplist_count, ";
			query << "ship_prod_power ";
		query << "FROM ";
			query << "shiplist ";
		query << "INNER JOIN ";
			query << "ships ";
			query << "ON shiplist_ship_id=ship_id ";
			query<< "AND shiplist_planet_id=" << planetId << " ";
			query << "AND ship_prod_power>0;";
		mysqlpp::Result sres = query.store();		
			query.reset();

		if (sres) 
		{
			int sresSize = sres.size();
			if (sresSize>0)
			{
				int dtemp = functions::getSolarPowerBonus(int(row["planet_temp_from"]), int(row["planet_temp_to"]));
				mysqlpp::Row srow;
				for (mysqlpp::Row::size_type i = 0; i<sresSize; i++) 
				{
					srow = sres.at(i);
					cnt[6] += (float(srow["ship_prod_power"]) + dtemp) * int(srow["shiplist_count"]);
				}
			}	
		}
			
		// Addieren der Planeten- und Rassenboni
		cnt[0] += (cnt[0] * (float(row["planet_type_f_metal"]) + float(row["race_f_metal"]) + float(row["sol_type_f_metal"]) - 3));
		cnt[1] += (cnt[1] * (float(row["planet_type_f_crystal"]) + float(row["race_f_crystal"]) + float(row["sol_type_f_crystal"]) - 3));
		cnt[2] += (cnt[2] * (float(row["planet_type_f_plastic"]) + float(row["race_f_plastic"]) + float(row["sol_type_f_plastic"]) - 3));
		cnt[3] += (cnt[3] * (float(row["planet_type_f_fuel"]) + float(row["race_f_fuel"]) + float(row["sol_type_f_fuel"]) - 3));
		cnt[4] += (cnt[4] * (float(row["planet_type_f_food"]) + float(row["race_f_food"]) + float(row["sol_type_f_food"]) - 3));
		cnt[6] += (cnt[6] * (float(row["planet_type_f_power"]) + float(row["race_f_power"]) + float(row["sol_type_f_power"]) - 3));

		// Bei ungenügend Energie Anpassung vornehmen
		if (cnt[7]>=cnt[6])
		{
			cnt[0] = floor(cnt[0] * cnt[6] / cnt[7]);
			cnt[1] = floor(cnt[1] * cnt[6] / cnt[7]);
			cnt[2] = floor(cnt[2] * cnt[6] / cnt[7]);
			cnt[3] = floor(cnt[3] * cnt[6] / cnt[7]);
			cnt[4] = floor(cnt[4] * cnt[6] / cnt[7]);
		}

		// Berechnet noch Bewohnerproduktion (Dieser Wert ist nur für die Wirtschaftsübersicht zur Speicherberechnung)
		cnt[5] = 1.1 + row["planet_type_f_population"] +row["race_f_population"] + row["sol_type_f_population"] -3; //$conf['people_multiply']['v']
		cnt[5]= row["planet_people"]/50 * (cnt[5]);
		if(cnt[5]<=3)
		{
			cnt[5]=3;
		}

		cnt[0] = floor(cnt[0]);
		cnt[1] = floor(cnt[1]);
		cnt[2] = floor(cnt[2]);
		cnt[3] = floor(cnt[3]);
		cnt[4] = floor(cnt[4]);
		cnt[6] = floor(cnt[6]);
	}
	
		/**
		* Saves updated content
		*/
	void PlanetManager::save(int planetId, std::vector<int>& store, std::vector<double>& cnt, int fieldsUsed, int fieldsExtra)
	{
		mysqlpp::Query query = con_->query();
		query << "UPDATE ";
			query << "planets ";
		query << "SET ";
			query << "planet_store_metal=" << store[0] << ", ";
			query << "planet_store_crystal=" << store [1] << ", ";
			query << "planet_store_plastic=" << store[2] << ", ";
			query << "planet_store_fuel=" << store[3] << ", ";
			query << "planet_store_food=" << store[4] << ", ";
			query << "planet_people_place=" << store[5] << ", ";
			query << "planet_fields_used=" << fieldsUsed << ", ";
			query << "planet_fields_extra=" << fieldsExtra << ", ";
			query << "planet_prod_metal=" << cnt[0] << ", ";
			query << "planet_prod_crystal=" << cnt[1] << ", ";
			query << "planet_prod_plastic=" << cnt[2] << ", ";
			query << "planet_prod_fuel=" << cnt[3] << ", ";
			query << "planet_prod_food=" << cnt[4] << ", ";
			query << "planet_prod_power=" << cnt[6] << ", ";
			query << "planet_prod_people=" << cnt[5] << ", ";
			query << "planet_use_power=" << cnt[7] << " ";      	
     	query << "WHERE ";
			query << "planet_id='" << planetId << "';";
		query.store();
		query.reset();
	}

	void PlanetManager::updateEconomy()
	{
		
	}
	



/*
		
		for (int x=0;x<planetIds_->size();x++)
		{
			std::cout << "Planet: "<<x<<"\n";
		}
*/

}


