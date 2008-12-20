
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
		Data::Data* getShipById(int id);
		Data::Data* getDefById(int id);
		Data::Data* getTechById(int id);
		Data::Data* getBuildingById(int id);
		Data::Data* getShipByName(std::string name);
		Data::Data* getDefByName(std::string name);
		Data::Data* getTechByName(std::string name);
		Data::Data* getBuildingByName(std::string name);
				
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
		std::vector<Data*> data;
		
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
