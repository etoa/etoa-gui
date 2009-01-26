import os

env = Environment(ENV = {'PATH' : os.environ['PATH'],
                         'TERM' : os.environ['TERM'],
                         'HOME' : os.environ['HOME']},  CCFLAGS = '-O3 -g3 -fno-inline -O0')

conf = Configure(env) 

if not conf.CheckLib('mysqlpp'):
  print "Couldn't find MySQL++ library!"
  Exit(1) 

if not conf.CheckLib('boost_thread'):
  print "Couldn't find Boost Threads library!"
  Exit(1) 


mainSrcFiles = [Glob('src/util/*.c*'), Glob('src/lib/anyoption/*.cpp'), Glob('src/eventhandler/*.cpp'),Glob('src/eventhandler/*/*.cpp'),Glob('src/eventhandler/*/*/*.cpp'),Glob('src/eventhandler/*/*/*/*.cpp'), Glob('src/*.cpp') ]

libs = ["mysqlpp", "boost_thread"]

env.Program(target="etoad", LIBS=libs, LIBPATH=".", source=mainSrcFiles)

