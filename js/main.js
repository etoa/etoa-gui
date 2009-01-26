
	if (!cnt)
	{
		var cnt = new Array();
	}
	function setCountdown(elem,time,diff,elemToSetEmpty)
	{
		var ts;
		
		d = diff - time;
		
		ts = diff - time - cnt[elem];
		if (ts>=0)
		{
			t = Math.floor(ts / 3600 / 24);
			h = Math.floor(ts / 3600  - (t*24));
			ts -= (t*24*3600);
			m = Math.floor((ts-(h*3600))/60);
			s = Math.floor((ts-(h*3600)-(m*60)));
			if (t>0)
				nv = t + "d " + h+"h "+m+"m "+s+"s";
			else if (h>0)
				nv = h+"h "+m+"m "+s+"s";
			else
				nv = m+"m "+s+"s";

			perc = Math.ceil(cnt[elem] / d * 100);
		
			if (document.getElementById(elem))
			{
				document.getElementById(elem).innerHTML=nv;
			}
			cnt[elem] = cnt[elem] + 1;
			setTimeout("setCountdown('"+elem+"',"+time+","+diff+",'"+elemToSetEmpty+"')",1000);
		}
		else
		{
			nv = "-";
			document.getElementById(elem).firstChild.nodeValue=nv;
			document.getElementById(elemToSetEmpty).innerHTML="";
		}
	}
	

	function updateProgressBar(progresselem,startTime,endTime,cTime,length)
	{
		if (progresselem)
		{
			diff = endTime - startTime;
			if (endTime>=cTime)
			{
				perc = Math.ceil((cTime-startTime) / diff * 100);
				cTime++;
				
				document.getElementById(progresselem).innerHTML=perc+"%";
				document.getElementById(progresselem).style.background="#fff url('images/progressbar.png') no-repeat";
				document.getElementById(progresselem).style.backgroundPosition=(-650+(perc*length/100))+"px 0px";
				//document.getElementById(progresselem).style.backgroundPosition=(-500+(perc*5))+"px 0px";
				if (perc<=48)
					document.getElementById(progresselem).style.color="#000";
				else
					document.getElementById(progresselem).style.color="#fff";

				setTimeout("updateProgressBar('"+progresselem+"',"+startTime+","+endTime+",'"+cTime+"','"+length+"')",1000);
			}
			else
			{
				document.getElementById(progresselem).innerHTML="Abgeschlossen!";
				document.getElementById(progresselem).style.background="url('images/progressbar.png') no-repeat";
				document.getElementById(progresselem).style.backgroundPosition="0px 0px";
				document.getElementById(progresselem).style.color="#fff";
			}
		}
	}	
	
		
	/**
	* Dynamische Zeitangabe
	*
	* @param time int Gibt die Restzeit oder den Timestamp an
	* @param target string Gibt die Ziel-ID an
	* @param format int 0=Counter, 1=Uhr
	* @param text string Ein optionaler Text kann eingebunden werden -> "Es geht noch TIME bis zum Ende"
	*/
	function time(time, target, format, text)
	{
		if (!text)
			var text = "";
		
		// Countdown
		if(format==0)
		{
			// Wandelt Restzeit in Stunden, Minuten und Sekunden um
			var d = Math.floor(time / 3600 / 24);
			var h = Math.floor((time - d*3600*24) / 3600);
			var m = Math.floor((time - d*3600*24 - h*3600) / 60);
			var s = Math.floor(time - d*3600*24 - h*3600 - m*60);		
			
			// Gibt Zeitstring an
			if(time>=0)
			{
				// Gibt Tage aus, sofern die Zeit mehrere Tage geht
				if(d > 0)
				{
					var time_string = d+"d "+h+"h "+m+"m "+s+"s";
				}
				else
				{
					var time_string = h+"h "+m+"m "+s+"s";
				}
			}
			else
			{
				var time_string = "Fertig";
			}
	
			// Text wird eingebunden
			if(text!="")
			{
				// Ersetzt alle "TIME" im Text durch den Counter
				var out = text.replace(/TIME/g, time_string);
			}
			else
			{
				var out = time_string;
			}
	
			document.getElementById(target).innerHTML = out;	
			time = time - 1;
		}
		
		// Uhr
		else if(format==1)
		{
			// Wandelt Timestamp in Javascript Timestamp um (Milliskunden)
			var timestamp = time * 1000;

				

			
			// Setzt Datum
			clock = new Date(timestamp);
	
			// Wandelt Timestamp in Stunden, Minuten und Sekunden um
			var h = clock.getHours();
			var m = clock.getMinutes();
			var s = clock.getSeconds();
			
			// Gibt Zahlen formatiert aus -> 05 statt 5
			if(h>=0 && h<=9)
			{
				var h = "0"+h+"";
			}
			if(m>=0 && m<=9)
			{
				var m = "0"+m+"";
			}
			if(s>=0 && s<=9)
			{
				var s = "0"+s+"";
			}
			
			// Gibt Zeitstring an
			var time_string = h+":"+m+":"+s;

			// Text wird eingebunden
			if(text!="")
			{
				// Ersetzt alle "TIME" im Text durch den Counter
				var out = text.replace(/TIME/g, time_string);
			}
			else
			{
				var out = time_string;
			}

	
			document.getElementById(target).innerHTML = out;	
			time = time + 1;
		}
		
		setTimeout("time("+time+", '"+target+"', "+format+", '"+text+"')",1000);
	}	
		
		
		
		
	var cdarray = new Object();

	function detectChangeRegister(elem,keyname)
	{
		cdarray[keyname] = elem.value;
	}
	
	function detectChangeTest(elem,keyname)
	{
		if (cdarray[keyname] != elem.value)
		{
			cdarray[keyname] = elem.value;
			return true;
		}
		return false;
	}

	function loadMsg(elemId)
	{
		document.getElementById(elemId).innerHTML = '<img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...';
	}
	
	function loadingMsg(trg,msg)
	{
		document.getElementById(trg).innerHTML='<div class=\"loadingMsg\">'+msg+'</div>';
	}

	function loadingMsgPrepend(trg,msg)
	{
		document.getElementById(trg).innerHTML='<div class=\"loadingMsg\">'+msg+'</div>'+document.getElementById(trg).innerHTML;
	}


	function showLoader(elem)
	{
		document.getElementById(elem).innerHTML='<div style=\"text-align:center;padding:10px;\"><img src="images/loading.gif" /></div>';
	}

	function showLoaderPrepend(elem)
	{
		document.getElementById(elem).innerHTML='<div style=\"text-align:center;padding:10px;\"><img src="images/loading.gif" /></div>'+document.getElementById(elem).innerHTML;
	}

	function showLoaderInline(elem)
	{
		document.getElementById(elem).innerHTML='<span style=\"text-align:center;padding:10px;\"><img src="images/loading.gif" /></span>';
	}
	function toggleBox(elemId)
	{
		if (document.getElementById(elemId).style.display=='none')
		{
			document.getElementById(elemId).style.display='';	
		}
		else
		{
			document.getElementById(elemId).style.display='none';					
		}		
	}


