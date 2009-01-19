
#ifndef __DATA__
#define __DATA__

#include <mysql++/mysql++.h>
#include <string>

/**
* ObjectType class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Data	
{
public:
	/**
	* Object Class
	* 
	*/
	Data(mysqlpp::Row object, bool init=true) {
		if (init) {
			this->id = (int)object["id"];
			this->name = std::string(object["name"]);
			this->shortComment = std::string(object["shortcomment"]);
			this->longComment = std::string(object["longcomment"]);
			this->costsMetal = (int)object["costs_metal"];
			this->costsCrystal = (int)object["costs_crystal"];
			this->costsPlastic = (int)object["costs_plastic"];
			this->costsFuel  = (int)object["costs_fuel"];
			this->costsFood  = (int)object["costs_food"];
			this->costsPower  = (int)object["costs_power"];
		}
	};
		
	int getId();
	std::string getName();
	std::string getShortComment();
	std::string getLongComment();
	int getCosts();
	double getCostsMetal();
	double getCostsCrystal();
	double getCostsPlastic();
	double getCostsFuel();
	double getCostsFood();
	int getCostsPower();

	
private:
	
	int id;
	std::string name;
	std::string shortComment;
	std::string longComment;
	short objectTypeId;
	int costsMetal;
	int costsCrystal;
	int costsPlastic;
	int costsFuel;
	int costsFood;
	int costsPower;
};

#endif
