
std::string versionNumber()
{
	return "1.0";
}


std::string getVersion()
{
	std::string out = ">> EtoA Backend Daemon <<\n\n";
	out += "(c) by EtoA Gaming, www.etoa.ch\n";
	out += "Version "+versionNumber()+"\n\n";
	out += "$Rev$\n";
	out += "$Date: 2009-09-24 00:22:40 +0200 (Thu, 24 Sep 2009) $\n";
	return out;
}

