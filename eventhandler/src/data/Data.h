
#ifndef __DATA__
#define __DATA__

#define MYSQLPP_MYSQL_HEADERS_BURIED
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
			this->costsMetal = (double)object["costs_metal"];
			this->costsCrystal = (double)object["costs_crystal"];
			this->costsPlastic = (double)object["costs_plastic"];
			this->costsFuel  = (double)object["costs_fuel"];
			this->costsFood  = (double)object["costs_food"];
			this->costsPower  = (double)object["costs_power"];
		}
	};

	int getId();
	std::string getName();
	std::string getShortComment();
	std::string getLongComment();
	double getCosts();
	double getCostsMetal();
	double getCostsCrystal();
	double getCostsPlastic();
	double getCostsFuel();
	double getCostsFood();
	double getCostsPower();


private:

	int id;
	std::string name;
	std::string shortComment;
	std::string longComment;
	short objectTypeId;
	double costsMetal, costsCrystal, costsPlastic, costsFuel, costsFood, costsPower;
};

#endif
