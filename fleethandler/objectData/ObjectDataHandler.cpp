#include <iostream>
#include <set>
#include <vector>
#include "../MysqlHandler.h"
#include <mysql++/mysql++.h>

#include "ObjectDataHandler.h"
#include "ObjectHandler.h"

	/** Liefert die Configwerte als string **/
	ObjectHandler::ObjectHandler objectData::get(int id,short type)
	{
		if (type>0) {
			return this->data[type][idShipData[id] ];
		} else {
			return this->data[type][idDefData[id] ];
		}
	}
	
	/** Clear the Data and reload it **/
	void objectData::reloadData()
	{
		this->data.clear();
		this->idDefData.clear();
		this->idShipData.clear();
		
		loadData();
	}
	
	
	/**	Initialisiert die Objectwerte **/
	void objectData::loadData ()
	{
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();

		this->counter = 0;
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	def_id AS id, ";
		query << "	def_name AS name, ";
		query << "	def_costs_metal AS costs_metal, ";
		query << "	def_costs_crystal AS costs_crystal, ";
		query << "	def_costs_plastic AS costs_plastic, ";
		query << "	def_costs_fuel AS costs_fuel, ";
		query << "	def_costs_food AS costs_food, ";
		query << "	def_costs_power AS costs_power, ";
		query << "	def_power_use AS power_use, ";
		query << "	def_fuel_use AS fuel_use, ";
		query << "	def_show AS showable, ";
		query << "	def_buildable AS buildable, ";
		query << "	def_order AS ordered, ";
		query << "	def_heal AS heal, ";
		query << "	def_structure AS structure, ";
		query << "	def_shield AS shield, ";
		query << "	def_weapon AS weapon, ";
		query << "	def_race_id AS race_id, ";
		query << "	def_cat_id AS cat_id, ";
		query << "	def_max_count AS max_count, ";
		query << "	def_points AS points, ";
		query << "	def_jam AS jam, ";
		query << "	def_fields AS field ";
		query << "FROM ";
		query << "	defense;";
		mysqlpp::Result dRes = query.store();	
		query.reset();
		if (dRes) {
			int dSize = dRes.size();

			if (dSize>0) {
				mysqlpp::Row dRow;
				std::vector< ObjectHandler > temp;

				for (mysqlpp::Row::size_type i = 0; i<dSize; i++) {
					dRow = dRes.at(i);
					this->counter = (int)i;
					this->idDefData[(int)(dRow["id"]) ] =  this->counter;
					ObjectHandler object(dRow,0);
					temp.push_back(object);
				}
				this->data.push_back(temp);
			}
		}

		this->counter = 0;
		query << "SELECT ";
		query << "	ship_id AS Id, ";
		query << "	ship_name AS Name, ";
		query << "	ship_type_id AS type_id, ";
		query << "	ship_costs_metal AS costs_metal, ";
		query << "	ship_costs_crystal AS costs_crystal, ";
		query << "	ship_costs_plastic AS costs_plastic, ";
		query << "	ship_costs_fuel AS costs_fuel, ";
		query << "	ship_costs_food AS costs_food, ";
		query << "	ship_costs_power AS costs_power, ";
		query << "	ship_power_use AS power_use, ";
		query << "	ship_fuel_use AS fuel_use, ";
		query << "	ship_fuel_use_launch AS fuel_use_launch, ";
		query << "	ship_fuel_use_landing AS fuel_use_landing, ";
		query << "	ship_fuel_use_economize AS fuel_use_economize, ";
		query << "	ship_prod_power AS prod_power, ";
		query << "	ship_capacity AS capacity, ";
		query << "	ship_people_capacity AS people_capacity, ";
		query << "	ship_pilots AS pilots, ";
		query << "	ship_speed AS speed, ";
		query << "	ship_time2start AS time2start, ";
		query << "	ship_time2land AS time2land, ";
		query << "	ship_show AS showable, ";
		query << "	ship_buildable AS buildable, ";
		query << "	ship_order AS ordered, ";
		query << "	ship_actions AS actions, ";
		query << "	ship_heal AS heal, ";
		query << "	ship_structure AS structure, ";
		query << "	ship_shield AS shield, ";
		query << "	ship_weapon AS weapon, ";
		query << "	ship_race_id AS race_id, ";
		query << "	ship_launchable AS launchable, ";
		query << "	ship_fieldsprovide As fieldsprovide, ";
		query << "	ship_cat_id AS cat_id, ";
		query << "	ship_fakeable AS fakeable, ";
		query << "	special_ship AS special, ";
		query << "	ship_max_count AS max_count, ";
		query << "	special_ship_max_level AS max_level, ";
		query << "	special_ship_need_exp AS need_exp, ";
		query << "	special_ship_exp_factor AS exp_factor, ";
		query << "	special_ship_bonus_weapon AS bonus_weapon, ";
		query << "	special_ship_bonus_structure AS bonus_structure, ";
		query << "	special_ship_bonus_shield AS bonus_shield, ";
		query << "	special_ship_bonus_heal AS bonus_heal, ";
		query << "	special_ship_bonus_capacity AS bonus_capacity, ";
		query << "	special_ship_bonus_speed AS bonus_speed, ";
		query << "	special_ship_bonus_pilots AS bonus_pilots, ";
		query << "	special_ship_bonus_tarn AS bonus_tarn, ";
		query << "	special_ship_bonus_antrax As bonus_antrax, ";
		query << "	special_ship_bonus_forsteal AS bonus_forsteal, ";
		query << "	special_ship_bonus_build_destroy AS bonus_build_destroy, ";
		query << "	special_ship_bonus_antrax_food AS bonus_antrax_food, ";
		query << "	special_ship_bonus_deactivade AS bonus_deactivade, ";
		query << "	ship_points AS points, ";
		query << "	ship_alliance_shipyard_level AS alliance_building_level, ";
		query << "	ship_alliance_costs AS alliance_costs ";
		query << "FROM ";
		query << "	ships;";
		mysqlpp::Result sRes = query.store();	
		query.reset();
		if (sRes) {
			int sSize = sRes.size();

			if (sSize>0) {
				mysqlpp::Row sRow;
				std::vector<ObjectHandler> temp;
				
				for (mysqlpp::Row::size_type i = 0; i<sSize; i++) {
					sRow = sRes.at(i);
					this->counter = (int)i;
					this->idShipData[(int)(sRow["id"]) ] =  this->counter;
					ObjectHandler object(sRow,1);
					temp.push_back(object);
				}
				this->data.push_back(temp);
			}
		}
	}		
