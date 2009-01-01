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
// Programmiert von Nicolas Perrenoud				 		//
// www.nicu.ch | mail@nicu.ch								 		//
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
//////////////////////////////////////////////////

/**
* Daemon framework
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/

#include <iostream>
#include <signal.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/errno.h>
#include <sstream>

#include "logger.h"
#include "pidfile.h"
#include "anyoption.h"

using namespace std;

bool isDaemon = true;
std::string versionString = "0.1 alpha";

std::string gameRound;
std::string pidFile;
std::string logFile;
int ownerUID;

// Send message to stdout or log
void lout(std::string msg)
{
	if (isDaemon)
		Logger::getInstance()->add(msg);
	else
		std::cout << msg<<endl;
}

// Signal handler
void sighandler(int sig)
{
	// Clean up pidfile
	unlink(pidFile.c_str()); // This is somehow a hack, better find a way to make more use of pidfile class
	
	char str[50];
	if (sig == SIGTERM)
	{
		sprintf(str,"Caught signal SIGTERM, exiting...",sig);
	  lout(str);
		lout("EtoA backend "+gameRound+" stopped");
		exit(EXIT_SUCCESS);
	}

	sprintf(str,"Caught signal %d, exiting...",sig);
  lout(str);
	lout("EtoA backend "+gameRound+" unexpectedly stopped");
	exit(EXIT_FAILURE);
}

bool fileExists( std::string fileName )
{
    FILE* fp = NULL;
    fp = fopen( fileName.c_str(), "rb" );
    if( fp != NULL )
    {
        fclose( fp );
        return true;
    }
    return false;
}

// Create a daemon
int daemonize()
{
  pid_t pid, sid;
  /* Fork off the parent process */
  pid = fork();
  if (pid < 0) 
  {
  	lout("Could not fork parent process");
 		exit(EXIT_FAILURE);
  }
  /* If we got a good PID, then we can exit the parent process. */
  if (pid > 0) 
  {
  	//cout << "Daemon has PID "<<pid<<endl;
    exit(EXIT_SUCCESS);
  }

  /* Change the file mode mask */
  umask(0);
          
  /* Create a new SID for the child process */
  sid = setsid();
  if (sid < 0) 
  {
  	lout("Unable to get SID for child process");
    exit(EXIT_FAILURE);
  }
  
  /* Close out the standard file descriptors */
  close(STDIN_FILENO);
  close(STDOUT_FILENO);
  close(STDERR_FILENO);
  
  // Create pidfile
  PIDFile* pf = new PIDFile(pidFile);
  pf->write();	
  
  int myPid = (int)getpid();
	stringstream s;
	s << "Daemon initialized with PID ";
	s << myPid;
	s << " and owned by ";
	s << getuid();
	lout(s.str());	
  return myPid;
}

