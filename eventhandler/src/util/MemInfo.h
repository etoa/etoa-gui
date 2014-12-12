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
    
	long long getTotalVirtualMem() const;
	long long getVirtualMemUsed() const;
	int getVirtualMemUsedByCurrentProcess(); //Note: this value is in KB!
	long long getTotalPyhsMem() const;
	long long getPhysMemUsed() const;
	int getPhysMemUsedByCurrentProcess(); //Note: this value is in KB!
  
  private:

	struct sysinfo memInfo;
	
	int parseLine(char* line);

};

#endif