function changeNav(selIndex,page)
{
	obj = document.getElementById('nav_mode_select');
	document.location='?page='+page+'&planet_id='+obj.options[obj.selectedIndex].value;
}

function servertimeclock()
{
	if (s==60)
	{
		s=0;
		if (m==60)
		{
			m=0;
			if (h==23)
			{
				h=0;
				s=0;
				ho=h;
				mo=m;
				so=s;
			}
			else
			{
				h = h + 1;
				s=0
				ho=h;
				mo=m;
				so=s;
			}
		}
		else
		{
			m = m + 1;
			ho=h;
			mo=m;
			so=s;
			s = s + 1;
		}
	}
	else
	{
		ho=h;
		mo=m;
		so=s;
		s = s + 1;
	}
	nv = "";
	if (ho<10) nv += "0"+ho; else nv += ho;
	nv+=":";
	if (mo<10) nv += "0"+mo; else nv += mo;
	nv+=":";
	if (so<10) nv += "0"+so; else nv += so;
	document.getElementById('timefield').firstChild.nodeValue="Serverzeit: "+nv;
	setTimeout("servertimeclock()",1000);
}

function confirmCancelBuild(typ,elem)
{
	if (elem.firstChild.nodeValue == "Abbrechen")
	{
		if (typ==1)
			return confirm('Willst du den Bau dieses Gebäudes wirklich abbrechen?');
		else
			return confirm('Willst du den Abbruch dieses Gebäudes wirklich stoppen?');
	}
}

