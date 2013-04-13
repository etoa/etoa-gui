#ifndef __MEMINFO_H
#define __MEMINFO_H

#include <unistd.h>
#include <cstdlib>

#include "stdlib.h"
#include "stdio.h"
#include "string.h"

#include "sys/types.h"
#include "sys/sysinfo.h"

class MemInfo
{
  public:
    
	MemInfo();
	~MemInfo();
    
	long long getTotalVirtualMem();
	long long getVirtualMemUsed();
	int getVirtualMemUsedByCurrentProcess(); //Note: this value is in KB!
	long long getTotalPyhsMem();
	long long getPhysMemUsed();
	int getPhysMemUsedByCurrentProcess(); //Note: this value is in KB!
  
  private:

	struct sysinfo memInfo;
	
	int parseLine(char* line);

};

#endif
