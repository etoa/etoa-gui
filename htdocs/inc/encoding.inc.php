<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
// $Id$
//////////////////////////////////////////////////////

/* encoding.inc.php von river */

function replace_ascii_control_chars($str)
{
	$controlChars = array(
		chr( 0) => '&#x2400;',
		chr( 1) => '&#x2401;',
		chr( 2) => '&#x2402;',
		chr( 3) => '&#x2403;',
		chr( 4) => '&#x2404;',
		chr( 5) => '&#x2405;',
		chr( 6) => '&#x2406;',
		chr( 7) => '&#x2407;',
		chr( 8) => '&#x2408;',
		chr( 9) => '&#x2409;',
		chr(10) => '&#x240A;',
		chr(11) => '&#x240B;',
		chr(12) => '&#x240C;',
		chr(13) => '&#x240D;',
		chr(14) => '&#x240E;',
		chr(15) => '&#x240F;',
		chr(16) => '&#x2410;',
		chr(17) => '&#x2411;',
		chr(18) => '&#x2412;',
		chr(19) => '&#x2413;',
		chr(20) => '&#x2414;',
		chr(21) => '&#x2415;',
		chr(22) => '&#x2416;',
		chr(23) => '&#x2417;',
		chr(24) => '&#x2418;',
		chr(25) => '&#x2419;',
		chr(26) => '&#x241A;',
		chr(27) => '&#x241B;',
		chr(28) => '&#x241C;',
		chr(29) => '&#x241D;',
		chr(30) => '&#x241E;',
		chr(31) => '&#x241F;',
		chr(127)=> '&#x2421;');
	return strtr($str, $controlChars);
}

function replace_ascii_control_chars_unicode($str)
{
	$controlChars = array(
		chr( 0) => chr(0x2400),
		chr( 1) => chr(0x2401),
		chr( 2) => chr(0x2402),
		chr( 3) => chr(0x2403),
		chr( 4) => chr(0x2404),
		chr( 5) => chr(0x2405),
		chr( 6) => chr(0x2406),
		chr( 7) => chr(0x2407),
		chr( 8) => chr(0x2408),
		chr( 9) => chr(0x2409),
		chr(10) => chr(0x240A),
		chr(11) => chr(0x240B),
		chr(12) => chr(0x240C),
		chr(13) => chr(0x240D),
		chr(14) => chr(0x240E),
		chr(15) => chr(0x240F),
		chr(16) => chr(0x2410),
		chr(17) => chr(0x2411),
		chr(18) => chr(0x2412),
		chr(19) => chr(0x2413),
		chr(20) => chr(0x2414),
		chr(21) => chr(0x2415),
		chr(22) => chr(0x2416),
		chr(23) => chr(0x2417),
		chr(24) => chr(0x2418),
		chr(25) => chr(0x2419),
		chr(26) => chr(0x241A),
		chr(27) => chr(0x241B),
		chr(28) => chr(0x241C),
		chr(29) => chr(0x241D),
		chr(30) => chr(0x241E),
		chr(31) => chr(0x241F),
		chr(127)=> chr(0x2421));
	return strtr($str, $controlChars);
}
?>