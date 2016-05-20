/*
* Planet-Name-Generator by Steven Savage of Seventh Sanctum(TM)
* http://www.seventhsanctum.com/
*
* Thanks for this great piece of code!
*
* Seventh Sanctum™ and its contents are copyright (c) 2006 by Steven Savage except 
* where otherwise noted. No infringement or claim on any copyrighted material is intended.
* Code provided in these pages is free for all to use as long as the author and this website are credited.
* No guarantees whatsoever are made regarding these generators or their contents. 
*/

var aVocab = new Array();

var intIncr
var intCat

intCat =0
intIncr=0

aVocab[intCat]= new Array();
aVocab[intCat][0]="SEED"
aVocab[intCat][1] = new Array()
aVocab[intCat][1][intIncr++]="<NAME>"
aVocab[intCat][1][intIncr++]="<NAME> <EXTRA>"


intCat++
intIncr=0
aVocab[intCat]= new Array();
aVocab[intCat][0]="EXTRA"
aVocab[intCat][1] = new Array()
aVocab[intCat][1][intIncr++]="<GRLETTER>"
aVocab[intCat][1][intIncr++]="<GRNUMBER>"
aVocab[intCat][1][intIncr++]="<GRTERM>"
aVocab[intCat][1][intIncr++]="<NUMBER>"

intCat++
intIncr=0
aVocab[intCat]= new Array();
aVocab[intCat][0]="GRLETTER"
aVocab[intCat][1] = new Array()
aVocab[intCat][1][intIncr++]="Alpha"
aVocab[intCat][1][intIncr++]="Beta"
aVocab[intCat][1][intIncr++]="Gamma"
aVocab[intCat][1][intIncr++]="Delta"
aVocab[intCat][1][intIncr++]="Epsilon"
aVocab[intCat][1][intIncr++]="Zeta"
aVocab[intCat][1][intIncr++]="Eta"
aVocab[intCat][1][intIncr++]="Theta"
aVocab[intCat][1][intIncr++]="Iota"
aVocab[intCat][1][intIncr++]="Kappa"
aVocab[intCat][1][intIncr++]="Lambda"
aVocab[intCat][1][intIncr++]="Mu"
aVocab[intCat][1][intIncr++]="Nu"
aVocab[intCat][1][intIncr++]="Xi"
aVocab[intCat][1][intIncr++]="Omicron"
aVocab[intCat][1][intIncr++]="Pi"
aVocab[intCat][1][intIncr++]="Rho"
aVocab[intCat][1][intIncr++]="Sigma"
aVocab[intCat][1][intIncr++]="Tau"
aVocab[intCat][1][intIncr++]="Upsilon"
aVocab[intCat][1][intIncr++]="Phi"
aVocab[intCat][1][intIncr++]="Chi"
aVocab[intCat][1][intIncr++]="Psi"
aVocab[intCat][1][intIncr++]="Omega"


intCat++
intIncr=0
aVocab[intCat]= new Array();
aVocab[intCat][0]="GRNUMBER"
aVocab[intCat][1] = new Array()
aVocab[intCat][1][intIncr++]="I"
aVocab[intCat][1][intIncr++]="II"
aVocab[intCat][1][intIncr++]="III"
aVocab[intCat][1][intIncr++]="IV"
aVocab[intCat][1][intIncr++]="V"
aVocab[intCat][1][intIncr++]="VI"
aVocab[intCat][1][intIncr++]="VII"
aVocab[intCat][1][intIncr++]="VIII"
aVocab[intCat][1][intIncr++]="IX"
aVocab[intCat][1][intIncr++]="X"
aVocab[intCat][1][intIncr++]="XI"
aVocab[intCat][1][intIncr++]="XII"

intCat++
intIncr=0
aVocab[intCat]= new Array();
aVocab[intCat][0]="GRTERM"
aVocab[intCat][1] = new Array()
aVocab[intCat][1][intIncr++]="Prime"
aVocab[intCat][1][intIncr++]="Secundus"
aVocab[intCat][1][intIncr++]="Tertius"
aVocab[intCat][1][intIncr++]="Quartus"
aVocab[intCat][1][intIncr++]="Quintus"
aVocab[intCat][1][intIncr++]="Sextus"
aVocab[intCat][1][intIncr++]="Septimus"
aVocab[intCat][1][intIncr++]="Octavus"
aVocab[intCat][1][intIncr++]="Nonus"
aVocab[intCat][1][intIncr++]="Decimus"
aVocab[intCat][1][intIncr++]="Undecimus"
aVocab[intCat][1][intIncr++]="Duodecimus"

