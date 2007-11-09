
CC = gcc
CFLAGS = 
OBJS = etoa_stats.o

etoa_stats:	${OBJS}
						${CC} ${CFLAGS} -o etoa_stats ${OBJS} -lmysqlclient
etoa_stats.o:	etoa_stats.c
						${CC} ${CFLAGS} -o ${OBJS} -c etoa_stats.c 
clean:
						rm -f ${OBJS}					
	