function nurZahlen(ereignis)
{
	if(document.all)
	{
		Buchstabe=ereignis.keyCode;
	}
	else
	{
		Buchstabe=ereignis.which
	}

	if((Buchstabe<48 || Buchstabe>57)&&Buchstabe!=8 &&Buchstabe!=0)
	{
		return false;
	}
	else
	{
		return true;
	}
}


function fulload(flength)
{
	f=document.forms[0];
	rval = '';
	for (x=0;x<flength;x++)
	{
		rval = rval + '9';
	}
	f.fleet_res_metal.value=rval;
	f.fleet_res_crystal.value=rval;
	f.fleet_res_plastic.value=rval;
	f.fleet_res_fuel.value=rval;
	f.fleet_res_food.value=rval;
}


function coordHighlight(x,y,action)
{
	if (action)
	{
		document.getElementById('xcoords_'+x).style.backgroundColor='#666';
		document.getElementById('ycoords_'+y).style.backgroundColor='#666';
	}
	else
	{
		document.getElementById('xcoords_'+x).style.backgroundColor='';
		document.getElementById('ycoords_'+y).style.backgroundColor='';
	}
}


function removeDefVal(obj,defVal)
{
	if (obj.value==defVal) obj.value='';
}

function setDefVal(obj,defVal)
{
	if (obj.value=='') obj.value=defVal;
}


/***************************************************
 *                                                 *
 * (c) copyright 2006 by timopaul                  *
 * timopaul.com                                    *
 *                                                 *
 ***************************************************
 *                                                 *
 * eine kleine funktion, welche innerhalb eines    *
 * input-field an jeder dritten stelle, von        *
 * hinten gezaehlt, einen kleinen tick (´) setzt   *
 * nicht gedacht fuer zeihenketten, sondern fuer   *
 * zahlen um jeden tausender abzugrenzen.          *
 *                                                 *
 * TODO ::                                         *
 * <input type="text" onkeyup="setTick(this); " /> *
 *                                                 *
 ***************************************************/


function setTick(obj) {

  shizzle = '';
  tick = '.';
  tickless = sliceString(tick, obj.value);
	length = tickless.length;
	a = new Array();

	for (i = 0; i < length; i++)
		a.unshift(tickless[i]);

  for (i = 0; i < a.length; i++) {
    if (i % 3 == 0)
      shizzle = tick + shizzle;
    shizzle = a[i] + shizzle;
  }
	obj.value = shizzle.substring(0, shizzle.length - 1);
}


function sliceString(string, value) {

	erg = '';
	for (i = 0; i < value.length; i++) {
		if (value.charAt(i) != string) {
			erg += value.charAt(i);
    }
  }

  return erg;
}


