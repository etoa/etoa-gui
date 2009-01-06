env = Environment(CC = 'g++', CCFLAGS = '-O3 -Wall') 
conf = Configure(env) 

if not conf.CheckLib('mysqlpp'):
  print "Couldn't find mysqlpp library. Exiting."
  Exit(1) 

env.Library(target="anyoption", source=Split("src/lib/anyoption/anyoption.cpp")) 

mainSrcFiles = Split("src/main.cpp src/lib/logger.cpp src/lib/pidfile.cpp")
libs = Split("anyoption mysqlpp")

env.Program(target="etoad", LIBS=libs, LIBPATH=".", source=mainSrcFiles)

