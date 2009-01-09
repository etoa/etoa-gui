#include <semaphore.h>

#include "Mutex.h"

Mutex::Mutex() 
{
	sem_init(&sem, 0, 1);
} 

Mutex::~Mutex()
{
	sem_destroy(&sem);
}

void Mutex::guard()
{
	sem_wait(&sem);
}

void Mutex::release()
{
	sem_post(&sem);	
}