intCat++
intIncr=0
aVocab[intCat]= new Array();
aVocab[intCat][0]="NUMBER"
aVocab[intCat][1] = new Array()
aVocab[intCat][1][intIncr++]="1"
aVocab[intCat][1][intIncr++]="2"
aVocab[intCat][1][intIncr++]="3"
aVocab[intCat][1][intIncr++]="4"
aVocab[intCat][1][intIncr++]="5"
aVocab[intCat][1][intIncr++]="6"
aVocab[intCat][1][intIncr++]="7"
aVocab[intCat][1][intIncr++]="8"
aVocab[intCat][1][intIncr++]="9"
aVocab[intCat][1][intIncr++]="10"
aVocab[intCat][1][intIncr++]="11"
aVocab[intCat][1][intIncr++]="12"


//Name generator code

	var aLetters = new Array();
	var aLettersType = new Array();
	var aLetA = new Array();

// The "marky array" to record what letters can be associated with what.  In order to keep the 1-26 relationship, there
// is a "placeholder" at the 0 point, which can also be used for other data later;

aLetA[1]=[0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];  //a
aLetA[2]=[0,1,1,0,1,1,0,0,1,1,1,0,1,0,0,1,0,0,1,0,0,1,0,1,0,1,1];  //b
aLetA[3]=[0,1,0,1,0,1,0,0,1,1,0,1,1,1,1,1,0,0,1,1,1,1,0,1,0,1,1];  //c
aLetA[4]=[0,1,1,0,1,1,0,0,0,1,0,0,1,0,0,1,0,0,1,0,0,1,0,0,0,1,1];  //d
aLetA[5]=[0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];  //e
aLetA[6]=[0,1,0,0,0,1,1,0,0,1,0,0,0,0,0,1,1,0,1,0,0,1,0,0,0,1,0];  //f
aLetA[7]=[0,1,0,0,0,1,0,1,1,1,0,0,1,0,0,1,0,0,1,0,0,1,0,0,0,1,0];  //g
aLetA[8]=[0,1,0,0,0,1,0,0,0,1,0,0,1,0,0,1,0,0,1,0,0,1,0,1,0,1,0];  //h
aLetA[9]=[0,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];  //i
aLetA[10]=[0,1,0,0,0,1,0,1,0,1,0,0,0,0,0,1,0,1,1,0,0,1,0,0,0,1,0];  //j
aLetA[11]=[0,1,0,1,0,1,0,0,1,1,0,1,1,0,0,1,0,0,1,0,1,1,0,1,0,1,0];  //k
aLetA[12]=[0,1,0,0,0,1,0,0,0,1,0,0,1,1,1,1,1,0,1,1,1,1,0,0,0,0,0];  //l
aLetA[13]=[0,1,0,0,0,1,0,0,1,1,0,1,1,1,1,1,0,0,1,0,0,1,0,0,0,1,0];  //m
aLetA[14]=[0,1,0,0,0,1,0,0,1,1,0,1,1,1,1,1,0,0,1,0,0,1,0,0,0,1,0];  //n
aLetA[15]=[0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];  //o
aLetA[16]=[0,1,0,1,0,1,1,0,1,1,0,0,1,0,0,1,1,1,1,0,0,1,0,0,0,1,0];  //p
aLetA[17]=[0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,1,0];  //q
aLetA[18]=[0,1,0,0,1,1,0,0,1,1,0,0,0,1,1,1,0,0,1,0,0,1,0,0,0,1,0];  //r
aLetA[19]=[0,1,0,0,1,1,0,0,1,1,0,1,1,0,0,1,1,0,1,1,1,1,1,1,0,1,1];  //s
aLetA[20]=[0,1,0,0,0,1,0,0,1,1,0,0,1,1,1,1,1,0,1,1,1,1,0,0,0,0,1];  //t
aLetA[21]=[0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];  //u
aLetA[22]=[0,1,0,0,0,1,0,0,0,1,0,0,1,0,0,1,0,0,1,0,0,1,0,1,0,1,0];  //v
aLetA[23]=[0,1,0,0,0,1,0,0,1,1,0,0,0,0,0,1,1,0,1,0,0,1,1,0,0,0,0];  //w
aLetA[24]=[0,1,0,0,0,1,0,1,1,1,0,0,0,0,0,1,0,0,0,1,0,1,0,0,1,1,0];  //x
aLetA[25]=[0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1];  //y
aLetA[26]=[0,1,0,1,0,1,0,0,1,1,0,0,0,0,0,1,0,0,1,1,0,1,0,0,0,1,1];  //z

