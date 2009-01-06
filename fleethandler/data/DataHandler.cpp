#include <map>
#include <vector>
#include <string>
#include "../MysqlHandler.h"
#include <mysql++/mysql++.h>

#include "DataHandler.h"
#include "Data.h"
#include "ShipData.h"
#include "DefData.h"
#include "TechData.h"
#include "BuildingData.h"

	// Liefert eine Kopie des Datenwertes zurück
	ShipData::ShipData* DataHandler::getShipById(int id) {
		return this->shipData[idShipConverter[id] ];
	}
	
	DefData::DefData* DataHandler::getDefById(int id) {
		return this->defData[idDefConverter[id] ];
	}
	
	TechData::TechData* DataHandler::getTechById(int id) {
		return this->techData[idTechConverter[id] ];
	}
	
	BuildingData::BuildingData* DataHandler::getBuildingById(int id) {
		return this->buildingData[idBuildingConverter[id] ];
	}
	
	ShipData::ShipData* DataHandler::getShipByName(std::string name) {
		return this->shipData[nameConverter[name] ];
	}
	
	DefData::DefData* DataHandler::getDefByName(std::string name) {
		return this->defData[nameConverter[name] ];
	}
			
	TechData::TechData* DataHandler::getTechByName(std::string name) {
		return this->techData[nameConverter[name] ];
	}
			
	BuildingData::BuildingData* DataHandler::getBuildingByName(std::string name) {
		return this->buildingData[nameConverter[name] ];
	}
	
	// Clear the Data and reload it
	void DataHandler::reloadData()
	{
		this->defData.clear();
		this->shipData.clear();
		this->techData.clear();
		this->buildingData.clear();
		this->idDefConverter.clear();
		this->idShipConverter.clear();
		this->idTechConverter.clear();
		this->idBuildingConverter.clear();
		
		loadData();
	}
	
	
	//	Initialisiert die Datenwerte
	void DataHandler::loadData ()
	{
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();

		this->counter = 0;
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	def_id AS id, ";
		query << "	def_name AS name, ";
		query << "	def_shortcomment AS shortcomment, ";
		query << "	def_longcomment AS longcomment, ";
		query << "	def_costs_metal AS costs_metal, ";
		query << "	def_costs_crystal AS costs_crystal, ";
		query << "	def_costs_plastic AS costs_plastic, ";
		query << "	def_costs_fuel AS costs_fuel, ";
		query << "	def_costs_food AS costs_food, ";
		query << "	def_costs_power AS costs_power, ";
		query << "	def_power_use, ";
		query << "	def_fuel_use, ";
		query << "	def_show, ";
		query << "	def_buildable, ";
		query << "	def_order, ";
		query << "	def_heal, ";
		query << "	def_structure, ";
		query << "	def_shield, ";
		query << "	def_weapon, ";
		query << "	def_race_id, ";
		query << "	def_cat_id, ";
		query << "	def_max_count, ";
		query << "	def_points, ";
		query << "	def_jam, ";
		query << "	def_fields ";
		query << "FROM ";
		query << "	defense;";
		mysqlpp::Result dRes = query.store();	
		query.reset();
		if (dRes) {
			int dSize = dRes.size();
			DefData* object;

			if (dSize>0) {
				mysqlpp::Row dRow;

				for (mysqlpp::Row::size_type i = 0; i<dSize; i++) {
					dRow = dRes.at(i);
					this->idDefConverter[(int)(dRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(dRow["name"]) ] = this->counter;
					object = new DefData(dRow);
					this->defData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;
		query << "SELECT ";
		query << "	ship_id AS Id, ";
		query << "	ship_name AS Name, ";
		query << "	ship_type_id, ";
		query << "	ship_shortcomment AS shortcomment, ";
		query << "	ship_longcomment AS longcomment, ";
		query << "	ship_costs_metal AS costs_metal, ";
		query << "	ship_costs_crystal AS costs_crystal, ";
		query << "	ship_costs_plastic AS costs_plastic, ";
		query << "	ship_costs_fuel AS costs_fuel, ";
		query << "	ship_costs_food AS costs_food, ";
		query << "	ship_costs_power AS costs_power, ";
		query << "	ship_power_use, ";
		query << "	ship_fuel_use, ";
		query << "	ship_fuel_use_launch, ";
		query << "	ship_fuel_use_landing, ";
		query << "	ship_prod_power, ";
		query << "	ship_capacity, ";
		query << "	ship_people_capacity, ";
		query << "	ship_pilots, ";
		query << "	ship_speed, ";
		query << "	ship_time2start, ";
		query << "	ship_time2land, ";
		query << "	ship_show, ";
		query << "	ship_buildable, ";
		query << "	ship_order, ";
		query << "	ship_actions, ";
		query << "	ship_bounty_bonus, ";
		query << "	ship_heal, ";
		query << "	ship_structure, ";
		query << "	ship_shield, ";
		query << "	ship_weapon, ";
		query << "	ship_race_id, ";
		query << "	ship_launchable, ";
		query << "	ship_fieldsprovide, ";
		query << "	ship_cat_id, ";
		query << "	ship_fakeable, ";
		query << "	special_ship, ";
		query << "	ship_max_count, ";
		query << "	special_ship_max_level, ";
		query << "	special_ship_need_exp, ";
		query << "	special_ship_exp_factor, ";
		query << "	special_ship_bonus_weapon, ";
		query << "	special_ship_bonus_structure, ";
		query << "	special_ship_bonus_shield, ";
		query << "	special_ship_bonus_heal, ";
		query << "	special_ship_bonus_capacity, ";
		query << "	special_ship_bonus_speed, ";
		query << "	special_ship_bonus_pilots, ";
		query << "	special_ship_bonus_tarn, ";
		query << "	special_ship_bonus_antrax, ";
		query << "	special_ship_bonus_forsteal, ";
		query << "	special_ship_bonus_build_destroy, ";
		query << "	special_ship_bonus_antrax_food, ";
		query << "	special_ship_bonus_deactivade, ";
		query << "	ship_points, ";
		query << "	ship_alliance_shipyard_level, ";
		query << "	ship_alliance_costs ";
		query << "FROM ";
		query << "	ships;";
		mysqlpp::Result sRes = query.store();	
		query.reset();
		if (sRes) {
			int sSize = sRes.size();
			ShipData* object;

			if (sSize>0) {
				mysqlpp::Row sRow;
				
				for (mysqlpp::Row::size_type i = 0; i<sSize; i++) {
					sRow = sRes.at(i);
					this->idShipConverter[(int)(sRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(sRow["name"]) ] = this->counter;
					object = new ShipData(sRow);
					this->shipData.push_back(object);
					this->counter++;
					/** Save the id in the action container **/
					
					
				}
			}
		}
		
		this->counter = 0;
		query << "SELECT ";
		query << "	tech_id AS Id, ";
		query << "	tech_name AS Name, ";
		query << "	tech_type_id, ";
		query << "	tech_shortcomment AS shortcomment, ";
		query << "	tech_longcomment AS longcomment, ";
		query << "	tech_costs_metal AS costs_metal, ";
		query << "	tech_costs_crystal AS costs_crystal, ";
		query << "	tech_costs_plastic AS costs_plastic, ";
		query << "	tech_costs_fuel AS costs_fuel, ";
		query << "	tech_costs_food AS costs_food, ";
		query << "	tech_costs_power AS costs_power, ";
		query << "	tech_build_costs_factor, ";
		query << "	tech_last_level, ";
		query << "	tech_show, ";
		query << "	tech_order, ";
		query << "	tech_stealable ";
		query << "FROM ";
		query << "	technologies;";
		mysqlpp::Result tRes = query.store();	
		query.reset();
		if (tRes) {
			int tSize = tRes.size();
			TechData* object;

			if (tSize>0) {
				mysqlpp::Row tRow;
				
				for (mysqlpp::Row::size_type i = 0; i<tSize; i++) {
					tRow = tRes.at(i);
					this->idTechConverter[(int)(tRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(tRow["name"]) ] = this->counter;
					object = new TechData(tRow);
					this->techData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;	 	 	 	 	 	 	 	 	 	 	 	 	 	
		query << "SELECT ";
		query << "	building_id AS Id, ";
		query << "	building_name AS Name, ";
		query << "	building_type_id, ";
		query << "	building_shortcomment AS shortcomment, ";
		query << "	building_longcomment AS longcomment, ";
		query << "	building_costs_metal AS costs_metal, ";
		query << "	building_costs_crystal AS costs_crystal, ";
		query << "	building_costs_plastic AS costs_plastic, ";
		query << "	building_costs_fuel AS costs_fuel, ";
		query << "	building_costs_food AS costs_food, ";
		query << "	building_costs_power AS costs_power, ";
		query << "	building_build_costs_factor, ";
		query << "	building_demolish_costs_factor, ";
		query << "	building_power_use, ";
		query << "	building_power_req, ";
		query << "	building_fuel_use, ";
		query << "	building_prod_metal, ";
		query << "	building_prod_crystal, ";
		query << "	building_prod_plastic, ";
		query << "	building_prod_fuel, ";
		query << "	building_prod_food, ";
		query << "	building_prod_power, ";
		query << "	building_production_factor, ";
		query << "	building_store_metal, ";
		query << "	building_store_crystal, ";
		query << "	building_store_plastic, ";
		query << "	building_store_fuel, ";
		query << "	building_store_food, ";
		query << "	building_store_factor, ";
		query << "	building_people_place, ";
		query << "	building_last_level, ";
		query << "	building_fields, ";
		query << "	building_show, ";
		query << "	building_order, ";
		query << "	building_fieldsprovide, ";
		query << "	building_workplace ";
		query << "FROM ";
		query << "	buildings;";
		mysqlpp::Result bRes = query.store();	
		query.reset();
		if (bRes) {
			int bSize = bRes.size();
			BuildingData* object;

			if (bSize>0) {
				mysqlpp::Row bRow;
				
				for (mysqlpp::Row::size_type i = 0; i<bSize; i++) {
					bRow = bRes.at(i);
					this->idBuildingConverter[(int)(bRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(bRow["name"]) ] = this->counter;
					object = new BuildingData(bRow);
					this->buildingData.push_back(object);
					this->counter++;
				}
			}
		}
	}
