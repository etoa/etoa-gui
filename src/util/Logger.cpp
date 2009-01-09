#include <time.h>
#include <iostream>
#include <fstream>
#include <sstream>

#include "Logger.h"

using namespace std;

Logger::Logger(std::string logFilePath)
{        
	origClogBuf = std::clog.rdbuf();
	LogStream	out(std::clog.rdbuf(), logFilePath);
 	std::clog.rdbuf(out.rdbuf());		
	std::clog << "Logging started"<<std::endl;
}

Logger::~Logger()
{
	std::clog << "Logging stopped"<<std::endl;
	std::clog.rdbuf(origClogBuf);
}



 LogBuf::LogBuf(std::streambuf *sb, std::string filePath):
      std::streambuf(),
      i_sbuf(sb),
      i_newline(true),
      i_cache(EOF)
    {
    	outFilePath = filePath;
      setp(0, 0);
      setg(0, 0, 0);
    }
    LogBuf::~LogBuf()
    {
      
    }

    bool	LogBuf::skip_prefix()
    {
      return true;
    }


    int	LogBuf::underflow()
    {
      if (i_cache == EOF)
      {
        if (i_newline)
          if (!skip_prefix())
    	return EOF;

        i_cache = i_sbuf->sbumpc();
        if (i_cache == '\n')
          i_newline = true;
        return i_cache;
      }
      else
        return i_cache;
    }

    int	LogBuf::uflow()
    {
      if (i_cache == EOF)
      {
        if (i_newline)
          if (!skip_prefix())
    	return EOF;
        
    int rc = i_sbuf->sbumpc();
        if (rc == '\n')
          i_newline = true;
        return rc;
      }
      {
        int rc = i_cache;
        i_cache = EOF;
        return rc;
      }
    }


    int	LogBuf::overflow(int c)
    {
      if (c != EOF)
      {
        if (i_newline)
        {
        	if (i_sbuf->sputn(i_prfx, i_len) != i_len)
    				return EOF;
          else
    				i_newline = false;
    		}

        int rc = i_sbuf->sputc(c);
        if (c == '\n')
          i_newline = true;
          
        return rc;
      }
      return 0;
    }

		std::streamsize LogBuf::xsputn ( const char* s, std::streamsize n )
		{
			struct tm *current;
			time_t now;
			time(&now);
			current = localtime(&now);
			char* a = asctime(current);
			a[strlen(a) - 1] = '\0';
			
			std::stringstream ss;;	
			if (i_newline)
				ss << std::endl << "["<<a<<"] ";

			for (int i=0;i<n;i++)
			{
				if ((int)s[i]>0)
					ss << s[i];
			}
		
			std::ofstream filestream(outFilePath.c_str(), std::ios::out | std::ios::app);
			if (filestream.is_open())
			{
				filestream << ss.str();				
			}
			filestream.close();				
			return std::streambuf::xsputn(s, n);
		}
		
    LogStream::LogStream(std::streambuf *sb, std::string filePath):
      std::ostream(new LogBuf(sb, filePath))
    {
    }


    LogStream::~LogStream()
    {

    }

