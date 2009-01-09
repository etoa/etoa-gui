import os

env = Environment(ENV = {'PATH' : os.environ['PATH'],
                         'TERM' : os.environ['TERM'],
                         'HOME' : os.environ['HOME']},  CCFLAGS = '-O3 -Wall')

conf = Configure(env) 

if not conf.CheckLib('mysqlpp'):
  print "Couldn't find MySQL++ library!"
  Exit(1) 

if not conf.CheckLib('boost_thread'):
  print "Couldn't find Boost Threads library!"
  Exit(1) 

env.Library(target="anyoption", source=Split("src/lib/anyoption/anyoption.cpp")) 

mainSrcFiles = Split("src/main.cpp src/util/Logger.cpp src/util/PidFile.cpp src/util/IPCMessageQueue.cpp src/util/ExceptionHandler.cpp src/util/sigsegv.c")
libs = Split("anyoption mysqlpp boost_thread")

env.Program(target="etoad", LIBS=libs, LIBPATH=".", source=mainSrcFiles)

