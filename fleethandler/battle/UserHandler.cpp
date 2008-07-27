#include <vector>
#include <math.h>
#include "../config/ConfigHandler.h"
#include "UserHandler.h"

	void UserHandler::updateValues()
	{
		cCount = 0;
		cWeapon = 0;
		cHealPoints = 0;
		cHealCount = 0;
		
		percentage = cStructureShield / initStructureShield;
		
		std::vector< ObjectHandler>::iterator it;
		for ( it = objects.begin() ; it < objects.end(); it++ )
		{
			it->newCnt = ceil(percentage * it->cnt);
			if (it->newCnt > it->cnt) it->newCnt = it->cnt;
			cCount += it->newCnt;
			cWeapon += it->newCnt * it->weapon;
			
			if (it->heal > 0)
			{
				cHealCount += it->newCnt;
				cHealPoints += ceil(it->heal * it->newCnt);
			}
			
		}
		
		for ( it = defObjects.begin() ; it < defObjects.end(); it++ )
		{
			it->newCnt = ceil(percentage * it->cnt);
			if (it->newCnt > it->cnt) it->newCnt = it->cnt;
			cCount += it->newCnt;
			cWeapon += it->newCnt * it->weapon;
			
			if (it->heal > 0)
			{
				cHealCount += it->newCnt;
				cHealPoints += ceil(it->heal * it->newCnt);
			}
			
		}
		
		cWeapon *= weaponTech;
		cHealPoints *= healTech;
		
	}
	
	void UserHandler::updateValuesEnd(std::vector<double> &wf)
	{
		Config &config = Config::instance();
		percentage = cStructureShield / initStructureShield;
		
		std::vector< ObjectHandler >::iterator it;
		
		for ( it = objects.begin() ; it < objects.end(); it++ )
		{
			
			it->newCnt = ceil(percentage * it->cnt);
			
			if (it->newCnt * (it->structure + it->shield) <= 0)
			{
				it->newCnt = 0;
			}
			else if (it->newCnt > it->cnt)
			{
				it->newCnt = it->cnt;
			}
			
			loseFleet[0] += round((it->cnt - it->newCnt) * it->metal);
			loseFleet[1] += round((it->cnt - it->newCnt) * it->crystal);
			loseFleet[2] += round((it->cnt - it->newCnt) * it->plastic);
			loseFleet[3] += round((it->cnt - it->newCnt) * it->fuel);
			loseFleet[4] += round((it->cnt - it->newCnt) * it->food);
			
			wf[0] += round((it->cnt - it->newCnt) * config.nget("ship_wf_percent",0) *it->metal);
			wf[1] += round((it->cnt - it->newCnt) * config.nget("ship_wf_percent",0) *it->crystal);
			wf[2] += round((it->cnt - it->newCnt) * config.nget("ship_wf_percent",0) *it->plastic);
		}
		
		for ( it = defObjects.begin(); it <  defObjects.end(); it++ )
		{
			it->newCnt = ceil(percentage * it->cnt);
			
			double temp = it->newCnt;
			
			if (it->newCnt * (it->structure + it->shield) <= 0)
			{
				it->newCnt = 0;
			}
			
			it->newCnt += round((it->cnt - it->newCnt) * config.nget("def_restore_percent",0));
			
			if (it->newCnt > it->cnt) it->newCnt = it->cnt;
			
			it->repairCnt = it->newCnt - temp;
			
			loseFleet[0] += round((it->cnt - it->newCnt) * it->metal);
			loseFleet[1] += round((it->cnt - it->newCnt) * it->crystal);
			loseFleet[2] += round((it->cnt - it->newCnt) * it->plastic);
			loseFleet[3] += round((it->cnt - it->newCnt) * it->fuel);
			loseFleet[4] += round((it->cnt - it->newCnt) * it->food);
			
			wf[0] += round((it->cnt - it->newCnt) * config.nget("def_wf_percent",0) *it->metal);
			wf[1] += round((it->cnt - it->newCnt) * config.nget("def_wf_percent",0) *it->crystal);
			wf[2] += round((it->cnt - it->newCnt) * config.nget("def_wf_percent",0) *it->plastic);
		}
	}
	