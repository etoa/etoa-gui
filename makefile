OBJECTS = FleetHandler.o ResHandler.o ShipHandler.o MessageHandler.o BackHandler.o PlanetHandler.o #AttackHandler.o SpecialHandler.o
OPTS = -lmysqlpp

FleetHandler: ${OBJECTS}
	g++ ${OBJECTS} ${OPTS} -o FleetHandler
	
FleetHandler.o: FleetHandler.cpp

#	comp
ResHandler.o: functions/ResHandler.cpp functions/ResHandler.h
	g++ -c functions/ResHandler.cpp functions/ResHandler.cpp

ShipHandler.o: functions/ShipHandler.cpp functions/ShipHandler.h
	g++ -c functions/ShipHandler.cpp functions/ShipHandler.cpp

PlanetHandler.o: functions/PlanetHandler.cpp functions/PlanetHandler.h
	g++ -c functions/PlanetHandler.cpp functions/PlanetHandler.cpp

MessageHandler.o: functions/MessageHandler.cpp functions/MessageHandler.h
	g++ -c functions/MessageHandler.cpp functions/MessageHandler.cpp

#AttackHandler.o: attack/AttackHandler.cpp attack/AttackHandler.h
#	g++ -c attack/AttackHandler.cpp
#	
BackHandler.o: back/BackHandler.cpp back/BackHandler.h
	g++ -c back/BackHandler.cpp
	
#SpecialHandler.o: special/SpecialHandler.cpp special/SpecialHandler.h
#	g++ -c special/SpecialHandler.cpp	

	

clean:
	rm FleetHandler FleetHandler.o ResHandler.o ShipHandler.o BackHandler.o