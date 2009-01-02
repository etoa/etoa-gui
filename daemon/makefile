OBJECTS = main.o logger.o pidfile.o anyoption.o

OPTS = 

etoad: ${OBJECTS}
	g++ ${OBJECTS} ${OPTS} -o etoad
	
main.o: main.cpp
	g++ -c main.cpp

logger.o: logger.cpp logger.h
	g++ -c logger.cpp

pidfile.o: pidfile.cpp pidfile.h
	g++ -c pidfile.cpp
	
anyoption.o: anyoption.cpp anyoption.h
	g++ -c anyoption.cpp	

clean:
	rm *.o
