//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// (C) by EtoA Gaming | www.etoa.ch   			 		//
//////////////////////////////////////////////////
//
// Pidfile manager
//

#include "PidFile.h"

PIDFile::PIDFile(const std::string &filename)
  : pidfile_path(filename), pidfile_fd(-1)
{

}

bool PIDFile::fileExists()
{
  FILE* fp = NULL;
  fp = fopen( pidfile_path.c_str(), "rb" );
  if( fp != NULL )
  {
      fclose( fp );
      return true;
  }
  return false;
}

int PIDFile::readPid()
{
 	char mystring[10];
 	FILE * pFile = fopen (pidfile_path.c_str(), "r");
	if (pFile == NULL) 
	{
		std::cerr << "Strange, the PIDfile exists but I am not allowed to read it!"<<std::endl;
 		exit(EXIT_FAILURE);
 	}
 	else 
 	{
 		char* tmpstr;
   	tmpstr = fgets (mystring , 100 , pFile);
   	fclose (pFile);
 	}		
 	return atoi(mystring);	
}

void PIDFile::write()
{
  // open pidfile for writing
  pidfile_fd = open(pidfile_path.c_str(),O_WRONLY|O_CREAT|O_NOFOLLOW, 0644);
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
      msg << "Cannot lock pidfile '" << pidfile_path << "': " << strerror(err);
      throw std::runtime_error(msg.str());
  }

  // truncate pidfile at 0 length
  int tmpresult;
  tmpresult = ftruncate(pidfile_fd, 0);

  // write our pid
  try
  {
    std::ofstream pidf(pidfile_path.c_str());
    pidf << getpid();
  }
  catch(std::exception x) 
  {
    std::ostringstream msg;
    msg << "Cannot write pidfile '" << pidfile_path << "': " << x.what();
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
