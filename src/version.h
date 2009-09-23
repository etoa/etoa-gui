
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
	out += "$Date$\n";
	return out;
}

