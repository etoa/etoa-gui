
#ifndef __CONFIGHANDLER__
#define __CONFIGHANDLER__

#include <mysql++/mysql++.h>
#include <map>
#include <vector>
#include "../MysqlHandler.h"

/**
* Config Singleton, very usefull!!!!! So use it .D
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

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
		* @param string name, config_name in der DB
		* @param int value, 0=value, 1=param1, 2=param2
		* @author Glaubinix
		**/
		std::string get(std::string name, int value);
		
		/**
		* Liefert die Configwerte als double
		*
		* @param string name, config_name in der DB
		* @param int value, 0=value, 1=param1, 2=param2
		* @author Glaubinix
		**/		
		double nget(std::string name, int value);
		
		/**
		* Liefert die Zahlenwerte gespeicherter Werte
		*
		* @param string name, Erkennungsname ingame, wie auch backend
		* @author Glaubinix
		**/
		double idget(std::string name);
		
		/**
		* Liefert die Zahlenwerte gespeicherter Flottenaktionen
		*
		* @param string name, Flottenaktionsnamen
		* @author Glaubinix
		**/
		short getAction(std::string action);
		
	private:
	
		/**
		* Initialisiert die Configwerte
		*
		* @author Glaubinix
		**/
		void loadConfig();
		
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
		
		static Config* _instance;
		
		/**
		* Konsturktor der Configklasse
		*
		* @author Glaubinix
		**/
		Config () {
			loadConfig();
		 };
		Config ( const Config& );
		Config & operator = (const Config &);
	};


#endif
