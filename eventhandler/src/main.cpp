//////////////////////////////////////////////////
//   ____    __           ______                //
//  /\  _`\ /\ \__       /\  _  \               //
//  \ \ \L\_\ \ ,_\   ___\ \ \L\ \              //
//   \ \  _\L\ \ \/  / __`\ \  __ \             //
//    \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \            //
//     \ \____/\ \__\ \____/\ \_\ \_\           //
//      \/___/  \/__/\/___/  \/_/\/_/  	        //
//                                              //
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame            //
// Ein Massive-Multiplayer-Online-Spiel         //
// Programmiert von Nicolas Perrenoud           //
// www.nicu.ch | mail@nicu.ch                   //
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
//////////////////////////////////////////////////

/**
* Startup function, bootstraps the daemon and
* initializes threads, logging and pidfile.
*
* @author Nicolas Perrenoud<mrcage@etoa.ch>
*
* Copyright (c) 2004 by EtoA Gaming, www.etoa.net
*
* $Rev$
* $Author$
* $Date$
*/

#include "etoa.h"
#include "version.h"

using namespace std;

std::string gameRound;
std::string pidFile;

PIDFile* pf;

bool detach = false;

int ownerUID;

std::string appPath;
std::string configFile;

// Signal handler
void sighandler(int sig)
{
	// Clean up pidfile
	delete pf;

	if (sig == SIGTERM)
	{
		LOG(LOG_NOTICE,"Received ordinary termination signal (SIGTERM), shutting down");
		exit(EXIT_SUCCESS);
	}
	if (sig == SIGINT)
	{
		LOG(LOG_WARNING,"Received interrupt from keyboard (SIGINT), shutting down");
		exit(EXIT_SUCCESS);
	}

	LOG(LOG_ERR,"Caught signal "<<sig<<", shutting down due to error");
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
		LOG(LOG_CRIT, "Could not fork parent process");
		exit (EXIT_FAILURE);
	}

	/* If we got a good PID, then we can exit the parent process. */
	if (pid > 0)
	{
		exit (EXIT_SUCCESS);
	}

	/* Close out the standard file descriptors */
	close (STDIN_FILENO);
	close (STDOUT_FILENO);
	close (STDERR_FILENO);

	/* Change the file mode mask */
	umask(0);

	/* Create a new SID for the child process */
	sid = setsid();
	if (sid < 0)
	{
		LOG(LOG_CRIT, "Unable to get SID for child process");
		exit (EXIT_FAILURE);
	}

	// Create pidfile
	pf->write();

	int myPid = (int) getpid();
	LOG(LOG_NOTICE, "Daemon initialized with PID " << myPid << " and owned by " << getuid());

}

bool validateRoundName(const std::string& s)
{
    for (std::string::size_type i = 0; i < s.size(); i++) {
        if (!isalnum(s[i])) return false;
    }

    return true;
}

