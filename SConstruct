env = Environment(CC = 'g++', CCFLAGS = '-O3 -Wall') 
conf = Configure(env) 

if not conf.CheckLib('mysqlpp'):
  print "Couldn't find MySQL++ library!"
  Exit(1) 

if not conf.CheckLib('boost_thread'):
  print "Couldn't find Boost Threads library!"
  Exit(1) 

env.Library(target="anyoption", source=Split("src/lib/anyoption/anyoption.cpp")) 

mainSrcFiles = Split("src/main.cpp src/lib/logger.cpp src/lib/pidfile.cpp")
libs = Split("anyoption mysqlpp boost_thread")

env.Program(target="etoad", LIBS=libs, LIBPATH=".", source=mainSrcFiles)

