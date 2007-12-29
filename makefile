OBJECTS = fleethandler.o ResHandler.o ShipHandler.o backhandler.o #attackhandler.o specialhandler.o
OPTS = -lmysqlpp

fleethandler: ${OBJECTS}
	g++ ${OBJECTS} ${OPTS} -o fleethandler
	
fleethandler.o: fleethandler.cpp

#	comp
ResHandler.o: functions/ResHandler.cpp functions/ResHandler.h
	g++ -c functions/ResHandler.cpp functions/ResHandler.cpp

ShipHandler.o: functions/ShipHandler.cpp functions/ShipHandler.h
	g++ -c functions/ShipHandler.cpp functions/ShipHandler.cpp

#attackhandler.o: attack/attackhandler.cpp attack/attackhandler.h
#	g++ -c attack/attackhandler.cpp
#	
backhandler.o: back/backhandler.cpp back/backhandler.h
	g++ -c back/backhandler.cpp
	
#specialhandler.o: special/specialhandler.cpp special/specialhandler.h
#	g++ -c special/specialhandler.cpp	

	

clean:
	rm fleethandler fleethandler.o ResHandler.o ShipHandler.o backhandler.o