
#ifndef __ENTITY__
#define __ENTITY__

#include <string>
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "../functions/Functions.h"
#include "../objects/User.h"

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
			
			this->resMetal = 0;
			this->resCrystal = 0;
			this->resPlastic = 0;
			this->resFuel = 0;
			this->resFood = 0;
			this->resPower = 0;
			this->resPeople = 0;
			
			this->wfMetal = 0;
			this->wfCrystal = 0;
			this->wfPlastic = 0;
			
			this->userMain = false;
			this->typeId = 0;
			
			this->coordsLoaded = false;
			this->dataLoaded = false;
			this->changedData = false;
			this->shipsLoaded = false;
			
			this->actionName = "";
			this->userId = 0;
		}
		
		
		virtual ~Entity() {}
		virtual void saveData() = 0;
				
		int getId();
		char getCode();
		int getUserId();
		
		User* getUser();
		
		short getTypeId();
		bool getIsUserMain();
		
		std::string getCoords();
		
		void setAction(std::string actionName);
		
		double getResMetal();
		double getResCrystal();
		double getResPlastic();
		double getResFuel();
		double getResFood();
		double getResPower();
		double getResPeople();
		double getResSum();
		
		void addResMetal(double metal);
		void addResCrystal(double crystal);
		void addResPlastic(double plastic);
		void addResFuel(double fuel);
		void addResFood(double food);
		void addResPower(double power);
		void addResPeople(double people);
		
		double removeResMetal(double metal);
		double removeResCrystal(double crystal);
		double removeResPlastic(double plastic);
		double removeResFuel(double fuel);
		double removeResFood(double food);
		double removeResPower(double power);
		double removeResPeople(double people);
		
		double getWfMetal();
		double getWfCrystal();
		double getWfPlastic();
		double getWfSum();
		
		void addWfMetal(double metal);
		void addWfCrystal(double crystal);
		void addWfPlastic(double plastic);
		
		double removeWfMetal(double metal);
		double removeWfCrystal(double crystal);
		double removeWfPlastic(double plastic);
		
		std::string getResString();
		
		std::string getLogResStart();
		std::string getLogResEnd();
		std::string getLogShipsStart();
		std::string getLogShipsEnd();
		
	protected:
		int id;
		int userId;
		int cellId;
		short sx;
		short sy;
		short pos;
		char code;
		short typeId;
		
		User *entityUser;
		
		double resMetal, resCrystal, resPlastic, resFuel, resFood, resPower, resPeople;
		double initResMetal, initResCrystal, initResPlastic, initResFuel, initResFood, initResPower, initResPeople;
		double wfMetal, wfCrystal, wfPlastic;
		double initWfMetal, initWfCrystal, initWfPlastic;
		
		int lastVisited;
		std::string codeName;
		std::string coordsString;
		std::string actionName;
		
		bool userMain;
		bool showCoords, coordsLoaded;
		bool dataLoaded;
		bool changedData;
		bool shipsLoaded;

		void loadCoords();
		virtual void loadData() = 0;
};

#endif
