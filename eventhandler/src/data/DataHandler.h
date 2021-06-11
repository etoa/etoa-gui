
#ifndef __DATAHANDLER__
#define __DATAHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>
#include <map>
#include <vector>

#include "Data.h"
#include "ShipData.h"
#include "DefData.h"
#include "TechData.h"
#include "BuildingData.h"
#include "RaceData.h"
#include "SolData.h"
#include "PlanetData.h"
#include "SpecialistData.h"
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
		* @param id
		* @author Glaubinix
		**/
		ShipData* getShipById(int id);
		DefData* getDefById(int id);
		TechData* getTechById(int id);
		BuildingData* getBuildingById(int id);
		RaceData* getRaceById(int id);
		SolData* getSolById(int id);
		PlanetData* getPlanetById(int id);
		SpecialistData* getSpecialistById(int id);
		ShipData* getShipByName(std::string name);
		DefData* getDefByName(std::string name);
		TechData* getTechByName(std::string name);
		BuildingData* getBuildingByName(std::string name);

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
		std::map<int, int> idDefConverter, idShipConverter, idTechConverter, idBuildingConverter, idRaceConverter, idSolConverter, idPlanetConverter, idSpecialistConverter;

		/**
		* Container mit den gespeicherten Daten
		**/
		std::vector<ShipData> shipData;
		std::vector<DefData> defData;
		std::vector<TechData> techData;
		std::vector<BuildingData> buildingData;
		std::vector<RaceData> raceData;
		std::vector<SolData> solData;
		std::vector<PlanetData> planetData;
		std::vector<SpecialistData> specialistData;

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
