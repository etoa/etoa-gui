
#ifndef __ENTITY__
#define __ENTITY__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include <string>

/**
* Entity class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Entity	
{
	public: 
		Entity(char code, mysqlpp::Row &eRow=NULL) {
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
			
			this->actionName = "";
			int userId = 0;
		}
		
		void setAction(std::string actionName);
		std::string getCoords();
		int getUserId();
		
		
		
	protected:
		int id;
		int cellId;
		int userId;
		short pos;
		char code;
		int lastVisited;
		std::string codeName;
		std::string coordsString;
		std::string actionName;
		
		bool showCoords, coordsLoaded;
		bool dataLoaded;
	
	private:
		void loadCoords();
		virtual void loadEntityData() = 0;
};

#endif
