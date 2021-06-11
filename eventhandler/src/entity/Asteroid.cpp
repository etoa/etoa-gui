
#include <ctime>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Asteroid.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"

	void Asteroid::loadData() {

		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	asteroids ";
		query << "WHERE ";
		query << "	id=" << this->id << " ";
		query << "LIMIT 1;";
		RESULT_TYPE nRes = query.store();
		query.reset();

		if (nRes) {
			int nSize = nRes.size();

			if (nSize>0) {
				mysqlpp::Row nRow = nRes.at(0);

				this->resMetal = (double)nRow["res_metal"];
				this->resCrystal = (double)nRow["res_crystal"];
				this->resPlastic = (double)nRow["res_plastic"];
				this->resFuel = (double)nRow["res_fuel"];
				this->resFood = (double)nRow["res_food"];
				this->resPower = (double)nRow["res_power"];
			}
		}

		this->initResMetal = this->resMetal;
		this->initResCrystal = this->resCrystal;
		this->initResPlastic = this->resPlastic;
		this->initResFuel = this->resFuel;
		this->initResFood = this->resFood;
		this->initResPower = this->resPower;

		this->initWfMetal = this->resMetal;
		this->initWfCrystal = this->wfCrystal;
		this->initWfPlastic = this->wfPlastic;

		this->entityUser = new User(this->userId);

		this->dataLoaded = true;
	}

	void Asteroid::saveData() {

		Config &config = Config::instance();

		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();

		// Check if there are still enough resources in the field, if not delete it and create a new one
		if (this->getResSum() < config.nget("asteroid_ress",1)) {
			// Delete the old one and replace it with an empty field
			query << "UPDATE ";
			query << "	entities ";
			query << "SET ";
			query << "	code='e', ";
			query << " lastvisited=0 ";
			query << "WHERE ";
			query << "	id=" << this->getId() << " ";
			query << "LIMIT 1;";
			query.store();
			query.reset();

			query << "DELETE FROM";
			query << "	asteroids ";
			query << "WHERE ";
			query << " id=" << this->getId() << " ";
			query << "LIMIT 1;";
			query.store();
			query.reset();

			query << "INSERT INTO ";
			query << " space ";
			query << "(";
			query << "	id ";
			query << ") ";
			query << "VALUES ";
			query << "(";
			query << "" << this->getId() << ");";
			query.store();
			query.reset();

            double shipCount =0;

			query << "SELECT "
            << "    sum(shiplist_count) AS sl_count "
			<< "FROM "
            << "    shiplist "
            << "INNER JOIN "
            << "    ships "
            << "ON "
            << "    ship_id=shiplist_ship_id "
            << "    AND ship_actions LIKE '%collectmetal%' ";
            RESULT_TYPE res = query.store();
            query.reset();

            if (res) {
                unsigned int resSize = res.size();

                if (resSize>0) {
                    mysqlpp::Row row;
                    for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
                        row = res.at(i);

                        shipCount = (double)row["sl_count"];
                    }
                }
            }

            query << "SELECT "
                << "	sum(fs_ship_cnt) AS fs_count "
				<< "FROM "
                << "    fleet "
                << "INNER JOIN "
                << "    fleet_ships "
                << "ON "
                << "    id=fs_fleet_id "
                << "INNER JOIN "
                << "    ships "
                << "ON "
                << "    fs_ship_id=ship_id "
                << "    AND ship_actions LIKE '%collectmetal%' ";
            res = query.store();
            query.reset();

            if (res) {
                unsigned int resSize = res.size();

                if (resSize>0) {
                    mysqlpp::Row row;
                    for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
                        row = res.at(i);

                        shipCount += (double)row["fs_count"];
                    }
                }
            }

        	// Create a new one
            double x = config.nget("asteroid_ress",1) + (400000*pow(2.0,log10(shipCount)));
			double newMetal = rand() % (int)(0.45*x) + (int)(0.21*x);
			double newCrystal = rand() % (int)(0.45*x) + (int)(0.21*x);
			double newPlastic = x-(newCrystal+newMetal);


			// Check if there is an empty field left
			query << "SELECT ";
			query << "	id ";
			query << "FROM ";
			query << "	entities ";
			query << "WHERE ";
			query << "	code='e' ";
			query << "ORDER BY ";
			query << " RAND() ";
			query << "LIMIT 1;";
			RESULT_TYPE searchRes = query.store();
			query.reset();

			if (searchRes) {
				int searchSize = searchRes.size();

				// if there is, create it
				if (searchSize > 0) {
					mysqlpp::Row searchRow = searchRes.at(0);

					query << "UPDATE ";
					query << "	entities ";
					query << "SET ";
					query << "	code='a' ";
					query << "WHERE ";
					query << "	id=" << searchRow["id"] << " ";
					query << "LIMIT 1;";
					query.store();
					query.reset();

					query << "INSERT INTO ";
					query << "	asteroids ";
					query << "(";
					query << "	id, ";
					query << "	res_metal, ";
					query << "	res_crystal, ";
					query << "	res_plastic ";
					query << ") ";
					query << "VALUES ";
					query << "(";
					query << "" << searchRow["id"] << ", ";
					query << "" << newMetal << ", ";
					query << "" << newCrystal << ", ";
					query << "" << newPlastic << ");";
					query.store();
					query.reset();

					query << "DELETE FROM ";
					query << "	space ";
					query << "WHERE ";
					query << " id=" << searchRow["id"] << " ";
					query << "LIMIT 1;";
					query.store();
					query.reset();
				}
			}
		}
		else if (this->changedData) {
			// Update the asteroid field with the new resources
			query << "UPDATE ";
			query << "	asteroids ";
			query << "SET ";
			query << "	res_metal=res_metal+" << (this->getResMetal() - this->initResMetal) << ", ";
			query << "	res_crystal=res_crystal+" << (this->getResCrystal() - this->initResCrystal) << ", ";
			query << "	res_plastic=res_plastic+" << (this->getResPlastic() - this->initResPlastic) << ", ";
			query << "	res_fuel=res_fuel+" << (this->getResFuel() - this->initResFuel) << ", ";
			query << "	res_food=res_food+" << (this->getResFood() - this->initResFood) << ", ";
			query << "	res_power=res_power+" << (this->getResPower() - this->initResPower) << " ";
			query << "WHERE ";
			query << "	id=" << this->getId() << " ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}

		this->changedData = false;
	}
