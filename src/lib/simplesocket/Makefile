#
# +---------------------------------------------------------+
# | Simple Socket C++ library                        v1.0   |
# | Copyright (C) 2001          Wim Bokkers                 |
# +---------------------------------------------------------+
#


CC = g++
INCLUDES= -I. -I-
LIBDIR= -L.
LIB= 
CFLAGS=
CPPFLAGS= -Wall

STRIP=strip


all: ssock_test SimpleSocket.o
	
dist:
	tar cf SimpleLib.tar *.h *.cpp Makefile.cygwin Makefile.sgi
	gzip SimpleLib.tar

ssock_test: ssock_client ssock_server


ssock_client: SimpleSocket.o Makefile ssock_client.cpp
	$(CC) $(INCLUDES) $(CPPFLAGS) $(CFLAGS) -o ssock_client \
            ssock_client.cpp SimpleSocket.o

ssock_server: SimpleSocket.o Makefile ssock_server.cpp
	$(CC) $(INCLUDES) $(CPPFLAGS) $(CFLAGS) -o ssock_server \
            ssock_server.cpp SimpleSocket.o

SimpleSocket.o: SimpleSocket.cpp SimpleSocket.h SimpleException.h \
                SimpleMessage.h Makefile
	$(CC) $(INCLUDES) $(CPPFLAGS) $(CFLAGS) -c SimpleSocket.cpp

distclean: clean

clean:
	rm *.o