aLetters[1]="a"
aLettersType[1]=2
aLetters[2]="b"
aLettersType[2]=1
aLetters[3]="c"
aLettersType[3]=1
aLetters[4]="d"
aLettersType[4]=1
aLetters[5]="e"
aLettersType[5]=2
aLetters[6]="f"
aLettersType[6]=1
aLetters[7]="g"
aLettersType[7]=1
aLetters[8]="h"
aLettersType[8]=1
aLetters[9]="i"
aLettersType[9]=2
aLetters[10]="j"
aLettersType[10]=1
aLetters[11]="k"
aLettersType[11]=1
aLetters[12]="l"
aLettersType[12]=1
aLetters[13]="m"
aLettersType[13]=1
aLetters[14]="n"
aLettersType[14]=1
aLetters[15]="o"
aLettersType[15]=2
aLetters[16]="p"
aLettersType[16]=1
aLetters[17]="q"
aLettersType[17]=1
aLetters[18]="r"
aLettersType[18]=1
aLetters[19]="s"
aLettersType[19]=1
aLetters[20]="t"
aLettersType[20]=1
aLetters[21]="u"
aLettersType[21]=2
aLetters[22]="v"
aLettersType[22]=1
aLetters[23]="w"
aLettersType[23]=1
aLetters[24]="x"
aLettersType[24]=1
aLetters[25]="y"
aLettersType[25]=1
aLetters[26]="z"
aLettersType[26]=1


		//GetName
		//      Length of name
		//      Consonant Count
		//      Vowel Count
		//      %chance vowel
		//      Must Start Consanant
		//      Must Start Vowel
		//      Must End Consanant
		//      Must End Vowel
		//      %double consanants
		//      Only double consanants once
		//      %double vowels
		//      Only double vowels once
		//	String of characters to excluse
	function GetName(iMaxCount, iMaxCons, iMaxVowel, iVowelChance, bStartCons, bStartVowel, bEndCons, bEndVowel, iDoubleCons, bDoubleConOnce, iDoubleVowel, bDoubleVowelOnce, sLeaveOut)
	{
		var sReturn
		var iLastChar, iCurChar, iCharCount, iConsCount, iVowelCount

		iLastChar = 0;
		iCharCount = 0;
		iConsCount = 0;
		iVowelCount = 0;
		sReturn ="";            

		while (iCharCount<iMaxCount)
		{
			//Check to see if there are too many consonants
			
			if ((iConsCount >= iMaxCons))
			{
				iCurChar = GetChar(iLastChar, sLeaveOut);
				while (aLettersType[iCurChar] == 1)
				{
					iCurChar = GetChar(iLastChar, sLeaveOut);
				}
			}
			else if (iVowelCount >= iMaxVowel)
			{
				iCurChar = GetChar(iLastChar, sLeaveOut);
				while (aLettersType[iCurChar] == 2)
				{
					iCurChar = GetChar(iLastChar, sLeaveOut);
				}
			}                       
			else
			{
				//See if starting rules must be addressed
				if ((iCharCount == 0) && ((bStartCons == true) || (bStartVowel == true)))
				{
					if (bStartCons == true)
					{
						iCurChar = GetChar(iLastChar, sLeaveOut);
						while (aLettersType[iCurChar] == 2)
						{
							iCurChar = GetChar(iLastChar, sLeaveOut);
						}
					}
					else
					{
						iCurChar = GetChar(iLastChar, sLeaveOut);
						while (aLettersType[iCurChar] == 1)
						{
							iCurChar = GetChar(iLastChar, sLeaveOut);
						}
					}
				}
				else if ((iCharCount == (iMaxCount - 1)) && ((bEndCons == true) || (bEndVowel == true)))
				{
					if (bEndCons == true)
					{
						iCurChar = GetChar(iLastChar, sLeaveOut);
						while (aLettersType[iCurChar] == 2)
						{
							iCurChar = GetChar(iLastChar, sLeaveOut);
						}
					}
					else
					{
						iCurChar = GetChar(iLastChar, sLeaveOut);
						while (aLettersType[iCurChar] == 1)
						{
							iCurChar = GetChar(iLastChar, sLeaveOut);
						}
					}
				}
				else
				{       
					if (GenNumber(100) <= iVowelChance)
					{
						iCurChar = GetChar(iLastChar, sLeaveOut);
						while (aLettersType[iCurChar] == 1)
						{
							iCurChar = GetChar(iLastChar, sLeaveOut);
						}
					}
					else
					{
						iCurChar = GetChar(iLastChar, sLeaveOut);
						while (aLettersType[iCurChar] == 2)
						{
							iCurChar = GetChar(iLastChar, sLeaveOut);
						}
					}
				}
			}

			if (aLettersType[iCurChar] == 1)
			{
				iVowelCount = 0;
				++iConsCount;
			}
			else if (aLettersType[iCurChar] == 2)
			{
				iConsCount = 0;
				++iVowelCount;
			}
			else
			{
				iConsCount = 0;
				iVowelCount = 0;
			}
			++iCharCount;
			sReturn = sReturn + aLetters[iCurChar];
			iLastChar=iCurChar;

			// double letters?
			//iDoubleCons, bDoubleConOnce, iDoubleVowel, bDoubleVowelOnce

			if ((iDoubleCons + iDoubleVowel) > 0)
			{
				if ((iDoubleCons >0) && (aLettersType[iCurChar] == 1))
				{
					if ((iConsCount == 1) && (iCharCount >1))
					{
						if (aLetA[iCurChar][iCurChar] != 0)
						{
							if(GenNumber(100) <= iDoubleCons)
							{
								sReturn = sReturn + aLetters[iCurChar];
								++iCharCount;
								++iConsCount;
								if (bDoubleConOnce)
								{
									iDoubleCons = 0;
								}
							}
						}
					}
				}
				if ((iDoubleVowel >0) && (aLettersType[iLastChar] == 2))
				{
					if ((iVowelCount == 1) && (iCharCount >1))
					{
						if (aLetA[iCurChar][iCurChar] != 0)
						{
							if(GenNumber(100) <= iDoubleVowel)
							{
								sReturn = sReturn + aLetters[iCurChar];
								++iCharCount;
								++iVowelCount;
								if (bDoubleVowelOnce)
								{
									iDoubleVowel = 0;
								}
							}
						}
					}
				}
			}


			//to enforce "end rules"
			if (iCharCount == (iMaxCount -1))
			{
				if (bEndCons || bEndVowel)
				{
					iConsCount = 0;
					iVowelCount = 0;        
				}
			}
		}

	
		//Capitalize sLine
		sReturn = (sReturn.charAt(0).toUpperCase()) + sReturn.slice(1, sReturn.length);
		return sReturn;
	}

	function GetChar(iLastChar, sLeaveOut)
	{
		var iCharReturn, iCharIterate

		if (iLastChar == 0)
		{
			iCharReturn = GenNumber(26);
			while (iCharReturn<1)
			{
			iCharReturn = GenNumber(26);
			}
			while (sLeaveOut.indexOf(aLetters[iCharReturn]) > -1)
			{
				iCharReturn = GenNumber(26);
				while (iCharReturn<1)
				{
				iCharReturn = GenNumber(26);
				}
			}
		}
		else
		{
			iCharIterate = GenNumber(26);
			while (iCharIterate<1)
			{
				iCharIterate = GenNumber(26);
			}		
			//alert (aLetters[iCharIterate] + " picked to follow " + aLetters[iLastChar]);
			var bContinue = false;
			while (bContinue == false)
			{
				bContinue = true;
				if (iCharIterate == iLastChar)
				{
					bContinue = false;
				}

				if (sLeaveOut.indexOf(aLetters[iCharIterate])>-1)
				{
					bContinue = false;
				}

				if (aLetA[iLastChar][iCharIterate] == 0)
				{
					bContinue = false;
				}

				if (bContinue == false)
				{
					++iCharIterate;
					if (iCharIterate == iLastChar)
					{
						++iCharIterate;
					}
					if (iCharIterate >= 27)
					{
						iCharIterate=1;
					}
				}
			}

			iCharReturn = iCharIterate;
		}

		return iCharReturn
	}



