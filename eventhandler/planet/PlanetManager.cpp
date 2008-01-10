#include <vector>
#include <iostream>

#include <mysql++/mysql++.h>

#include "PlanetManager.h"

namespace planet
{
	PlanetManager::PlanetManager(mysqlpp::Connection* con, std::vector<int>* planetIds)
	{
		this->con_ = con;
		std::vector<int>* planetIds_ = planetIds;
		std::cout << "PlanetManager initialized...\n";
	}
	
	void PlanetManager::updateValues()
	{
		for (int i=0; i<planetIds_->size(); i++)
		{
			int planetId = planetIds_->at(i);
			
	    mysqlpp::Query query = con_->query();
	  	query << "SELECT "
	    << "    planets.*, "
	    << "    space_cells.*, "
	    << "    races.race_f_metal, "
	    << "    races.race_f_crystal, "
	    << "    races.race_f_plastic, "
	    << "    races.race_f_fuel, "
	   	<< "    races.race_f_food, "
	   	<< "    races.race_f_power, "
	    << "    races.race_f_population, "
	    << "    races.race_f_researchtime, "
	    << "    races.race_f_buildtime, "
	    << "    sol_types.type_name as sol_type_name, "
	    << "    sol_types.type_f_metal as sol_type_f_metal, "
	    << "    sol_types.type_f_crystal as sol_type_f_crystal, "
	    << "    sol_types.type_f_plastic as sol_type_f_plastic, "
	    << "    sol_types.type_f_fuel as sol_type_f_fuel, "
	    << "    sol_types.type_f_food as sol_type_f_food, "
	    << "    sol_types.type_f_power as sol_type_f_power, "
	    << "    sol_types.type_f_population as sol_type_f_population, "
	    << "    sol_types.type_f_researchtime as sol_type_f_researchtime, "
	    << "    sol_types.type_f_buildtime as sol_type_f_buildtime, "
	    << "    planet_types.type_name as planet_type_name, "
	    << "    planet_types.type_f_metal as planet_type_f_metal, "
	    << "    planet_types.type_f_crystal as planet_type_f_crystal, "
	    << "    planet_types.type_f_plastic as planet_type_f_plastic, "
	    << "    planet_types.type_f_fuel as planet_type_f_fuel, "
	    << "    planet_types.type_f_food as planet_type_f_food, "
	    << "    planet_types.type_f_power as planet_type_f_power, "
	    << "    planet_types.type_f_population as planet_type_f_population, "
	    << "    planet_types.type_f_researchtime as planet_type_f_researchtime, "
	    << "    planet_types.type_f_buildtime as planet_type_f_buildtime "
      << "  FROM  "
      << "  { "
      << "  	( "
			<< "			( "
			<< "					planets "
			<< "						INNER JOIN  "
      << "            	planet_types  "
      << "            ON planets.planet_type_id = planet_types.type_id "
      << "            AND planets.planet_id='"<< planetId << "' "
			<< "				) "
      << "        INNER JOIN  "
      << "        (	 "
      << "        	space_cells "
      << "            INNER JOIN sol_types ON space_cells.cell_solsys_solsys_sol_type = sol_types.type_id "
      << "        ) "
      << "        ON planets.planet_solsys_id = space_cells.cell_id "
			<< "			) "
	    << "  		INNER JOIN  "
	    << "  			users  "
	    << "  		ON planets.planet_user_id = users.user_id "
    	<< ") "
    	<< "INNER JOIN  "
    	<< "	races  "
    	<< "ON users.user_race_id = races.race_id	";
	    mysqlpp::Result res = query.store();		
			query.reset();
      mysqlpp::Row row = res.at(0);
	      	



			// Upate fields
			
     
			int fieldsUsed=0;
			int fieldsExtra=0;
			$bres = dbquery("
			SELECT
				SUM(buildings.building_fields*buildlist.buildlist_current_level) AS f,
				SUM(round(buildings.building_fieldsprovide * pow(buildings.building_production_factor,buildlist.buildlist_current_level-1))) AS e
			FROM
                ".$db_table['buildings'].",
                ".$db_table['buildlist']."
			WHERE
                buildlist.buildlist_building_id=buildings.building_id
                AND buildlist.buildlist_current_level>'0'
                AND buildlist.buildlist_planet_id=".$this->id.";");
			if (mysql_num_rows($bres)>0)
			{
				$barr=mysql_fetch_array($bres);
				$this->fields_used+=$barr['f'];
				$this->fields_extra+=$barr['e'];
			}
			$bres = dbquery("
			SELECT
				SUM(defense.def_fields*deflist.deflist_count) AS f
			FROM
				".$db_table['defense'].",
				".$db_table['deflist']."
			WHERE
				deflist.deflist_def_id=defense.def_id
				AND deflist.deflist_planet_id=".$this->id.";");
			if (mysql_num_rows($bres)>0)
			{
				$barr=mysql_fetch_array($bres);
				$this->fields_used+=$barr['f'];
			}






	      	

	      	
	      	
	      	
		}
		
		
		
		



		
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


