
#ifndef __CONFIGHANDLER__
#define __CONFIGHANDLER__

#include <mysql++/mysql++.h>
#include <map>

#include "../EventHandler.h"

/**
* Loads config Data from DB
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

	class Config
	{
	public:
		static Config* instance ()
		{
			static CGuard g;   // Speicherbereinigung
			if (!_instance)
				_instance = new Config ();
			return _instance;
		}
		void loadConfig () {
			std::cout << "home";
			};
	private:
		static std::map<std::string, std::map<std::string, std::string> > &mpConfig;
		static Config* _instance;
		Config () { }; /* verhindert, das ein Objekt von außerhalb von Config erzeugt wird. */
				// protected, wenn man von der Klasse noch erben möchte
		Config ( const Config& ); /* verhindert, dass eine weitere Instanz via
 Kopie-Konstruktor erstellt werden kann */
		~Config () { };
		class CGuard
		{
		public:
			~CGuard()
			{
				if( NULL != Config::_instance )
				{
					delete Config::_instance;
					Config::_instance = NULL;
				}
			}
		};
		friend class CGuard;
	protected:
		/**
		* The connection object
		*/
		mysqlpp::Connection* con_;
	};


#endif