//Generator code

		//GetName
		//      Length of name
		//      Consonant Count
		//      Vowel Count
		//      %chance vowel
		//      Must Start Consanant
		//      Must Start Vowel
		//      Must End Consanant
		//      Must End Vowel
		//      %double consanants
		//      Only double consanants once
		//      %double vowels
		//      Only double vowels once
		//	String of characters to excluse
	function GetPlanetName()
	{
		var strReturn

		switch(GenNumber(6))
		{
			case 0:
				//Generic planet
				strReturn = GetName(4 + GenNumber(4), 2, 2, 50, false, false, false, false, 10, true, 5, true, "");
				break;
			case 1:
				//European Sounding
				strReturn = GetName(3 + GenNumber(4), 2, 2, 50, false, false, true, false, 15,true, 0, false,"xz");
				break;
			case 2:
				//Mideastern Sounding
				strReturn = GetName(2 + GenNumber(6), 2, 1, 650, false, false, false, false, 0, true, 0, false, "kflqvwyx");
				break;
			case 3:
				//Eastern Sounding
				strReturn = GetName(4 + GenNumber(4), 1, 1, 50, false, false, false, true, 0, false	, 0, false,"pqvxz");
				break;
			case 4:
				//Generic Eastern
				strReturn = GetName(2 + GenNumber(2), 1, 1, 50, true, false, false, false, 0, false, 0, false,"v") + "-" + GetName(2 + GenNumber(2), 1, 1, 50, true, false, false, false, 0, false, 0, false,"v");
				break;
			case 5:
				strReturn = GetName(3 + GenNumber(3), 2, 2, 50, false, false, false, false, 5, true, 5, true, "") + "-" + GetName(3 + GenNumber(3), 2, 2, 50, false, false, false, false, 5, true, 5, true, "");
				break;
			case 6:
				strReturn = GetName(1 + GenNumber(2), 1, 1, 50, false, false, false, false, 0, true, 0, true, "") + GetName(4 + GenNumber(3), 2, 2, 50, false, false, false, false, 5, true, 5, true, "");
				break;
		}

		return strReturn;
	}

	function GenNumber(nRange)
	{
                var iNumGen
		iNumGen = Math.round((Math.random() * nRange));

                return iNumGen;
	}

        function GetFrom(aArray)
	{
		var undefined
		var sReturn
		sReturn = aArray[GenNumber(aArray.length)];
		if (sReturn == undefined)
		{
			sReturn = GetFrom(aArray)
		}
		return sReturn
	}


	function GetArray(sArrayName)
	{
		for (var intLooper=0;intLooper <aVocab.length;intLooper++)
		{
			if (aVocab[intLooper][0]==sArrayName)
			{
				return aVocab[intLooper][1];
				break;
			}
		}
	}

	function ScanLine(sLine)
	{
		var iTagStart, iTagEnd
		var sKey

		if (sLine.indexOf("<") > -1)
		{
			iTagStart = sLine.indexOf("<");
			iTagEnd = sLine.indexOf(">");
			
			sKey = sLine.substr(iTagStart+1, iTagEnd-(iTagStart+1));
			
			if (sKey=="NAME")
			{
				sKey= GetPlanetName();
			}
			else
			{
				sKey = GetFrom(GetArray(sKey))
			}
			sLine = sLine.substr(0, iTagStart) + sKey + sLine.substr(iTagEnd+1, (sLine.length - iTagEnd))

		}
		

		if (sLine.indexOf("<") > - 1)
		{
			sLine = ScanLine(sLine)
		}

		return sLine;
	}


	function GenPlot()
	{
		sLine = GetFrom(GetArray("SEED"));

		sLine = ScanLine(sLine)

		document.getElementById('planet_name').value = sLine;
	}