/*
 Pleas leave this notice.
 DHTML tip message version 1.5.4 copyright Essam Gamal 2003
 Home Page: (http://migoicons.tripod.com)
 Email: (migoicons@hotmail.com)
 Updated on :7/30/2003
*/
/*
var MI_IE=MI_IE4=MI_NN4=MI_ONN=MI_NN=MI_pSub=MI_sNav=0;mig_dNav()
var Style=[],Text=[],Count=0,move=0,fl=0,isOK=1,hs,e_d,tb,w=window,PX=(MI_pSub)?"px":""
var d_r=(MI_IE&&document.compatMode=="CSS1Compat")? "document.documentElement":"document.body"
var ww=w.innerWidth
var wh=w.innerHeight
var sbw=MI_ONN? 15:0

function mig_hand(){
if(MI_sNav){
w.onresize=mig_re
document.onmousemove=mig_mo
if(MI_NN4) document.captureEvents(Event.MOUSEMOVE)
}}

function mig_dNav(){
var ua=navigator.userAgent.toLowerCase()
MI_pSub=navigator.productSub
MI_OPR=ua.indexOf("opera")>-1?parseInt(ua.substring(ua.indexOf("opera")+6,ua.length)):0
MI_IE=document.all&&!MI_OPR?parseFloat(ua.substring(ua.indexOf("msie")+5,ua.length)):0
MI_IE4=parseInt(MI_IE)==4
MI_NN4=navigator.appName.toLowerCase()=="netscape"&&!document.getElementById
MI_NN=MI_NN4||document.getElementById&&!document.all
MI_ONN=MI_NN4||MI_pSub<20020823
MI_sNav=MI_NN||MI_IE||MI_OPR>=7
}

function mig_cssf(){
if(MI_IE>=5.5&&FiltersEnabled){fl=1
var d=" progid:DXImageTransform.Microsoft."
mig_layCss().filter="revealTrans()"+d+"Fade(Overlap=1.00 enabled=0)"+d+"Inset(enabled=0)"+d+"Iris(irisstyle=PLUS,motion=in enabled=0)"+d+"Iris(irisstyle=PLUS,motion=out enabled=0)"+d+"Iris(irisstyle=DIAMOND,motion=in enabled=0)"+d+"Iris(irisstyle=DIAMOND,motion=out enabled=0)"+d+"Iris(irisstyle=CROSS,motion=in enabled=0)"+d+"Iris(irisstyle=CROSS,motion=out enabled=0)"+d+"Iris(irisstyle=STAR,motion=in enabled=0)"+d+"Iris(irisstyle=STAR,motion=out enabled=0)"+d+"RadialWipe(wipestyle=CLOCK enabled=0)"+d+"RadialWipe(wipestyle=WEDGE enabled=0)"+d+"RadialWipe(wipestyle=RADIAL enabled=0)"+d+"Pixelate(MaxSquare=35,enabled=0)"+d+"Slide(slidestyle=HIDE,Bands=25 enabled=0)"+d+"Slide(slidestyle=PUSH,Bands=25 enabled=0)"+d+"Slide(slidestyle=SWAP,Bands=25 enabled=0)"+d+"Spiral(GridSizeX=16,GridSizeY=16 enabled=0)"+d+"Stretch(stretchstyle=HIDE enabled=0)"+d+"Stretch(stretchstyle=PUSH enabled=0)"+d+"Stretch(stretchstyle=SPIN enabled=0)"+d+"Wheel(spokes=16 enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=0,motion=forward enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=0,motion=reverse enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=1,motion=forward enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=1,motion=reverse enabled=0)"+d+"Zigzag(GridSizeX=8,GridSizeY=8 enabled=0)"+d+"Alpha(enabled=0)"+d+"Dropshadow(OffX=3,OffY=3,Positive=true,enabled=0)"+d+"Shadow(strength=3,direction=135,enabled=0)"
}}

function stm(t,s){
if(MI_sNav&&isOK){
if(document.onmousemove!=mig_mo||w.onresize!=mig_re) mig_hand()
if(fl&&s[17]>-1&&s[18]>0)mig_layCss().visibility="hidden"
var ab="";var ap=""
var titCol=s[0]?"COLOR='"+s[0]+"'":""
var titBgCol=s[1]&&!s[2]?"BGCOLOR='"+s[1]+"'":""
var titBgImg=s[2]?"BACKGROUND='"+s[2]+"'":""
var titTxtAli=s[3]?"ALIGN='"+s[3]+"'":""
var txtCol=s[6]?"COLOR='"+s[6]+"'":""
var txtBgCol=s[7]&&!s[8]?"BGCOLOR='"+s[7]+"'":""
var txtBgImg=s[8]?"BACKGROUND='"+s[8]+"'":""
var txtTxtAli=s[9]?"ALIGN='"+s[9]+"'":""
var tipHeight=s[13]? "HEIGHT='"+s[13]+"'":""
var brdCol=s[15]? "BGCOLOR='"+s[15]+"'":""
if(!s[4])s[4]="Verdana,Arial,Helvetica"
if(!s[5])s[5]=1
if(!s[10])s[10]="Verdana,Arial,Helvetica"
if(!s[11])s[11]=1
if(!s[12])s[12]=200
if(!s[14])s[14]=0
if(!s[16])s[16]=0
if(!s[24])s[24]=10
if(!s[25])s[25]=10
hs=s[22]
if(MI_pSub==20001108){
if(s[14])ab="STYLE='border:"+s[14]+"px solid"+" "+s[15]+"'";
ap="STYLE='padding:"+s[16]+"px "+s[16]+"px "+s[16]+"px "+s[16]+"px'"}
var closeLink=hs==3?"<TD ALIGN='right'><FONT SIZE='"+s[5]+"' FACE='"+s[4]+"'><A HREF='javascript:void(0)' ONCLICK='mig_hide(0)' STYLE='text-decoration:none;color:"+s[0]+"'><B>Close</B></A></FONT></TD>":""
var title=t[0]||hs==3?"<TABLE WIDTH='100%' BORDER='0' CELLPADDING='0' CELLSPACING='0' "+titBgCol+" "+titBgImg+"><TR><TD "+titTxtAli+"><FONT SIZE='"+s[5]+"' FACE='"+s[4]+"' "+titCol+"><B>"+t[0]+"</B></FONT></TD>"+closeLink+"</TR></TABLE>":"";
var txt="<TABLE "+ab+" WIDTH='"+s[12]+"' BORDER='0' CELLSPACING='0' CELLPADDING='"+s[14]+"' "+brdCol+"><TR><TD>"+title+"<TABLE WIDTH='100%' "+tipHeight+" BORDER='0' CELLPADDING='"+s[16]+"' CELLSPACING='0' "+txtBgCol+" "+txtBgImg+"><TR><TD "+txtTxtAli+" "+ap+" VALIGN='top'><FONT SIZE='"+s[11]+"' FACE='"+s[10]+"' "+txtCol +">"+t[1]+"</FONT></TD></TR></TABLE></TD></TR></TABLE>"
mig_wlay(txt)
tb={trans:s[17],dur:s[18],opac:s[19],st:s[20],sc:s[21],pos:s[23],xpos:s[24],ypos:s[25]}
if(MI_IE4)mig_layCss().width=s[12]
e_d=mig_ed()
Count=0
move=1
}}

function mig_mo(e){
if(move){
var X=0,Y=0,s_d=mig_scd(),w_d=mig_wd()
var mx=MI_NN?e.pageX:MI_IE4?event.x:event.x+s_d[0]
var my=MI_NN?e.pageY:MI_IE4?event.y:event.y+s_d[1]
if(MI_IE4)e_d=mig_ed()
switch(tb.pos){
case 1:X=mx-e_d[0]-tb.xpos+6;Y=my+tb.ypos;break
case 2:X=mx-(e_d[0]/2);Y=my+tb.ypos;break
case 3:X=tb.xpos+s_d[0];Y=tb.ypos+s_d[1];break
case 4:X=tb.xpos;Y=tb.ypos;break
default:X=mx+tb.xpos;Y=my+tb.ypos}
if(w_d[0]+s_d[0]<e_d[0]+X+sbw)X=w_d[0]+s_d[0]-e_d[0]-sbw
if(w_d[1]+s_d[1]<e_d[1]+Y+sbw){if(tb.pos>2)Y=w_d[1]+s_d[1]-e_d[1]-sbw;else Y=my-e_d[1]}
if(X<s_d[0])X=s_d[0]
with(mig_layCss()){left=X+PX;top=Y+PX}
mig_dis()
}}

function mig_dis(){Count++
if(Count==1){
if(fl){
if(tb.trans==51)tb.trans=parseInt(Math.random()*50)
var at=tb.trans>-1&&tb.trans<24&&tb.dur>0
var af=tb.trans>23&&tb.trans<51&&tb.dur>0
var t=mig_lay().filters[af?tb.trans-23:0]
for(var p=28;p<31;p++){mig_lay().filters[p].enabled=0}
for(var s=0;s<28;s++){if(mig_lay().filters[s].status)mig_lay().filters[s].stop()}
for(var e=1;e<3;e++){if(tb.sc&&tb.st==e){with(mig_lay().filters[28+e]){enabled=1;color=tb.sc}}}
if(tb.opac>0&&tb.opac<100){with(mig_lay().filters[28]){enabled=1;opacity=tb.opac}}
if(at||af){if(at)mig_lay().filters[0].transition=tb.trans;t.duration=tb.dur;t.apply()}}
mig_layCss().visibility=MI_NN4?"show":"visible"
if(fl&&(at||af))t.play()
if(hs>0&&hs<4)move=0
}}

function mig_layCss(){return MI_NN4?mig_lay():mig_lay().style}
function mig_lay(){with(document)return MI_NN4?layers[TipId]:MI_IE4?all[TipId]:getElementById(TipId)}
function mig_wlay(txt){if(MI_NN4){with(mig_lay().document){open();write(txt);close()}}else mig_lay().innerHTML=txt}
function mig_hide(C){if(!MI_NN4||MI_NN4&&C)mig_wlay("");with(mig_layCss()){visibility=MI_NN4?"hide":"hidden";left=0;top=0}}
function mig_scd(){return [parseInt(MI_IE?eval(d_r).scrollLeft:w.pageXOffset),parseInt(MI_IE?eval(d_r).scrollTop:w.pageYOffset)]}
function mig_re(){var w_d=mig_wd();if(MI_NN4&&(w_d[0]-ww||w_d[1]-wh))location.reload();else if(hs==3||hs==2) mig_hide(1)}
function mig_wd(){return [parseInt(MI_ONN?w.innerWidth:eval(d_r).clientWidth),parseInt(MI_ONN?w.innerHeight:eval(d_r).clientHeight)]}
function mig_ed(){return [parseInt(MI_NN4?mig_lay().clip.width:mig_lay().offsetWidth)+3,parseInt(MI_NN4?mig_lay().clip.height:mig_lay().offsetHeight)+5]}
function htm(){if(MI_sNav&&isOK){if(hs!=4){move=0;if(hs!=3&&hs!=2){mig_hide(1)}}}}

function mig_clay(){
if(!mig_lay()){isOK=0
alert("DHTML TIP MESSAGE VERSION 1.5 ERROR NOTICE.\n<DIV ID=\""+TipId+"\"></DIV> tag missing or its ID has been altered")}
else{mig_hand();mig_cssf()}}
*/