/**
* Returns a version description
*/
std::string getVersion()
{
	std::string out;
	out  = "\nEscape to Andromeda\n";
	out += "Eventhandler Backend Service (etoad)\n\n";
	out += "Copyright (c) EtoA Gaming, www.etoa.ch\n\n";
	out += "Version: " __ETOAD_VERSION_STRING__ "\n";
	out += "Built: " __DATE__ ", " __TIME__ "\n";
	return out;
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

	logPrio(LOG_INFO);

	// Parse command line
	AnyOption *opt = new AnyOption();
	opt->addUsage( "Options: " );
	opt->addUsage( "  -d, --daemon            Detach from console and run as daemon in background");
	opt->addUsage( "  -s, --stop              Stops a running instance of this backend");
	opt->addUsage( "");
	opt->addUsage( "  -p, --pidfile path      Path to PID file (default: /var/run/etoad/INSTANCE.pid)");
	opt->addUsage( "  -c, --config path       Path to config file (default: /etc/etoad/INSTANCE.conf)");
	opt->addUsage( "  -u, --uid userid        Select user id under which it runs (necessary if you are root)");
	opt->addUsage( "  -k, --killexisting      Kills an already running instance of this backend before starting this instance");
	opt->addUsage( "  -l, --log level         Specify log level (0=emerg, ... , 7=everything), default is 6");
	opt->addUsage( "");
	opt->addUsage( "      --debug             Enable debug mode");
	opt->addUsage( "  -h, --help              Prints this help");
	opt->addUsage( "      --version           Prints version information");
	opt->setFlag("help",'h');
	opt->setFlag("version");
	opt->setFlag("killexisting",'k');
	opt->setFlag("stop",'s');
	opt->setFlag("daemon",'d');
	opt->setFlag("debug");
	opt->setOption("log",'l');
	opt->setOption("userid",'u');
	opt->setOption("pidfile",'p');
	opt->setOption("config",'c');
	opt->setOption("sleep",'t');
	opt->processCommandArgs( argc, argv );

	appPath = std::string(argv[0]);

	// Show help
	if(argc <= 1 || opt->getFlag( "help" ) || opt->getFlag( 'h' ))
	{
		std::cerr << "Usage: " << appPath << " INSTANCE [options]" << std::endl;
		opt->printUsage();
 		return EXIT_SUCCESS;
	}

	// Show version info
	if( opt->getFlag( "version" ))
	{
		std::cout << getVersion()<<endl;
 		return EXIT_SUCCESS;
	}

	// Set game round
	gameRound = argv[1];
	if (!validateRoundName(gameRound))
	{
		LOG(LOG_ERR,"Invalid game round name!");
		return EXIT_FAILURE;
	}

	// Enable debug if requested
	if (opt->getFlag("debug"))
	{
		debugEnable(1);
	}

	bool killExistingInstance = false;
	if (opt->getFlag("killexisting") || opt->getFlag('k'))
	{
		killExistingInstance = true;
	}
	bool stop = false;
	if (opt->getFlag("stop") || opt->getFlag('s'))
	{
		stop = true;
	}
	if (opt->getFlag("daemon") || opt->getFlag('d'))
	{
		detach = true;
	}
	else
	{
		std::cout << "Escape to Andromeda Event-Handler" << std::endl;
		std::cout << "(C) 2007 EtoA Gaming Switzerland, www.etoa.ch" << std::endl;
		std::cout << "Version " __ETOAD_VERSION_STRING__ "" << std::endl<< std::endl;
	}

	// Log verbosity
	if (opt->getValue('l') != NULL)
	{
		int lvl = atoi(opt->getValue('l'));
		if (LOG_DEBUG >= lvl && lvl >= LOG_EMERG)
		{
			std::cout << "Setting log verbosity to " << lvl << std::endl;
			logPrio(lvl);
		}
	}
	else if (opt->getValue("log") != NULL)
	{
		int lvl = atoi(opt->getValue("log"));
		if (LOG_DEBUG >= lvl && lvl >= LOG_EMERG)
		{
			std::cout << "Setting log verbosity to " << lvl << std::endl;
			logPrio(lvl);
		}
	}

	// Determine config directory
	if (opt->getValue('c') != NULL)
	{
		configFile = opt->getValue('c');
	}
	else if (opt->getValue("config") != NULL)
	{
		configFile = opt->getValue("config");
	}
	else
	{
		configFile = "/etc/etoad/" + gameRound + ".conf";
	}
	if (!boost::filesystem::is_regular_file(configFile) && !boost::filesystem::is_symlink(configFile))
	{
		LOG(LOG_ERR, "Config file " << configFile << " does not exist!");
		return EXIT_FAILURE;
	}

	// Sets the round name the logger uses to create the etoad.roundname.log files
	logProgam(gameRound);

	// Set pidfile
	if( opt->getValue('p') != NULL)
	{
		pidFile = opt->getValue('p');
	}
	else if (opt->getValue("pidfile") != NULL )
	{
		pidFile = opt->getValue("pidfile");
	}
	else
	{
		pidFile = "/var/run/etoad/"+gameRound+".pid";
	}

	// Set user
	if( opt->getValue('u') != NULL)
	{
		ownerUID = atoi(opt->getValue('u'));
	}
	else if (opt->getValue("uid") != NULL )
	{
		ownerUID = atoi(opt->getValue("uid"));
	}
	else
	{
		ownerUID = (int)getuid();
	}

	// Set correct uid
	if (setuid(ownerUID) != 0)
	{
		LOG(LOG_ERR, "Unable to change user id!");
		exit (EXIT_FAILURE);
	}
	// Check uid
	if (getuid() == 0)
	{
		LOG(LOG_ERR, "This software cannot be run as root!");
		exit (EXIT_FAILURE);
	}


	pf = new PIDFile(pidFile);

	// Check for existing instance
	if (pf->fileExists())
	{
		int existingPid = pf->readPid();

		if (stop)
		{
			kill(existingPid, SIGTERM);
			DEBUG("Killing process " << existingPid);
			exit (EXIT_SUCCESS);
		}
		if (killExistingInstance)
		{
			std::cout << "EtoA Daemon " << gameRound << " seems to run already with PID "
					<<existingPid<<"! Killing this instance..." << std::endl;
			int kres = kill(existingPid,SIGTERM);
			if (kres<0)
			{
				if (errno==EPERM)
				{
					std::cerr << "I am not allowed to kill the instance. Exiting..." << std::endl;
					exit(EXIT_FAILURE);
				}
				else
				{
					std::cerr << "The process doesn't exist, perhaps the PID file was outdated. Continuing..." << std::endl;
				}
			}
			sleep(1);
		}
		else
		{
			std::cerr << "EtoA Daemon " << gameRound << " is already running with PID "
					<<existingPid<<"!"<<std::endl
					<<"Use the -k flag to force killing it and continue with this instance. Exiting..." << std::endl;
			exit(EXIT_FAILURE);
		}
	}
 	else if (stop)
 	{
 		std::cerr << "No running process found, exiting..."<<std::endl;
 		return EXIT_FAILURE;
 	}

	LOG(LOG_NOTICE,"Starting EtoA event-handler " __ETOAD_VERSION_STRING__ " for universe " << gameRound);

	if (detach)
	{
		daemonize();
	}
	else
	{
		pf->write();
	}

	Config &config = Config::instance();
	config.setConfigFile(configFile);
	if (opt->getValue('t') != NULL)
	{
		config.setSleep(atoi(opt->getValue('t')));
	}

	delete opt;

	// Enter main loop
	etoamain();

	// This point should never be reached
	cerr << "Unexpectedly reached end of main()";
	return EXIT_FAILURE;
}
