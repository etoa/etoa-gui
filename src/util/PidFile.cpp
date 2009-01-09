#include <fcntl.h>
#include <fstream>
#include <iostream>
#include <sstream>
#include <stdexcept>
#include <sys/stat.h>
#include <sys/types.h>
#include <unistd.h>
#include <cerrno>

#include "PidFile.h"

PIDFile::PIDFile(const std::string &filename)
  : pidfile_path(filename), pidfile_fd(-1)
{
  // nothing else to do
}


PIDFile::PIDFile(const char * const filename)
  : pidfile_path(filename), pidfile_fd(-1)
{
  // nothing else to do
}


void PIDFile::write()
{
  // open pidfile for writing
  pidfile_fd = open(pidfile_path.c_str(), 
                    O_WRONLY|O_CREAT|O_NOFOLLOW, 0644);
  if (0 > pidfile_fd)
    {
      int err = errno;
      std::ostringstream msg;
      msg << "Cannot open pidfile '" << pidfile_path.c_str() << "': "
          << strerror(err);
      throw std::runtime_error(msg.str());
    }

  // lock pidfile for writing
  int rc = lockf(pidfile_fd, F_TLOCK, 0);
  if (-1 == rc)
    {
      int err = errno;
      std::ostringstream msg;
      msg << "Cannot lock pidfile '" << pidfile_path << "': "
          << strerror(err);
      throw std::runtime_error(msg.str());
    }

  // truncate pidfile at 0 length
  ftruncate(pidfile_fd, 0);

  // write our pid
  try
    {
      std::ofstream pidf(pidfile_path.c_str());
      pidf << getpid();
    }
  catch(std::exception x) {
    std::ostringstream msg;
    msg << "Cannot write pidfile '" << pidfile_path << "': "
        << x.what();
    throw std::runtime_error(msg.str());
  }
}

PIDFile::~PIDFile()
{
  if(-1 != pidfile_fd)
    {
      // pidfile has been opened and locked
      lockf(pidfile_fd, F_ULOCK, 0);
      close(pidfile_fd);
      unlink(pidfile_path.c_str());
    }
}
