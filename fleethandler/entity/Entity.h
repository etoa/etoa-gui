
#ifndef __ENTITY__
#define __ENTITY__

#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "../functions/Functions.h"

/**
* Entity class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Entity	
{
	public: 
		Entity(char code, mysqlpp::Row &eRow) {
			if (eRow) {
				this->id = (int)eRow["id"];
				this->cellId = (int)eRow["cell_id"];
				this->code = code;
				this->pos = (short)eRow["pos"];
				this->lastVisited = (int)eRow["lastvisited"];
			} else {
				this->id = 0;
				this->cellId = 0;
				this->code = code;
				this->pos = 0;
				this->lastVisited = 0;
			}
			
			this->coordsLoaded = false;
			this->dataLoaded = false;
			this->changedData = false;
			
			this->actionName = "";
			this->userId = 0;
		}
		
		void setAction(std::string actionName);
		std::string getCoords();
		int getUserId();
		int getId();
		
		double getResMetal();
		double getResCrystal();
		double getResPlastic();
		double getResFuel();
		double getResFood();
		double getResPower();
		double getResSum();
		
		double removeResMetal(double metal);
		double removeResCrystal(double crystal);
		double removeResPlastic(double plastic);
		double removeResFuel(double fuel);
		double removeResFood(double food);
		double removeResPower(double power);		
		
		virtual void saveData() = 0;
		
	protected:
		int id;
		int cellId;
		int userId;
		short sx;
		short sy;
		short pos;
		char code;
		double resMetal, resCrystal, resPlastic, resFuel, resFood, resPower;
		int lastVisited;
		std::string codeName;
		std::string coordsString;
		std::string actionName;
				
		bool showCoords, coordsLoaded;
		bool dataLoaded;
		bool changedData;

		void loadCoords();
		virtual void loadData() = 0;
};

#endif
