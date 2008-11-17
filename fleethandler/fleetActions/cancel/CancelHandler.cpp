#include <mysql++/mysql++.h>

#include "CancelHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"

namespace cancel
{
	void CancelHandler::update()
	{
		/**
		* Fleet-Action: Cancelled flight
		*/
		
		if (this->f->getEntityToUserId() == this->f->getUserId()) {
			// Land the fleet and delete it in the database
			fleetLand(1);

			fleetDelete();
	
			// Send a message to the user
			std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
			msg += this->f->getEntityToString(0);
			msg += "\n[b]Startplanet:[/b] ";
			msg += this->f->getEntityFromString(0);
			msg += "\n[b]Zeit:[/b] ";
			msg += this->f->getLandtimeString();
			msg += "\n[b]Auftrag:[/b] ";
			msg += this->f->getActionString();
			msg += msgAllShips;
			msg += msgRes;
			msg += "";
			functions::sendMsg(this->f->getUserId(),5,"Flotte angekommen",msg);
		}
		
		// If the fleet user isnt the same as the planet user
		else {
			// Send the fleet to user's mainplanet
			fleetSendMain();
			
			// Send a message to the user
			std::string msg = "[b]FLOTTE Landen GESCHEITERT[/b]\n\nEine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!\n\n[b]Ziel:[/b] ";
			msg += this->f->getEntityToString(0);
			msg += "\n[b]Startplanet:[/b] ";
			msg += this->f->getEntityFromString(0);
			msg += "\n[b]Zeit:[/b] ";
			msg += this->f->getLandtimeString();
			msg += "\n[b]Auftrag:[/b] ";
			msg += this->f->getActionString();
			
			functions::sendMsg(this->f->getUserId(),5,"Flotte umgelenkt",msg);
		}
	}	
}