//
//Fügt per Klick BB-Codes ein bbcode(arg1=formular ort, arg2=Kürzel, arg3=text)
//

var bbtags   = new Array();


tag_prompt = "Geben Sie einen Text ein:";
img_prompt = "Bitte geben Sie die volle Bildadresse ein:";
font_formatter_prompt = "Geben Sie einen Text ein - ";
link_text_prompt = "Geben Sie einen Linknamen ein (optional):";
link_url_prompt = "Geben Sie die volle Adresse des Links ein:";
link_email_prompt = "Geben Sie eine E-Mail-Adresse ein:";
list_type_prompt = "Was für eine Liste möchten Sie? Geben Sie '1' ein für eine nummerierte Liste, 'a' für ein alphabetische, oder gar nichts für eine einfache Punktliste.";
list_item_prompt = "Geben Sie einen Listenpunkt ein.\nGeben Sie nichts ein oder drücken 'Abbrechen' um die Liste fertigzustellen.";


// browser detection
var myAgent   = navigator.userAgent.toLowerCase();
var myVersion = parseInt(navigator.appVersion);
var is_ie   = ((myAgent.indexOf("msie") != -1)  && (myAgent.indexOf("opera") == -1));
var is_win   =  ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));

function setmode(modeValue) {
 	document.cookie = "bbcodemode="+modeValue+"; path=/; expires=Wed, 1 Jan 2020 00:00:00 GMT;";
}

