
#ifndef __SPECIALISTDATA__
#define __SPECIALISTDATA__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Data.h"

/**
* SpecialistData class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class SpecialistData : public Data {
public:
	SpecialistData(mysqlpp::Row object) : Data(object, false) 
	{
		this->specialistId = (short)object["specialist_id"];
		this->specialistName = std::string(object["specialist_name"]);
		this->specialistDesc = std::string(object["specialist_desc"]);
		this->specialistEnabled = (bool)object["specialist_enabled"];
		this->specialistPointsReq = (int)object["specialist_points_req"];
		this->specialistCostsMetal = (double)object["specialist_costs_metal"];
		this->specialistCostsCrystal = (double)object["specialist_costs_crystal"];
		this->specialistCostsPlastic = (double)object["specialist_costs_plastic"];
		this->specialistCostsFuel = (double)object["specialist_costs_fuel"];
		this->specialistCostsFood = (double)object["specialist_costs_food"];
		this->specialistDays = (short)object["specialist_days"];
		this->specialistProdMetal = (double)object["specialist_prod_metal"];
		this->specialistProdCrystal = (double)object["specialist_prod_crystal"];
		this->specialistProdPlastic = (double)object["specialist_prod_plastic"];
		this->specialistProdFuel = (double)object["specialist_prod_fuel"];
		this->specialistProdFood = (double)object["specialist_prod_food"];
		this->specialistPower = (double)object["specialist_power"];
		this->specialistPopulation = (double)object["specialist_population"];
		this->specialistTimeTech = (double)object["specialist_time_tech"];
		this->specialistTimeBuilding = (double)object["specialist_time_buildings"];
		this->specialistTimeDefense = (double)object["specialist_time_defense"];
		this->specialistTimeShips = (double)object["specialist_time_ships"];
		this->specialistCostsBuilding = (double)object["specialist_costs_buildings"];
		this->specialistCostsDefense = (double)object["specialist_costs_defense"];
		this->specialistCostsShips = (double)object["specialist_costs_ships"];
		this->specialistCostsTech = (double)object["specialist_costs_tech"];
		this->specialistFleetSpeed = (double)object["specialist_fleet_speed"];
		this->specialistFleetMax = (double)object["specialist_fleet_max"];
		this->specialistDefRepair = (double)object["specialist_def_repair"];
		this->specialistSpyLevel = (double)object["specialist_spy_level"];
		this->specialistTarnLevel = (double)object["specialist_tarn_level"];
		this->specialistTradeTime = (double)object["specialist_trade_time"];
		this->specialistTradeBonus = (double)object["specialist_trade_bonus"];
	}
	
	short getSpecialistId();
	std::string getSpecialistName();
	std::string getSpecialistDesc();
	bool getSpecialistEnabled();
	double getSpecialistPointsReq();
	double getSpecialistCostsMetal();
	double getSpecialistCostsCrystal();
	double getSpecialistCostsPlastic();
	double getSpecialistCostsFuel();
	double getSpecialistCostsFood();
	short getSpecialistDays();
	double getSpecialistProdMetal();
	double getSpecialistProdCrystal();
	double getSpecialistProdPlastic();
	double getSpecialistProdFuel();
	double getSpecialistProdFood();
	double getSpecialistPower();
	double getSpecialistPopulation();
	double getSpecialistTimeTech();
	double getSpecialistTimeBuilding();
	double getSpecialistTimeDefense();
	double getSpecialistTimeShips();
	double getSpecialistCostsBuilding();
	double getSpecialistCostsDefense();
	double getSpecialistCostsShips();
	double getSpecialistCostsTech();
	double getSpecialistFleetSpeed();
	double getSpecialistFleetMax();
	double getSpecialistDefRepair();
	double getSpecialistSpyLevel();
	double getSpecialistTarnLevel();
	double getSpecialistTradeTime();
	double getSpecialistTradeBonus();
	
private:
	short specialistId;
	std::string specialistName, specialistDesc;
	bool specialistEnabled;
	double specialistPointsReq;
	double specialistCostsMetal, specialistCostsCrystal, specialistCostsPlastic, specialistCostsFuel, specialistCostsFood;
	short specialistDays;
	double specialistProdMetal, specialistProdCrystal, specialistProdPlastic, specialistProdFuel, specialistProdFood, specialistPower, specialistPopulation;
	double specialistTimeTech, specialistTimeBuilding, specialistTimeDefense, specialistTimeShips;
	double specialistCostsBuilding, specialistCostsDefense, specialistCostsShips, specialistCostsTech;
	double specialistFleetSpeed, specialistFleetMax, specialistDefRepair, specialistSpyLevel, specialistTarnLevel;
	double specialistTradeTime, specialistTradeBonus;
};

#endif
