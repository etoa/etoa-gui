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

/**
* Returns the current version number
*/
std::string versionNumber()
{
	return "1.0";
}

/**
* Returns a version description
*/
std::string getVersion()
{
	std::string out;
		
	out  = "//////////////////////////////////////////////////\n";
	out += "//   ____    __           ______                //\n";
	out += "//  /\\  _`\\ /\\ \\__       /\\  _  \\               //\n";
	out += "//  \\ \\ \\L\\_\\ \\ ,_\\   ___\\ \\ \\L\\ \\              //\n";
	out += "//   \\ \\  _\\L\\ \\ \\/  / __`\\ \\  __ \\             //\n";
	out += "//    \\ \\ \\L\\ \\ \\ \\_/\\ \\L\\ \\ \\ \\/\\ \\            //\n";
	out += "//     \\ \\____/\\ \\__\\ \\____/\\ \\_\\ \\_\\           //\n";
	out += "//      \\/___/  \\/__/\\/___/  \\/_/\\/_/  	        //\n" ;
	out += "//                                              //\n";
	out += "//////////////////////////////////////////////////\n";
	out += "// The Andromeda-Project-Browsergame            //\n";
	out += "// Ein Massive-Multiplayer-Online-Spiel         //\n";
	out += "// Programmiert von Nicolas Perrenoud           //\n";
	out += "// www.nicu.ch | mail@nicu.ch                   //\n";
	out += "// als Maturaarbeit '04 am Gymnasium Oberaargau //\n";
	out += "//////////////////////////////////////////////////\n\n";		
	out += ">> Backend Service (Eventhandler) <<\n\n";
	out += "(c) by EtoA Gaming, www.etoa.ch\n";
	out += "Version "+versionNumber()+"\n\n";
	out += "$Rev$\n";
	out += "$Date$\n";
	return out;
}