function normalMode(theForm) {
		return true;
}

function getArraySize(theArray) {
 	for (i = 0; i < theArray.length; i++) {
  		if ((theArray[i] == "undefined") || (theArray[i] == "") || (theArray[i] == null)) return i;
	}

 	return theArray.length;
}

function pushArray(theArray, value) {
 	theArraySize = getArraySize(theArray);
 	theArray[theArraySize] = value;
}

function popArray(theArray) {
	theArraySize = getArraySize(theArray);
 	retVal = theArray[theArraySize - 1];
 	delete theArray[theArraySize - 1];
 	return retVal;
}


function smilie(theSmilie) {
	addText(" " + theSmilie, "", false, document.bbform);
}

function closetag(theForm) {
 	if (!normalMode(theForm)) {
  		if (bbtags[0]) addText("[/"+ popArray(bbtags) +"]", "", false, theForm);
  	}

 	setFocus(theForm);
}

function closeall(theForm) {
 	if (!normalMode(theForm)) {
  		if (bbtags[0]) {
   			while (bbtags[0]) {
    				addText("[/"+ popArray(bbtags) +"]", "", false, theForm);
   			}
   		}
 	}

 	setFocus(theForm);
}


function fontformat(theForm,theValue,theType) {
 	setFocus(theForm);

 	if (normalMode(theForm)) {
  		if (theValue != 0) {

   			var selectedText = getSelectedText(theForm);
   			var insertText = prompt(font_formatter_prompt+" "+theType, selectedText);
   			if ((insertText != null) && (insertText != "")) {
    				addText("["+theType+"="+theValue+"]"+insertText+"[/"+theType+"]", "", false, theForm);
    			}
  		}
 	}
 	else {
		if(addText("["+theType+"="+theValue+"]", "[/"+theType+"]", true, theForm)) {
			pushArray(bbtags, theType);
		}
	}

 	theForm.sizeselect.selectedIndex = 0;
 	theForm.fontselect.selectedIndex = 0;
 	theForm.colorselect.selectedIndex = 0;

 	setFocus(theForm);
}


