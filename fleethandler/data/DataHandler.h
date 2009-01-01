
#ifndef __DATAHANDLER__
#define __DATAHANDLER__

#include <mysql++/mysql++.h>
#include <map>
#include <vector>

#include "Data.h"
#include "ShipData.h"
#include "DefData.h"
#include "TechData.h"
#include "BuildingData.h"
#include "../MysqlHandler.h"


/**
* Data Singleton, very usefull!!!!! So use it .D
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

	class DataHandler
	{
	public:
		static DataHandler& instance ()
		{
			static DataHandler _instance;
			return _instance;
		}
		~DataHandler () {};
		
		/**
		* Liefert ein Data Object zurück
		*
		* @param int id
		* @author Glaubinix
		**/
		ShipData::ShipData* getShipById(int id);
		DefData::DefData* getDefById(int id);
		TechData::TechData* getTechById(int id);
		BuildingData::BuildingData* getBuildingById(int id);
		ShipData::ShipData* getShipByName(std::string name);
		DefData::DefData* getDefByName(std::string name);
		TechData::TechData* getTechByName(std::string name);
		BuildingData::BuildingData* getBuildingByName(std::string name);
				
		/**
		* Initialisiert die Werte
		*
		* @author Glaubinix
		**/
		void reloadData();
		
	private:
	
		/**
		* Initialisiert die Werte
		*
		* @author Glaubinix
		**/
		void loadData();
		
		/**
		* name <-> object_id Relationcontainer
		**/
		std::map<std::string, int> nameConverter;
		
		/**
		* id <-> object_id Relationcontainer
		**/
		std::map<int, int> idDefConverter, idShipConverter, idTechConverter, idBuildingConverter;
		
		/**
		* Container mit den gespeicherten Daten
		**/
		std::vector<ShipData*> shipData;
		std::vector<DefData*> defData;
		std::vector<TechData*> techData;
		std::vector<BuildingData*> buildingData;
		
		/**
		* counter
		**/
		int counter;
		
		static DataHandler* _instance;
		
		/**
		* Konsturktor der Configklasse
		*
		* @author Glaubinix
		**/
		DataHandler () {
			loadData();
		 };
		DataHandler ( const DataHandler& );
		DataHandler & operator = (const DataHandler &);
	};


#endif
