
#ifndef __DEFHANDLER__
#define __DEFHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles defense updates
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace def
{
	class DefHandler	: EventHandler
	{
	public:
		DefHandler()  : EventHandler() { this->changes_ = false; }
		void update();
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedPlanets() { return this->changedPlanets_; }
	private:
		bool changes_;
		bool updatePlanet;
		std::vector<int> changedPlanets_;		
	};
}
#endif