int main(int argc, char* argv[])
{
	// Register signal handlers
  signal(SIGABRT, &sighandler);
	signal(SIGTERM, &sighandler);
	signal(SIGINT, &sighandler);
	signal(SIGHUP, &sighandler);

	// Parse command line
	AnyOption *opt = new AnyOption();
  opt->addUsage( "Usage: " );
  opt->addUsage( "" );
  opt->addUsage( " -r  --round roundname   Select round to be used (necessary)");
  opt->addUsage( " -u  --uid userid        Select user id under which it runs (necessary if you are root)");
  opt->addUsage( " -p  --pidfile path      Select path to pidfile");
  opt->addUsage( " -l  --logfile path  	   Select path to logfile");
  opt->addUsage( " -k  --killexisting  	   Kills an already running instance of this backend");
  opt->addUsage( " -h  --help              Prints this help");
  opt->addUsage( " -v  --version           Prints version information");
  opt->setFlag("help",'h');
  opt->setFlag("version",'v');
  opt->setFlag("killexisting",'k');
  opt->setOption("userid",'u');
  opt->setOption("round",'r');
  opt->setOption("pidfile",'p');  
  opt->setOption("logfile",'l');
  opt->processCommandArgs( argc, argv );
	if( ! opt->hasOptions()) 
	{ 
    opt->printUsage();
	 	return EXIT_FAILURE;
	}  
  if( opt->getFlag( "help" ) || opt->getFlag( 'h' )) 
  {	
  	opt->printUsage();
 		return EXIT_SUCCESS;
	}
  if( opt->getFlag( "version" ) || opt->getFlag( 'v' )) 
  {	
  	cout << "EtoA Backend Daemon, Version "<<versionString<<endl<<"(c) by EtoA Gaming, www.etoa.c"<<endl<<endl;
 		return EXIT_SUCCESS;
	}
	bool killExistingInstance = false;
  if( opt->getFlag( "killexisting" ) || opt->getFlag( 'k' )) 
  {	
		killExistingInstance = true;
	}
	
	if( opt->getValue( 'r' ) != NULL)
		gameRound = opt->getValue( 'r' );
	else if (opt->getValue("round") != NULL )
		gameRound = opt->getValue("round");
	else
	{
		cout << "Error: No gameround name given!"<<endl;	
	 	return EXIT_FAILURE;
	}
	
	if( opt->getValue('p') != NULL)
		pidFile = opt->getValue('p');
	else if (opt->getValue("pidfile") != NULL )
		pidFile = opt->getValue("pidfile");
	else
		pidFile = "/var/run/etoa/"+gameRound+".pid";
		
	if( opt->getValue('l') != NULL)
		logFile = opt->getValue('l');
	else if (opt->getValue("logfile") != NULL )
		logFile = opt->getValue("logfile");
	else
		logFile = "/var/log/etoa/"+gameRound+".log";

	if( opt->getValue('u') != NULL)
		ownerUID = atoi(opt->getValue('u'));
	else if (opt->getValue("uid") != NULL )
		ownerUID = atoi(opt->getValue("uid"));
	else
		ownerUID = (int)getuid();

  // Set correct uid
  if (setuid(ownerUID)!=0)
  {
  	cout << "Unable to change user id" << endl;
    exit(EXIT_FAILURE);  	
  }
  // Check uid
  if (getuid()==0)
  {
  	cout << "This software cannot be run as root!" <<endl;
    exit(EXIT_FAILURE);  	
  }  

	// Check for existing instance
	if (fileExists(pidFile))
	{
   	char mystring[10];
   	FILE * pFile = fopen (pidFile.c_str(), "r");
		if (pFile == NULL) 
		{
			std::cout << "Strange, the PIDfile exists but I am not allowed to read it!"<<std::endl;
   		exit(EXIT_FAILURE);
   	}
   	else 
   	{
     	fgets (mystring , 100 , pFile);
     	fclose (pFile);
   	}		
   	int existingPid = atoi(mystring);
		if (killExistingInstance)
		{
			std::cout << "EtoA Daemon " << gameRound << " seems to run already with PID "<<existingPid<<"! Killing this instance..." << std::endl;
			int kres = kill(existingPid,SIGTERM);
			if (kres<0)
			{
				if (errno==EPERM)
				{
					std::cout << "I am not allowed to kill the instance. Exiting..." << std::endl;
					exit(EXIT_FAILURE);
				}
				else
				{
					std::cout << "The process doesn't exist, perhaps the PID file was outdated. Continuing..." << std::endl;
				}
			}
			sleep(1);
		}
		else
		{
			std::cout << "EtoA Daemon " << gameRound << " is already running with PID "<<existingPid<<"!"<<std::endl<<"Use the -k flag to force killing it. Exiting..." << std::endl;
			exit(EXIT_FAILURE);
		}
	}

  /* Open any logs here */        
	Logger* log = Logger::getInstance();
	log->setFile(logFile);

	lout("Starting EtoA backend for "+gameRound);

  /* Our process ID and Session ID */
	if (isDaemon)
	{
		daemonize();
	}

	// The very usefull main loop
	int i=0;
	while (true)
	{
		//lout("foobar");
		sleep(1);
	}
	
	// This point should never be reached
	lout("Unexpectedly reached end of main()");
	return EXIT_FAILURE;
}
