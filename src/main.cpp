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

#include <boost/thread.hpp>
#include <mysql++/mysql++.h>

#include "lib/anyoption/anyoption.h"

#include "util/IPCMessageQueue.h"
#include "util/Logger.h"
#include "util/PidFile.h"
#include "util/sigsegv.h"

using namespace std;

std::string versionString = "0.1 alpha";

std::string gameRound;
std::string pidFile;
std::string logFile;

Logger* logr;
PIDFile* pf;

int ownerUID;

// Signal handler
void sighandler(int sig)
{
	// Clean up pidfile
	delete pf;
	
	if (sig == SIGTERM)
	{
	  std::clog << "Caught signal SIGTERM, exiting..."<<std::endl;
		std::clog << "EtoA backend "<<gameRound<<" stopped"<<std::endl;
		delete logr;
		exit(EXIT_SUCCESS);
	}

	std::cerr << "Caught signal "<<sig<<", exiting..."<<std::endl;
	std::cerr << "EtoA backend "<<gameRound<<" unexpectedly stopped"<<std::endl;

	delete logr;
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
void daemonize()
{

  pid_t pid, sid;
  /* Fork off the parent process */
  pid = fork();
  if (pid < 0) 
  {
  	cerr << "Could not fork parent process";
 		exit(EXIT_FAILURE);
  }

  /* If we got a good PID, then we can exit the parent process. */
  if (pid > 0) 
  {
  	//cout << "Daemon has PID "<<pid<<endl;
    exit(EXIT_SUCCESS);
  }

  // Open any logs here 
	logr = new Logger(logFile);
	clog << "Loggin started"<<endl;

	/* Close out the standard file descriptors */
  //close(STDIN_FILENO);
  //close(STDOUT_FILENO);
  //close(STDERR_FILENO);


  /* Change the file mode mask */
  umask(0);
          
  /* Create a new SID for the child process */
  sid = setsid();
  if (sid < 0) 
  {
  	cerr << "Unable to get SID for child process";
    exit(EXIT_FAILURE);
  }

  // Create pidfile
  pf = new PIDFile(pidFile);
  pf->write();	


  int myPid = (int)getpid();
	clog <<  "Daemon initialized with PID " << myPid << " and owned by " << getuid()<<std::endl;
}

void msgQueueThread()
{
	std::clog << "Message queue thread started"<<std::endl;
	IPCMessageQueue queue;
	while (true)
	{
		std::string res = queue.rcv();
		clog << "Queue: "<< res<<std::endl;
	}
	std::clog << "Message queue thread ended"<<std::endl;
}

void mainThread()
{
	std::clog << "Main thread started"<<std::endl;

  // Connect to the sample database.
  mysqlpp::Connection conn(false);
  mysqlpp::Query query = conn.query();
  bool dbOnline = false;
  if (conn.connect("etoatest", "localhost", "etoatest", "etoatest")) 
  {
		dbOnline = true;
  }
  else 
  {
  	cerr << "DB connection failed: " << conn.error()<<std::endl;
  }
  	
	while (true)
	{
		if (dbOnline)
		{
      query << "select count(*) as cnt from users";
      if (mysqlpp::Result res = query.store()) 
      {
				mysqlpp::Row pRow = res.at(0);
        clog << pRow["cnt"] << " user registered"<<endl;
      }
      else 
      {
      	cerr << "Failed to get user list: " << query.error();
      }
		}
		sleep(600);
	}	
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
  opt->addUsage( " -k  --killexisting      Kills an already running instance of this backend before starting this instance");
  opt->addUsage( " -s  --stop              Stops a running instance of this backend");
  opt->addUsage( " -h  --help              Prints this help");
  opt->addUsage( " -v  --version           Prints version information");
  opt->setFlag("help",'h');
  opt->setFlag("version",'v');
  opt->setFlag("killexisting",'k');
  opt->setFlag("stop",'s');
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
	bool stop = false;
  if( opt->getFlag( "stop" ) || opt->getFlag( 's' )) 
  {	
		stop = true;
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
   	
   	if (stop)
   	{
   		clog << "Got manual kill by console" <<endl;
   		kill(existingPid,SIGTERM);   
   		std::cout << "Killing process "<<existingPid<<endl;
   		exit(EXIT_SUCCESS);		
   	}
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
			std::cout << "EtoA Daemon " << gameRound << " is already running with PID "<<existingPid<<"!"<<std::endl<<"Use the -k flag to force killing it and continue with this instance. Exiting..." << std::endl;
			exit(EXIT_FAILURE);
		}
	}
 	else if (stop)
 	{
 		std::cout << "No running process found, exiting..."<<std::endl;
 		return EXIT_FAILURE;		
 	}	


	daemonize();

	boost::thread qThread(&msgQueueThread);
	boost::thread mThread(&mainThread);

	qThread.join();	
	mThread.join();	

	
	// This point should never be reached
	cerr << "Unexpectedly reached end of main()";
	return EXIT_FAILURE;
}
