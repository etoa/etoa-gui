
#ifndef __CONFIGHANDLER__
#define __CONFIGHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>
#include <map>
#include <vector>

#include "../util/Functions.h"

/**
* Config Singleton, very usefull!!!!! So use it .D
* 
* @author Stephan Vock <glaubinix@etoa.ch>
*/

	class My;
	
	class Config
	{
	public:
		static Config& instance ()
		{
			static Config _instance;
			return _instance;
		}
		~Config () {};
		
		/**
		* Liefert die Configwerte als string
		*
		* @param name config_name in der DB
		* @param value 0=value, 1=param1, 2=param2
		**/
		std::string get(std::string name, int value);
		
		/**
		* Liefert die Configwerte als double
		*
		* @param name config_name in der DB
		* @param value 0=value, 1=param1, 2=param2
		**/		
		double nget(std::string name, int value);
		
		/**
		* Liefert die Zahlenwerte gespeicherter Werte
		*
		* @param name Erkennungsname ingame, wie auch backend
		**/
		double idget(std::string name);
		
		/**
		* Liefert die Zahlenwerte gespeicherter Flottenaktionen
		*
		* @param name Flottenaktionsnamen
		**/
		short getAction(std::string action);
		std::string getActionName(std::string action);
		
		/**
		* Setzt den Rundenname
		*
		* @param name Rundenname
		**/
		void setRoundName(std::string name);
			
		/**
		* Liefert den Frontendpfad zurück
		**/
		std::string getFrontendPath();
		
		/**
		 * Config neu laden
		 **/
		void reloadConfig();
		
	private:
	
		/**
		* Initialisiert die Configwerte
		*
		**/
		void loadConfig();
		
		/**
		 * Initalisiert die Gassaugerconfigwerte
		 *
		 **/
		void calcCollectFuelValues();
		
		/**
		* Id <-> config_name Realtionscontainer
		**/
		std::map<std::string, int> sConfig;
		
		/**
		* Mapcontainer mit vorgespeicheten Werten. Müssen manuel eingefügt werden
		**/
		std::map<std::string, double> idConfig;
		
		/**
		* Mapcontainer mit den gespeicherten Configwerten
		**/
		std::vector<std::vector<std::string> > cConfig;
		
		/**
		* Mapcontainer mit fleetactions
		**/
		std::map<std::string, short> actions;
		std::map<std::string, std::string> actionName;
		
		std::string gameRound;
		
		static Config* _instance;
		
		/**
		* Konsturktor der Configklasse
		*
		**/
		Config () {	};
		Config ( const Config& );
		Config & operator = (const Config &);
	};


#endif
