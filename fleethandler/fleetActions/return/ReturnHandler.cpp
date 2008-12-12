#include <mysql++/mysql++.h>

#include "ReturnHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"

namespace retour
{
	void ReturnHandler::update()
	{
	
		/**
		* Fleet-Action: Returned flight
		*/
		
		if (this->f->getEntityToUserId() == this->f->getUserId()) {
			// Land fleet and delete entries in the database
			fleetLand(1,1,1);
			fleetDelete();
			// Check if the user'd like to have a return message for spy and transport
			this->sendMsg = true;
		
			if (this->f->getAction()=="spy" || this->f->getAction()=="transport") {
				mysqlpp::Query query = con_->query();
				query << "SELECT ";
				query << "	fleet_rtn_msg ";
				query << "FROM ";
				query << "	user_properties ";
				query << "WHERE ";
				query << "	id=" << this->f->getUserId() << ";";
				mysqlpp::Result mRes = query.store();
				query.reset();
			
				if (mRes) {
					int mSize = mRes.size();
				
					if (mSize > 0) {
						mysqlpp::Row mRow = mRes.at(0);
					
						if (mRow["fleet_rtn_msg"]!="0") {
							this->sendMsg = false;
						}
					}
				}
			}
			
			// If the check is ok, send a message to the user
			if (this->sendMsg) {
				std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n\n[b]Ziel:[/b] ";
				msg += this->f->getEntityToString(0);
				msg += "\n[b]Start:[/b] ";
				msg += this->f->getEntityFromString(0);
				msg += "\n[b]Zeit:[/b] ";
				msg += this->f->getLandtimeString();
				msg += "\n[b]Auftrag:[/b] ";
				msg += this->f->getActionString();
				
				msg += msgAllShips;
				msg += msgRes;
			
				functions::sendMsg(this->f->getUserId(),5,"Flotte angekommen",msg);
			}
		}
		
		// If the planet user is not the same as the fleet user, send fleet to the main and send a message with the info
		else {
			fleetSendMain();
			
			std::string msg = "[b]FLOTTE LANDEN GESCHEITERT[/b]\n\nEine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!\n\n[b]Ziel:[/b] ";
			msg += this->f->getEntityToString(0);
			msg += "\n[b]Start:[/b] ";
			msg += this->f->getEntityFromString(0);
			msg += "\n[b]Zeit:[/b] ";
			msg += this->f->getLandtimeString();
			msg += "\n[b]Auftrag:[/b] ";
			msg += this->f->getActionString();
			
			functions::sendMsg(this->f->getUserId(),5,"Flotte umgelenkt",msg);
		}
	}
}
