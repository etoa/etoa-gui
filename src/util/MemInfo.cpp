#include "MemInfo.h"

MemInfo::MemInfo()
{
	sysinfo (&memInfo);
}

MemInfo::~MemInfo()
{
}

long long MemInfo::getTotalVirtualMem() {
	long long totalVirtualMem = memInfo.totalram;
	totalVirtualMem += memInfo.totalswap;
	totalVirtualMem *= memInfo.mem_unit;
	return totalVirtualMem;
}

long long MemInfo::getVirtualMemUsed() {
	long long virtualMemUsed = memInfo.totalram - memInfo.freeram;
        virtualMemUsed += memInfo.totalswap - memInfo.freeswap;
        virtualMemUsed *= memInfo.mem_unit;
	return virtualMemUsed;
}

int MemInfo::getVirtualMemUsedByCurrentProcess() { 
        FILE* file = fopen("/proc/self/status", "r");
        int result = -1;
        char line[128];

        while (fgets(line, 128, file) != NULL){
            if (strncmp(line, "VmSize:", 7) == 0){
                result = parseLine(line);
                break;
            }
        }
        fclose(file);
        return result;
}

long long MemInfo::getTotalPyhsMem() {
	long long totalPhysMem = memInfo.totalram;
	totalPhysMem *= memInfo.mem_unit;
	return totalPhysMem;
}

long long MemInfo::getPhysMemUsed() {
	long long physMemUsed = memInfo.totalram - memInfo.freeram;
	physMemUsed *= memInfo.mem_unit;
	return physMemUsed;
}

int MemInfo::getPhysMemUsedByCurrentProcess() {
        FILE* file = fopen("/proc/self/status", "r");
        int result = -1;
        char line[128];

        while (fgets(line, 128, file) != NULL){
            if (strncmp(line, "VmRSS:", 6) == 0){
                result = parseLine(line);
                break;
            }
        }
        fclose(file);
        return result;
}

int MemInfo::parseLine(char* line){
	int i = strlen(line);
        while (*line < '0' || *line > '9') line++;
       	line[i-3] = '\0';
        i = atoi(line);
        return i;
}
