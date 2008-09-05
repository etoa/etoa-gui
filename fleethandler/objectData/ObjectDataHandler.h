
#ifndef __OBJECTDATAHANDLER__
#define __OBJECTDATAHANDLER__

#include <mysql++/mysql++.h>
#include <map>
#include <vector>
#include "ObjectHandler.h"
#include "../MysqlHandler.h"

/**
* ShipData Singleton, very usefull!!!!! So use it .D
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

	class objectData
	{
	public:
		static objectData& instance ()
		{
			static objectData _instance;
			return _instance;
		}
		~objectData () {};
		
		/**
		* Liefert die Configwerte als string
		*
		* @param string name, config_name in der DB
		* @param int value, 0=value, 1=param1, 2=param2
		* @author Glaubinix
		**/
		ObjectHandler::ObjectHandler get(int id, short type);
				
		/**
		* Initialisiert die Configwerte
		*
		* @author Glaubinix
		**/
		void reloadData();
		
	private:
	
		/**
		* Initialisiert die Configwerte
		*
		* @author Glaubinix
		**/
		void loadData();
		
		/**
		* Id <-> object_id Realtionscontainer
		**/
		std::map<int, int> idDefData, idShipData;
		
		/**
		* Mapcontainer mit den gespeicherten Configwerten
		**/
		std::vector<std::vector<ObjectHandler> > data;
		
		/**
		*
		**/
		int counter;
		
		static objectData* _instance;
		
		/**
		* Konsturktor der Configklasse
		*
		* @author Glaubinix
		**/
		objectData () {
			loadData();
		 };
		objectData ( const objectData& );
		objectData & operator = (const objectData &);
	};


#endif
