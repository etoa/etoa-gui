
#include "Entity.h"

	void Entity::setAction(std::string actionName) {
		this->actionName = actionName;
	}

	std::string Entity::getCoords() {
		if (!this->coordsLoaded)
			this->loadCoords();
		
		return this->coordsString;
	}
	
	int getUserId() {
		if (!this->dataLoaded)
			this->loadEntityData;
			
		return this->userId;
	}
	
	void Entity::loadCoords() {
		if (!this->codeName)
			loadEntityData();
			
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	cells ";
		query << "WHERE ";
		query << "	id='" << this->cellId << "' ";
		query << "LIMIT 1;";
		mysqlpp::Result cRes = query.store();
		query.reset();
			
		if (cRes) {
			int cSize = cRes.size();
			
			if (cSize>0) {
				mysqlpp::Row cRow = cRes.at(0);
				
				this->sx = std::string(cRow["sx"])
				this->sy = std::string(cRow["sy"])
				
				this->codeString = this->codeName
								+ "("
								+ this->sx
								+ "/"
								+ this->sy
								+ " : "
								+ std::string(cRow["cx"])
								+ "/"
								+ std::string/cRow["cy"])
								+ " : "
								+ this->pos
								+ ")";
			}
		}
	}