function bbcode(theForm, theTag, promptText) {
	if ( normalMode(theForm) || (theTag=="IMG")) {
		var selectedText = getSelectedText(theForm);
		if (promptText == '' || selectedText != '') promptText = selectedText;

		inserttext = prompt(((theTag == "IMG") ? (img_prompt) : (tag_prompt)) + "\n[" + theTag + "]xxx[/" + theTag + "]", promptText);
		if ( (inserttext != null) && (inserttext != "") ) {
			addText("[" + theTag + "]" + inserttext + "[/" + theTag + "]", "", false, theForm);
		}
	}
	else {
		var donotinsert = false;
  		for (i = 0; i < bbtags.length; i++) {
   			if (bbtags[i] == theTag) donotinsert = true;
  		}

  		if (!donotinsert) {
   			if(addText("[" + theTag + "]", "[/" + theTag + "]", true, theForm)){
				pushArray(bbtags, theTag);
			}
  		}
		else {
			var lastindex = 0;

			for (i = 0 ; i < bbtags.length; i++ ) {
				if ( bbtags[i] == theTag ) {
					lastindex = i;
				}
			}

			while (bbtags[lastindex]) {
				tagRemove = popArray(bbtags);
				addText("[/" + tagRemove + "]", "", false, theForm);
			}
		}
	}
}

function namedlink(theForm,theType) {
	var selected = getSelectedText(theForm);

	var linkText = prompt(link_text_prompt,selected);
	var prompttext;

	if (theType == 'url') {
 		prompt_text = link_url_prompt;
 		prompt_contents = "http://";
	}
	else {
		prompt_text = link_email_prompt;
		prompt_contents = "";
		}

	linkURL = prompt(prompt_text,prompt_contents);


	if ((linkURL != null) && (linkURL != "")) {
		var theText = '';

		if ((linkText != null) && (linkText != "")) {
   			theText = "["+theType+"="+linkURL+"]"+linkText+"[/"+theType+"]";
   		}
		else {
			theText = "["+theType+"]"+linkURL+"[/"+theType+"]";
		}

  		addText(theText, "", false, theForm);
 	}
}


function dolist(theForm) {
 	listType = prompt(list_type_prompt, "");
 	if ((listType == "a") || (listType == "1")) {
  		theList = "[list="+listType+"]\n";
  		listEend = "[/list="+listType+"] ";
 	}
 	else {
  		theList = "[list]\n";
  		listEend = "[/list] ";
 	}

 	listEntry = "initial";
 	while ((listEntry != "") && (listEntry != null)) {
  		listEntry = prompt(list_item_prompt, "");
  		if ((listEntry != "") && (listEntry != null)) theList = theList+"[*]"+listEntry+"\n";
 	}

 	addText(theList + listEend, "", false, theForm);
}


function addText(theTag, theClsTag, isSingle, theForm)
{
	var isClose = false;
	var message = theForm.message;
	var set=false;
  	var old=false;
  	var selected="";

  	if(message.textLength>=0 ) { // mozilla, firebird, netscape
  		if(theClsTag!="" && message.selectionStart!=message.selectionEnd) {
  			selected=message.value.substring(message.selectionStart,message.selectionEnd);
  			str=theTag + selected+ theClsTag;
  			old=true;
  			isClose = true;
  		}
		else {
			str=theTag;
		}

		message.focus();
		start=message.selectionStart;
		end=message.textLength;
		endtext=message.value.substring(message.selectionEnd,end);
		starttext=message.value.substring(0,start);
		message.value=starttext + str + endtext;
		message.selectionStart=start;
		message.selectionEnd=start;

		message.selectionStart = message.selectionStart + str.length;

		if(old) { return false; }

		set=true;

		if(isSingle) {
			isClose = false;
		}
	}
	if ( (myVersion >= 4) && is_ie && is_win) {  // Internet Explorer
		if(message.isTextEdit) {
			message.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null){
				if(theClsTag != "" && rng.text.length > 0)
					theTag += rng.text + theClsTag;
				else if(isSingle)
					isClose = true;

				rng.text = theTag;
			}
		}
		else{
			if(isSingle) isClose = true;

			if(!set) {
      				message.value += theTag;
      			}
		}
	}
	else
	{
		if(isSingle) isClose = true;

		if(!set) {
      			message.value += theTag;
      		}
	}

	message.focus();

	return isClose;
}


