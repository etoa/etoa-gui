#ifndef MUTEX_H
#define MUTEX_H

#include <semaphore.h>

class Mutex	
{
	public:
		Mutex();
		~Mutex();
		void guard();
		void release();
	private:
		sem_t sem;
};

#endif
