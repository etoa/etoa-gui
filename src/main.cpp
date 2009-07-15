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
// Main loop framework
//
// $Rev$
// $Author$
// $Date$
//

#include "etoa.h"

#include "version.h"

using namespace std;

std::string gameRound;
std::string pidFile;
std::string logFile;

Logger* logr;
PIDFile* pf;

bool verbose = false;
bool detach = false;

int ownerUID;

std::string appPath;

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
	std::clog << "Caught signal "<<sig<<", exiting..."<<std::endl;
	std::clog << "EtoA backend "<<gameRound<<" unexpectedly stopped"<<std::endl;

	delete logr;

	// Restart after segfault
	if (sig==SIGSEGV)
	{
		std::string cmd = appPath + " "+(detach ? "-d" : "")+" -k -r "+gameRound;
		system(cmd.c_str());
	}
	
	exit(EXIT_FAILURE);
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
    exit(EXIT_SUCCESS);
  }

	/* Close out the standard file descriptors */
	close(STDIN_FILENO);
	close(STDOUT_FILENO);
	close(STDERR_FILENO);

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
  pf->write();	

  int myPid = (int)getpid();
	clog <<  "Daemon initialized with PID " << myPid << " and owned by " << getuid()<<std::endl;
}

/**
* Runs the message queue listener for receiving
* command from the frontend
*/
void msgQueueThread()
{                                   
	std::clog << "Message queue thread started"<<std::endl;
	
	IPCMessageQueue queue(Config::instance().getFrontendPath());
	if (queue.valid())
	{
		while (true)
		{
			std::string cmd = "";
			int id = 0;
			queue.rcvCommand(&cmd,&id);
			
			if (cmd == "planetupdate")
			{
				EntityUpdateQueue::instance().push(id);
			}
			else if (cmd == "configupdate")
			{
				Config::instance().reloadConfig();
			}
		}
	}
	std::clog << "Message queue thread ended"<<std::endl;
}

int main(int argc, char* argv[])
{
	// Register signal handlers
  signal(SIGABRT, &sighandler);
	signal(SIGTERM, &sighandler);
	signal(SIGINT, &sighandler);
	signal(SIGHUP, &sighandler);
	signal(SIGSEGV, &sighandler);
	signal(SIGQUIT, &sighandler);
	signal(SIGFPE, &sighandler);

	sleep(1);
	appPath = std::string(argv[0]);

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
  opt->addUsage( " -d  --daemon            Detach from console and run as daemon in background");
  opt->addUsage( " -v  --verbose           Detailed output");
  opt->addUsage( " -h  --help              Prints this help");
  opt->addUsage( " --version           Prints version information");
  opt->setFlag("help",'h');
  opt->setFlag("version");
  opt->setFlag("killexisting",'k');
  opt->setFlag("stop",'s');
  opt->setFlag("daemon",'d');
  opt->setFlag("verbose",'v');
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
  if( opt->getFlag( "version" )) 
  {	
  	cout << getVersion()<<endl;
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
  if( opt->getFlag( "daemon" ) || opt->getFlag( 'd' )) 
  {	
		detach = true;
	}
  if( opt->getFlag( "verbose" ) || opt->getFlag( 'v' )) 
  {	
		verbose = true;
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

  pf = new PIDFile(pidFile);

	// Check for existing instance
	if (pf->fileExists())
	{
		int existingPid = pf->readPid();
   	
   	if (stop)
   	{
   		std::clog << "Got manual kill by console" <<endl;
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

  // Open any logs here 
	logr = new Logger(logFile);
	clog << "Loggin started"<<endl;

  
	if (detach)
		daemonize();

	Config &config = Config::instance();
	config.setRoundName(gameRound);

	boost::thread mThread(&etoamain);
	boost::thread qThread(&msgQueueThread);

	mThread.join();	
	qThread.join();
	
	// This point should never be reached
	cerr << "Unexpectedly reached end of main()";
	return EXIT_FAILURE;
}