function getSelectedText(theForm) {
	var message = theForm.message;
	var selected = '';

	if(navigator.appName=="Netscape" &&  message.textLength>=0 && message.selectionStart!=message.selectionEnd )
  		selected=message.value.substring(message.selectionStart,message.selectionEnd);

	else if( (myVersion >= 4) && is_ie && is_win ) {
		if(message.isTextEdit){
			message.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;

			if((sel.type == "Text" || sel.type == "None") && rng != null){
				if(rng.text.length > 0) selected = rng.text;
			}
		}
	}

  	return selected;
}

function setFocus(theForm) {
 	theForm.message.focus();
}



function FormatNumber(id, num, max, decpoint, sep) 
{
	// Macht aus der Zahl einen String (sonst funktioniert replace nicht richtig)
	var num = num.toString();
	
	// Löscht Trennzeichen aus der Zahl
	var num = num.replace(/`/g, "");
	
	// Prüft, ob Wert eine Zahl ist
	if(istZahl(num)==false)
	{
		var num = 0;
	}
	
	// Erstellt den Absolut Wert der Zahl
	var num = Math.abs(num);
	
  // check for missing parameters and use defaults if so 
  if (decpoint == '') {
    decpoint = ".";
  }
  if (sep == '') {
    sep = "`";
  } 
  
  if(max != '')
  {
  	var num = Math.min(max,num);
  }
   
	// Rundet die Zahl ab
	var num = Math.floor(num);   
   
   
  // need a string for operations
  num = num.toString();
  // separate the whole number and the fraction if possible
  a = num.split(decpoint);
  x = a[0]; // decimal
  y = a[1]; // fraction
  z = "";


  if (typeof(x) != "undefined") 
  {
    // reverse the digits. regexp works from left to right.
    for (i=x.length-1;i>=0;i--)
    {
      z += x.charAt(i);
    }
    
    // add seperators. but undo the trailing one, if there
    z = z.replace(/(\d{3})/g, "$1" + sep);
    if (z.slice(-sep.length) == sep)
    {
      z = z.slice(0, -sep.length);
    }
    x = "";
    
    // reverse again to get back the number
    for (i=z.length-1;i>=0;i--)
    {
      x += z.charAt(i);
    }
    // add the fraction back in, if it was there
    if (typeof(y) != "undefined" && y.length > 0)
    {
      x += decpoint + y;
    }
  }
  
  if (id!='return')
  {
  	document.getElementById(id).value=x;
  }
  else
  {
  	return x;
  }
}


function istZahl(field)
{
   var Wert=true;
   points=0;
   for(i=0;i<field.length;i++)
   {
	   if(field.charAt(i) < "0"|| field.charAt(i) > "9")
	   {
        Wert=false;
	   }
   }
   return Wert;
}


// Such nach unerlaubten Zeichen in einem Text und gibt TRUE bei einem Treffer zurück
function check_illegal_signs(str)
{
	var result = false;
	
	// Stellt sicher, dass die Variable vom Typ String ist
	var str = str.toString();
		
	if(str.search(/\'/) != -1
		|| str.search(/</) != -1
		|| str.search(/>/) != -1
		|| str.search(/\?/) != -1
		|| str.search(/\"/) != -1
		|| str.search(/\$/) != -1
		|| str.search(/\!/) != -1
		|| str.search(/=/) != -1
		|| str.search(/\;/) != -1
		|| str.search(/\&/) != -1
		)
	{
		var result = true;
	}
	
	return result;
}


function tabActivate(tabName,elemId)
{
	i = 0;
	while (true)
	{
		if (document.getElementById(tabName+"Content"+i))
		{
			if (i==elemId)
			{
				document.getElementById(tabName+"Content"+i).style.display='';
				document.getElementById(tabName+"Nav"+i).setAttribute("class", "tabTabActive");
				document.getElementById(tabName+"Nav"+i).setAttribute("className", "tabTabActive");
			}
			else
			{
				document.getElementById(tabName+"Content"+i).style.display='none';
				document.getElementById(tabName+"Nav"+i).setAttribute("class", "tabTab");
				document.getElementById(tabName+"Nav"+i).setAttribute("className", "tabTab");
			}
			i++;
		}
		else
		{
			break;
		}
	}
	
}