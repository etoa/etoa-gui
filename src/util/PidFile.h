#ifndef __PIDFILE_H
#define __PIDFILE_H

#include <string>

/** Open and lock a pidfile on construction, 
    unlock and remove it on destruction. */
class PIDFile 
{
  public:
    PIDFile(const std::string &filename);
   	PIDFile(const char * const filename);
    ~PIDFile();

    /** Open and lock pidfile, write current process PID to it. */
    void write();

  private:
    /** Pathname to the pidfile. */
    std::string pidfile_path;

    /** File descriptor for locking the pidfile. */
    int pidfile_fd;
};

#endif
