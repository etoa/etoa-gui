
if (!cnt) {
  var cnt = new Array();
}
function setCountdown(elem, time, diff, elemToSetEmpty) {
  var ts;

  d = diff - time;

  ts = diff - time - cnt[elem];
  if (ts >= 0) {
    t = Math.floor(ts / 3600 / 24);
    h = Math.floor(ts / 3600 - (t * 24));
    ts -= (t * 24 * 3600);
    m = Math.floor((ts - (h * 3600)) / 60);
    s = Math.floor((ts - (h * 3600) - (m * 60)));
    if (t > 0)
      nv = t + "d " + h + "h " + m + "m " + s + "s";
    else if (h > 0)
      nv = h + "h " + m + "m " + s + "s";
    else
      nv = m + "m " + s + "s";

    perc = Math.ceil(cnt[elem] / d * 100);

    if (document.getElementById(elem)) {
      document.getElementById(elem).innerHTML = nv;
    }
    cnt[elem] = cnt[elem] + 1;
    setTimeout("setCountdown('" + elem + "'," + time + "," + diff + ",'" + elemToSetEmpty + "')", 1000);
  }
  else {
    nv = "-";
    document.getElementById(elem).firstChild.nodeValue = nv;
    document.getElementById(elemToSetEmpty).innerHTML = "";
  }
}


function updateProgressBar(progresselem, startTime, endTime, cTime) {
  if (progresselem) {
    diff = endTime - startTime;
    if (endTime >= cTime) {
      perc = Math.ceil((cTime - startTime) / diff * 100);
      cTime++;

      $(progresselem).text(perc + "%");

      setTimeout(function () {
        updateProgressBar(progresselem, startTime, endTime, cTime);
      }, 1000);
    }
    else {
      $(progresselem).html("Abgeschlossen!");
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
function time(time, target, format, text) {
  if (!text)
    var text = "";

  // Countdown
  if (format == 0) {
    // Wandelt Restzeit in Stunden, Minuten und Sekunden um
    var d = Math.floor(time / 3600 / 24);
    var h = Math.floor((time - d * 3600 * 24) / 3600);
    var m = Math.floor((time - d * 3600 * 24 - h * 3600) / 60);
    var s = Math.floor(time - d * 3600 * 24 - h * 3600 - m * 60);

    // Gibt Zeitstring an
    if (time >= 0) {
      // Gibt Tage aus, sofern die Zeit mehrere Tage geht
      if (d > 0) {
        var time_string = d + "d " + h + "h " + m + "m " + s + "s";
      }
      else {
        var time_string = h + "h " + m + "m " + s + "s";
      }
    }
    else {
      var time_string = "Fertig";
    }

    // Text wird eingebunden
    if (text != "") {
      // Ersetzt alle "TIME" im Text durch den Counter
      var out = text.replace(/TIME/g, time_string);
    }
    else {
      var out = time_string;
    }

    $('#' + target).html(out);
    time = time - 1;
  }

  // Uhr
  else if (format == 1) {
    // Wandelt Timestamp in Javascript Timestamp um (Milliskunden)
    var timestamp = time * 1000;




    // Setzt Datum
    clock = new Date(timestamp);

    // Wandelt Timestamp in Stunden, Minuten und Sekunden um
    var h = clock.getHours();
    var m = clock.getMinutes();
    var s = clock.getSeconds();

    // Gibt Zahlen formatiert aus -> 05 statt 5
    if (h >= 0 && h <= 9) {
      var h = "0" + h + "";
    }
    if (m >= 0 && m <= 9) {
      var m = "0" + m + "";
    }
    if (s >= 0 && s <= 9) {
      var s = "0" + s + "";
    }

    // Gibt Zeitstring an
    var time_string = h + ":" + m + ":" + s;

    // Text wird eingebunden
    if (text != "") {
      // Ersetzt alle "TIME" im Text durch den Counter
      var out = text.replace(/TIME/g, time_string);
    }
    else {
      var out = time_string;
    }


    $('#' + target).html(out);
    time = time + 1;
  }

  setTimeout("time(" + time + ", '" + target + "', " + format + ", '" + text + "')", 1000);
}




var cdarray = new Object();

function detectChangeRegister(elem, keyname) {
  cdarray[keyname] = elem.value;
}

function detectChangeTest(elem, keyname) {
  if (cdarray[keyname] != elem.value) {
    cdarray[keyname] = elem.value;
    return true;
  }
  return false;
}

function loadMsg(elemId) {
  document.getElementById(elemId).innerHTML = '<img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...';
}

function loadingMsg(trg, msg) {
  document.getElementById(trg).innerHTML = '<div class=\"loadingMsg\">' + msg + '</div>';
}

function loadingMsgPrepend(trg, msg) {
  document.getElementById(trg).innerHTML = '<div class=\"loadingMsg\">' + msg + '</div>' + document.getElementById(trg).innerHTML;
}


function showLoader(elem) {
  document.getElementById(elem).innerHTML = '<div style=\"text-align:center;padding:10px;\"><img src="images/loading.gif" /></div>';
}

function showLoaderPrepend(elem) {
  document.getElementById(elem).innerHTML = '<div style=\"text-align:center;padding:10px;\"><img src="images/loading.gif" /></div>' + document.getElementById(elem).innerHTML;
}

function showLoaderInline(elem) {
  document.getElementById(elem).innerHTML = '<span style=\"text-align:center;padding:10px;\"><img src="images/loading.gif" /></span>';
}

function toggleBox(elemId) {
  if (document.getElementById(elemId).style.display == 'none') {
    document.getElementById(elemId).style.display = '';
  }
  else {
    document.getElementById(elemId).style.display = 'none';
  }
}

function showElement(elemId, value) {
  if (typeof value == "boolean") {
    if (value)
      document.getElementById(elemId).style.display = '';
    else {
      document.getElementById(elemId).style.display = 'none';
    }
  }
}


function changeNav(selIndex, page) {
  obj = document.getElementById('nav_mode_select');
  document.location = '?page=' + page + '&change_entity=' + obj.options[obj.selectedIndex].value;
}

function servertimeclock() {
  if (s == 60) {
    s = 0;
    if (m == 60) {
      m = 0;
      if (h == 23) {
        h = 0;
        s = 0;
        ho = h;
        mo = m;
        so = s;
      }
      else {
        h = h + 1;
        s = 0
        ho = h;
        mo = m;
        so = s;
      }
    }
    else {
      m = m + 1;
      ho = h;
      mo = m;
      so = s;
      s = s + 1;
    }
  }
  else {
    ho = h;
    mo = m;
    so = s;
    s = s + 1;
  }
  nv = "";
  if (ho < 10) nv += "0" + ho; else nv += ho;
  nv += ":";
  if (mo < 10) nv += "0" + mo; else nv += mo;
  nv += ":";
  if (so < 10) nv += "0" + so; else nv += so;
  document.getElementById('timefield').firstChild.nodeValue = "Serverzeit: " + nv;
  setTimeout("servertimeclock()", 1000);
}

function confirmCancelBuild(typ, elem) {
  if (elem.firstChild.nodeValue == "Abbrechen") {
    if (typ == 1)
      return confirm('Willst du den Bau dieses Gebäudes wirklich abbrechen?');
    else
      return confirm('Willst du den Abbruch dieses Gebäudes wirklich stoppen?');
  }
}

function nurZahlen(ereignis) {
  if (document.all) {
    Buchstabe = ereignis.keyCode;
  }
  else {
    Buchstabe = ereignis.which
  }

  if ((Buchstabe < 48 || Buchstabe > 57) && Buchstabe != 8 && Buchstabe != 0) {
    return false;
  }
  else {
    return true;
  }
}


function fulload(flength) {
  f = document.forms[0];
  rval = '';
  for (x = 0; x < flength; x++) {
    rval = rval + '9';
  }
  f.fleet_res_metal.value = rval;
  f.fleet_res_crystal.value = rval;
  f.fleet_res_plastic.value = rval;
  f.fleet_res_fuel.value = rval;
  f.fleet_res_food.value = rval;
}


function coordHighlight(x, y, action) {
  if (action) {
    document.getElementById('xcoords_' + x).style.backgroundColor = '#666';
    document.getElementById('ycoords_' + y).style.backgroundColor = '#666';
  }
  else {
    document.getElementById('xcoords_' + x).style.backgroundColor = '';
    document.getElementById('ycoords_' + y).style.backgroundColor = '';
  }
}


function removeDefVal(obj, defVal) {
  if (obj.value == defVal) obj.value = '';
}

function setDefVal(obj, defVal) {
  if (obj.value == '') obj.value = defVal;
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

//
//Fügt per Klick BB-Codes ein bbcode(arg1=formular ort, arg2=Kürzel, arg3=text)
//

var bbtags = new Array();


tag_prompt = "Geben Sie einen Text ein:";
img_prompt = "Bitte geben Sie die volle Bildadresse ein:";
font_formatter_prompt = "Geben Sie einen Text ein - ";
link_text_prompt = "Geben Sie einen Linknamen ein (optional):";
link_url_prompt = "Geben Sie die volle Adresse des Links ein:";
link_email_prompt = "Geben Sie eine E-Mail-Adresse ein:";
list_type_prompt = "Was für eine Liste möchten Sie? Geben Sie '1' ein für eine nummerierte Liste, 'a' für ein alphabetische, oder gar nichts für eine einfache Punktliste.";
list_item_prompt = "Geben Sie einen Listenpunkt ein.\nGeben Sie nichts ein oder drücken 'Abbrechen' um die Liste fertigzustellen.";


// browser detection
var myAgent = navigator.userAgent.toLowerCase();
var myVersion = parseInt(navigator.appVersion);
var is_ie = ((myAgent.indexOf("msie") != -1) && (myAgent.indexOf("opera") == -1));
var is_win = ((myAgent.indexOf("win") != -1) || (myAgent.indexOf("16bit") != -1));

function setmode(modeValue) {
  document.cookie = "bbcodemode=" + modeValue + "; path=/; expires=Wed, 1 Jan 2020 00:00:00 GMT;";
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
    if (bbtags[0]) addText("[/" + popArray(bbtags) + "]", "", false, theForm);
  }

  setFocus(theForm);
}

function closeall(theForm) {
  if (!normalMode(theForm)) {
    if (bbtags[0]) {
      while (bbtags[0]) {
        addText("[/" + popArray(bbtags) + "]", "", false, theForm);
      }
    }
  }

  setFocus(theForm);
}


function fontformat(theForm, theValue, theType) {
  setFocus(theForm);

  if (normalMode(theForm)) {
    if (theValue != 0) {

      var selectedText = getSelectedText(theForm);
      var insertText = prompt(font_formatter_prompt + " " + theType, selectedText);
      if ((insertText != null) && (insertText != "")) {
        addText("[" + theType + "=" + theValue + "]" + insertText + "[/" + theType + "]", "", false, theForm);
      }
    }
  }
  else {
    if (addText("[" + theType + "=" + theValue + "]", "[/" + theType + "]", true, theForm)) {
      pushArray(bbtags, theType);
    }
  }

  theForm.sizeselect.selectedIndex = 0;
  theForm.fontselect.selectedIndex = 0;
  theForm.colorselect.selectedIndex = 0;

  setFocus(theForm);
}


function bbcode(theForm, theTag, promptText) {
  if (normalMode(theForm) || (theTag == "IMG")) {
    var selectedText = getSelectedText(theForm);
    if (promptText == '' || selectedText != '') promptText = selectedText;

    inserttext = prompt(((theTag == "IMG") ? (img_prompt) : (tag_prompt)) + "\n[" + theTag + "]xxx[/" + theTag + "]", promptText);
    if ((inserttext != null) && (inserttext != "")) {
      addText("[" + theTag + "]" + inserttext + "[/" + theTag + "]", "", false, theForm);
    }
  }
  else {
    var donotinsert = false;
    for (i = 0; i < bbtags.length; i++) {
      if (bbtags[i] == theTag) donotinsert = true;
    }

    if (!donotinsert) {
      if (addText("[" + theTag + "]", "[/" + theTag + "]", true, theForm)) {
        pushArray(bbtags, theTag);
      }
    }
    else {
      var lastindex = 0;

      for (i = 0; i < bbtags.length; i++) {
        if (bbtags[i] == theTag) {
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

function namedlink(theForm, theType) {
  var selected = getSelectedText(theForm);

  var linkText = prompt(link_text_prompt, selected);
  var prompttext;

  if (theType == 'url') {
    prompt_text = link_url_prompt;
    prompt_contents = "http://";
  }
  else {
    prompt_text = link_email_prompt;
    prompt_contents = "";
  }

  linkURL = prompt(prompt_text, prompt_contents);


  if ((linkURL != null) && (linkURL != "")) {
    var theText = '';

    if ((linkText != null) && (linkText != "")) {
      theText = "[" + theType + "=" + linkURL + "]" + linkText + "[/" + theType + "]";
    }
    else {
      theText = "[" + theType + "]" + linkURL + "[/" + theType + "]";
    }

    addText(theText, "", false, theForm);
  }
}


function dolist(theForm) {
  listType = prompt(list_type_prompt, "");
  if ((listType == "a") || (listType == "1")) {
    theList = "[list=" + listType + "]\n";
    listEend = "[/list=" + listType + "] ";
  }
  else {
    theList = "[list]\n";
    listEend = "[/list] ";
  }

  listEntry = "initial";
  while ((listEntry != "") && (listEntry != null)) {
    listEntry = prompt(list_item_prompt, "");
    if ((listEntry != "") && (listEntry != null)) theList = theList + "[*]" + listEntry + "\n";
  }

  addText(theList + listEend, "", false, theForm);
}


function addText(theTag, theClsTag, isSingle, theForm) {
  var isClose = false;
  var message = theForm.message;
  var set = false;
  var old = false;
  var selected = "";

  if (message.textLength >= 0) { // mozilla, firebird, netscape
    if (theClsTag != "" && message.selectionStart != message.selectionEnd) {
      selected = message.value.substring(message.selectionStart, message.selectionEnd);
      str = theTag + selected + theClsTag;
      old = true;
      isClose = true;
    }
    else {
      str = theTag;
    }

    message.focus();
    start = message.selectionStart;
    end = message.textLength;
    endtext = message.value.substring(message.selectionEnd, end);
    starttext = message.value.substring(0, start);
    message.value = starttext + str + endtext;
    message.selectionStart = start;
    message.selectionEnd = start;

    message.selectionStart = message.selectionStart + str.length;

    if (old) { return false; }

    set = true;

    if (isSingle) {
      isClose = false;
    }
  }
  if ((myVersion >= 4) && is_ie && is_win) {  // Internet Explorer
    if (message.isTextEdit) {
      message.focus();
      var sel = document.selection;
      var rng = sel.createRange();
      rng.colapse;
      if ((sel.type == "Text" || sel.type == "None") && rng != null) {
        if (theClsTag != "" && rng.text.length > 0)
          theTag += rng.text + theClsTag;
        else if (isSingle)
          isClose = true;

        rng.text = theTag;
      }
    }
    else {
      if (isSingle) isClose = true;

      if (!set) {
        message.value += theTag;
      }
    }
  }
  else {
    if (isSingle) isClose = true;

    if (!set) {
      message.value += theTag;
    }
  }

  message.focus();

  return isClose;
}


function getSelectedText(theForm) {
  var message = theForm.message;
  var selected = '';

  if (navigator.appName == "Netscape" && message.textLength >= 0 && message.selectionStart != message.selectionEnd)
    selected = message.value.substring(message.selectionStart, message.selectionEnd);

  else if ((myVersion >= 4) && is_ie && is_win) {
    if (message.isTextEdit) {
      message.focus();
      var sel = document.selection;
      var rng = sel.createRange();
      rng.colapse;

      if ((sel.type == "Text" || sel.type == "None") && rng != null) {
        if (rng.text.length > 0) selected = rng.text;
      }
    }
  }

  return selected;
}

function setFocus(theForm) {
  theForm.message.focus();
}

function FormatNumber(id, num, max, decpoint, sep) {
  // Macht aus der Zahl einen String (sonst funktioniert replace nicht richtig)
  var num = num.toString();

  // Löscht Trennzeichen aus der Zahl
  var num = num.replace(/`/g, "");

  // Prüft, ob Wert eine Zahl ist
  if (istZahl(num) == false) {
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

  if (max != '') {
    var num = Math.min(max, num);
  }

  // Rundet die Zahl ab
  var num = Math.floor(num);


  // need a string for operations
  num = num.toString();
  // separate the whole number and the fraction if possible
  var a = num.split(decpoint);
  var x = a[0]; // decimal
  var y = a[1]; // fraction
  var z = "";


  if (typeof (x) != "undefined") {

    // reverse the digits. regexp works from left to right.
    for (var i = x.length - 1; i >= 0; i--) {
      z += x.charAt(i);
    }

    // add seperators. but undo the trailing one, if there
    z = z.replace(/(\d{3})/g, "$1" + sep);
    if (z.slice(-sep.length) == sep) {
      z = z.slice(0, -sep.length);
    }

    x = "";
    // reverse again to get back the number
    for (var i = z.length - 1; i >= 0; i--) {
      x += z.charAt(i);
    }
    // add the fraction back in, if it was there
    if (typeof (y) != "undefined" && y.length > 0) {
      x += decpoint + y;
    }

    if (id != 'return') {
      document.getElementById(id).value = x;
    }
    else {
      return x;
    }
  } // if (typeof(x) != "undefined")
} // function FormatNumber(id, num, max, decpoint, sep)

function FormatSignedNumber(id, num, max, decpoint, sep) {
  // Macht aus der Zahl einen String (sonst funktioniert replace nicht richtig)
  var num = num.toString();

  if (num == '-' || num == '0-' || num == '-0') {
    num = '-';
    document.getElementById(id).value = num;
  }
  else {
    //}
    // Löscht Trennzeichen aus der Zahl
    var num = num.replace(/`/g, "");

    // Prüft, ob Wert eine Zahl ist
    if (istSignedZahl(num) == false) {
      var num = 0;
    }

    // Erstellt den Absolut Wert der Zahl
    // var num = Math.abs(num);

    // check for missing parameters and use defaults if so
    if (decpoint == '') {
      decpoint = ".";
    }
    if (sep == '') {
      sep = "`";
    }

    if (max != '') {
      var num = Math.min(max, num);
    }

    // Rundet die Zahl ab
    var num = Math.floor(num);


    // need a string for operations
    num = num.toString();
    // separate the whole number and the fraction if possible
    var a = num.split(decpoint);
    var x = a[0]; // decimal
    var y = a[1]; // fraction
    var z = "";


    if (typeof (x) != "undefined") {
      var sign = false;
      if (x.charAt(0) == "-") {
        // Loescht Vorzeichen aus der Zahl
        x = x.replace(/-/g, "");
        sign = true;
      }

      // reverse the digits. regexp works from left to right.
      for (var i = x.length - 1; i >= 0; i--) {
        z += x.charAt(i);
      }

      // add seperators. but undo the trailing one, if there
      z = z.replace(/(\d{3})/g, "$1" + sep);
      if (z.slice(-sep.length) == sep) {
        z = z.slice(0, -sep.length);
      }

      if (sign)
        x = "-";
      else
        x = "";

      // reverse again to get back the number
      for (var i = z.length - 1; i >= 0; i--) {
        x += z.charAt(i);
      }
      // add the fraction back in, if it was there
      if (typeof (y) != "undefined" && y.length > 0) {
        x += decpoint + y;
      }
    }

    if (id != 'return') {
      document.getElementById(id).value = x;
    }
    else {
      return x;
    }
  } // if (typeof(x) != "undefined")
} // function FormatSignedNumber(id, num, max, decpoint, sep)

function istZahl(field) {
  var Wert = true;
  var points = 0;
  for (var i = 0; i < field.length; i++) {
    if (field.charAt(i) < "0" || field.charAt(i) > "9") {
      Wert = false;
    }
  }
  return Wert;
}

function istSignedZahl(field) {
  var Wert = true;
  var points = 0;
  for (var i = 0; i < field.length; i++) {
    if ((field.charAt(0) >= "0" && field.charAt(0) <= "9") || field.charAt(0) == "-" || field.charAt(0) == "+") {
      if (i != 0) {
        if (field.charAt(i) < "0" || field.charAt(i) > "9") {
          Wert = false;
        }
        else {
        }
      }
      else {
      }
    }
    else {
      Wert = false;
    }
  }
  return Wert;
}

// Such nach unerlaubten Zeichen in einem Text und gibt TRUE bei einem Treffer zurück
function check_illegal_signs(str) {
  var result = false;

  // Stellt sicher, dass die Variable vom Typ String ist
  var str = str.toString();

  if (str.search(/\'/) != -1
    || str.search(/</) != -1
    || str.search(/>/) != -1
    || str.search(/\?/) != -1
    || str.search(/\"/) != -1
    || str.search(/\$/) != -1
    || str.search(/\!/) != -1
    || str.search(/=/) != -1
    || str.search(/\;/) != -1
    || str.search(/\&/) != -1
  ) {
    var result = true;
  }

  return result;
}


function tabActivate(tabName, elemId) {
  i = 0;
  while (true) {
    if (document.getElementById(tabName + "Content" + i)) {
      if (i == elemId) {
        document.getElementById(tabName + "Content" + i).style.display = '';
        document.getElementById(tabName + "Nav" + i).setAttribute("class", "tabTabActive");
        document.getElementById(tabName + "Nav" + i).setAttribute("className", "tabTabActive");
      }
      else {
        document.getElementById(tabName + "Content" + i).style.display = 'none';
        document.getElementById(tabName + "Nav" + i).setAttribute("class", "tabTab");
        document.getElementById(tabName + "Nav" + i).setAttribute("className", "tabTab");
      }
      i++;
    }
    else {
      break;
    }
  }

}

function addFontColor(id, colorId) {
  var color = document.getElementById(id).value;
  if (color.length == 4 || color.length == 7) {
    document.getElementById(colorId).style.color = color;
  }
  else {
    document.getElementById(colorId).style.color = "#FFF";
  }
}


/**
*	BB-Code Wrapper
*
* @param $string Text to wrap BB-Codes into HTML
* @return Wrapped text
*/
function BBCodeToHTML(text, target) {
  text = htmlEntities(text);
  text = text.replace(/\n/g, "<br/>");

  text = text.replace(/\[b\]/gi, "<b>");
  text = text.replace(/\[\/b\]/gi, "</b>");
  text = text.replace(/\[i\]/gi, "<i>");
  text = text.replace(/\[\/i\]/gi, "</i>");
  text = text.replace(/\[u\]/gi, "<u>");
  text = text.replace(/\[\/u\]/gi, "</u>");
  text = text.replace(/\[c\]/gi, "<div style='text-align:center;'>");
  text = text.replace(/\[\/c\]/gi, "</div>");
  text = text.replace(/\[bc\]/gi, "<blockquote class='blockquotecode'><code>");
  text = text.replace(/\[\/bc\]/gi, "</code></blockquote>");

  text = text.replace(/\[h1\]/gi, "<h1>");
  text = text.replace(/\[\/h1\]/gi, "</h1>");
  text = text.replace(/\[h2\]/gi, "<h2>");
  text = text.replace(/\[\/h2\]/gi, "</h2>");
  text = text.replace(/\[h3\]/gi, "<h3>");
  text = text.replace(/\[\/h3\]/gi, "</h3>");

  text = text.replace(/\[center\]/gi, "<div style='text-align:center'>");
  text = text.replace(/\[\/center\]/gi, "</div>");
  text = text.replace(/\[right\]/gi, "<div style='text-align:right'>");
  text = text.replace(/\[\/right\]/gi, "</div>");
  text = text.replace(/\[headline\]/gi, "<div style='text-align:center'><b>");
  text = text.replace(/\[\/headline\]/gi, "</b></div>");

  text = text.replace(/\[\*\]/gi, "<li>");
  text = text.replace(/\[\/\*\]/gi, "</li>");
  text = text.replace(/\[list\]/gi, "<ul>");
  text = text.replace(/\[\/list\]/gi, "</ul>");
  text = text.replace(/\[line\]/gi, "<hr class='line' />");

  // Links
  text = text.replace(/\[page=(.*?)\](.*?)\[\/page\]/gi, "<a href='?page=$1'>$2</a>");
  text = text.replace(/\[url=(.*?)\](.*?)\[\/URL\]/gi, "<a href='$1' target='_blank'>$2</a>");
  text = text.replace(/\[URL\](.*?)\[\/URL\]/gi, "<a href='$1' target='_blank'>$1</a>");
  text = text.replace(/\[email=(.*?)\](.*?)\[\/email\]/gi, "<a href='mailto:$1'>$2</a>");
  text = text.replace(/\[email\](.*?)\[\/email\]/gi, "<a href='mailto:$1'>$1</a>");

  // Zitate
  text = text.replace(/\[quote\](.*?)\[\/quote\]/gi, "<fieldset class='quote'><legend class='quote'><b>Zitat</b></legend>$1</fieldset>");
  text = text.replace(/\[quote=(.*?)\](.*?)\[\/quote\]/gi, "<fieldset class='quote'><legend class='quote'><b>Zitat von:</b> $1</legend>$2</fieldset>");
  text = text.replace(/\[quote (.*?)\](.*?)\[\/quote\]/gi, "<fieldset class='quote'><legend class='quote'><b>Zitat von:</b> $1</legend>$2</fieldset>");


  // Bilder
  text = text.replace(/\[img\](.*?)\[\/img\]/gi, "<img src=\"$1\" alt=\"Bild: $1\">");
  text = text.replace(/\[flag (.*?)\]/gi, "<img src='images/flags/$1.gif' border='0' alt='Flagge $1' class='flag' />");
  text = text.replace(/\[thumb (.*?)\](.*?)\[\/thumb\]/gi, "<a href='$2'><img src='$2' alt='$2' width='$1' border='0' /></a>");

  // Farben
  text = text.replace(/\[font=(.*?)\]/gi, "<font style='font-family:$1'>");
  text = text.replace(/\[color=(.*?)\]/gi, "<font style='color:$1'>");
  text = text.replace(/\[size=(.*?)\]/gi, "<font style='font-size:$1pt'>");
  text = text.replace(/\[\/font\]/gi, "</font>");
  text = text.replace(/\[\/color\]/gi, "</font>");
  text = text.replace(/\[\/size\]/gi, "</font>");

  // Tabelle
  text = text.replace(/\[table\]/gi, "<table class='bbtable'>");
  text = text.replace(/\[\/table\]/gi, "</table>");
  text = text.replace(/\[th\]/gi, "<th>");
  text = text.replace(/\[\/th\]/gi, "</th>");
  text = text.replace(/\[tr\]/gi, "<tr>");
  text = text.replace(/\[\/tr\]/gi, "</tr>");
  text = text.replace(/\[td\]/gi, "<th>");
  text = text.replace(/\[\/td\]/gi, "</td>");

  // Smilies
  text = text.replace(/:angry:/gi, "<img src='images/smilies/angry.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:sad:/gi, "<img src='images/smilies/sad.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:anger:/gi, "<img src='images/smilies/anger.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:pst:/gi, "<img src='images/smilies/pst.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:holy:/gi, "<img src='images/smilies/holy.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:cool:/gi, "<img src='images/smilies/cool.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:rolleyes:/gi, "<img src='images/smilies/rolleyes.gif' style='border:none;' alt='Smilie' title='Smilie' />");

  text = text.replace(/:\)/gi, "<img src='images/smilies/smile.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:-\)/gi, "<img src='images/smilies/smile.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/;\)/gi, "<img src='images/smilies/wink.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/;-\)/gi, "<img src='images/smilies/wink.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:P/gi, "<img src='images/smilies/tongue.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:-P/gi, "<img src='images/smilies/tongue.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:0/gi, "<img src='images/smilies/laugh.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:D/gi, "<img src='images/smilies/biggrin.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:-D/gi, "<img src='images/smilies/biggrin.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:\(/gi, "<img src='images/smilies/frown.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/:-\(/gi, "<img src='images/smilies/frown.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/8\)/gi, "<img src='images/smilies/cool.gif' style='border:none;' alt='Smilie' title='Smilie' />");
  text = text.replace(/8-\)/gi, "<img src='images/smilies/cool.gif' style='border:none;' alt='Smilie' title='Smilie' />");

  document.getElementById(target).innerHTML = text;
}

function updatePeopleWorkingBox(people, time, food) {
  var peopleOptimized = parseFloat(document.getElementById('peopleOptimized').value);
  var peopleFree = parseFloat(document.getElementById('peopleFree').value);
  var foodAvaiable = parseFloat(document.getElementById('foodAvaiable').value);
  var foodRequired = parseFloat(document.getElementById('foodRequired').value);
  var workDone = parseFloat(document.getElementById('workDone').value);
  var error = "";
  people = parseFloat(people.replace(/`/g, ""));
  food = parseFloat(food.replace(/`/g, ""));
  if (people != -1) {
    if (people > peopleFree) people = peopleFree;
    food = people * foodRequired;
    time = people * workDone;
  }
  else if (food != -1) {
    people = Math.floor(food / foodRequired);
    time = people * workDone;
  }
  else if (time != -1) {
    if (is_tf(time)) {
      time = parseFloat(tf_back(time));
      people = Math.floor(time / workDone);
      food = people * foodRequired;

    }
    else return;
  }

  if (people > peopleFree)
    error = "Nicht genug freie Arbeiter vorhanden!";
  //else if (food > foodAvaiable)
  //	error = "Nicht genug Nahrung vorhanden!";
  else if (peopleOptimized != 0 && people > peopleOptimized)
    error = "Mehr Arbeiter als notwendig ausgewählt!";

  if (error.length > 0) {
    $('#changeWorkingPeopleError').html(error).show();
    $('#submit_people_form').hide();
  }
  else {
    $('#changeWorkingPeopleError').html('').hide();
    $('#submit_people_form').show();
  }

  document.getElementById('peopleWorking').value = FormatNumber('return', people, peopleFree, '', '');
  document.getElementById('foodUsing').value = FormatNumber('return', food, 0, '', '');
  document.getElementById('timeReduction').value = formatTimespan(time);
}

// checks if the last sign is a letter
function is_tf(time) {
  time = time.replace(/\s+/g, '');

  if (time[time.length - 1] == 's' || time[time.length - 1] == 'm' || time[time.length - 1] == 'h' || time[time.length - 1] == 'd' | time[time.length - 1] == 'w')
    return true;
  else return false;
}
function tf_back(time) {
  var value = 0;
  var index = 0;
  var index2 = 0;
  if (time.indexOf("w") > 0) {
    index2 = time.indexOf("w");
    value += 3600 * 24 * 7 * parseInt(time.slice(index, index2));
    index = index2 + 1;
  }
  if (time.indexOf("d") > 0) {
    index2 = time.indexOf("d");
    value += 3600 * 24 * parseInt(time.slice(index, index2));
    index = index2 + 1;
  }
  if (time.indexOf("h") > 0) {
    index2 = time.indexOf("h");
    value += 3600 * parseInt(time.slice(index, index2));
    index = index2 + 1;
  }
  if (time.indexOf("m") > 0) {
    index2 = time.indexOf("m");
    value += 60 * parseInt(time.slice(index, index2));
    index = index2 + 1;
  }
  if (time.indexOf("s") > 0) {
    index2 = time.indexOf("s");
    value += parseInt(time.slice(index, index2));
  }
  return value;
}

function formatTimespan(time)
{
  var w = Math.floor(time / 3600 / 24 / 7);
  time -= w * 3600 * 24 * 7;
  var t = Math.floor(time / 3600 / 24);
  var h = Math.floor((time - (t * 3600 * 24)) / 3600);
  var m = Math.floor((time - (t * 3600 * 24) - (h * 3600)) / 60);
  var s = Math.floor((time - (t * 3600 * 24) - (h * 3600) - (m * 60)));

  var str = "";
  if (w > 0)
    str += w.toString() + "w ";
  if (t > 0)
    str += t.toString() + "d ";
  if (h > 0)
    str += h.toString() + "h ";
  if (m > 0)
    str += m.toString() + "m ";
  if (s > 0)
    str += s.toString() + "s ";

  if (str.length == 0)
    str = "0s ";

  return str;
}

//
// Javascript für dynamischen Planetkreis
//
function show_info(
  planet_id,
  planet_name,
  building_name,
  building_time,
  shipyard_name,
  shipyard_time,
  defense_name,
  defense_time,
  people,
  res_metal,
  res_crystal,
  res_plastic,
  res_fuel,
  res_food,
  use_power,
  prod_power,
  store_metal,
  store_crystal,
  store_plastic,
  store_fuel,
  store_food,
  people_place) {

  //Planetinfo Anzeigen
  document.getElementById("planet_info_name").firstChild.nodeValue = planet_name;

  document.getElementById("planet_info_building_name").firstChild.nodeValue = building_name;
  document.getElementById("planet_info_building_time").firstChild.nodeValue = building_time;

  document.getElementById("planet_info_shipyard_name").firstChild.nodeValue = shipyard_name;
  document.getElementById("planet_info_shipyard_time").firstChild.nodeValue = shipyard_time;

  document.getElementById("planet_info_defense_name").firstChild.nodeValue = defense_name;
  document.getElementById("planet_info_defense_time").firstChild.nodeValue = defense_time;

  //Überprüfen ob Speicher voll ist
  var check_metal = store_metal - res_metal;
  var check_crystal = store_crystal - res_crystal;
  var check_plastic = store_plastic - res_plastic;
  var check_fuel = store_fuel - res_fuel;
  var check_food = store_food - res_food;
  var check_people = people_place - people;

  var rest_power = prod_power - use_power;

  //Wenn Speicher voll, anders darstellen als normal
  if (check_metal <= 0) {
    document.getElementById("planet_info_res_metal").className = 'resfullcolor';
  }
  else {
    document.getElementById("planet_info_res_metal").className = 'resmetalcolor';
  }

  if (check_crystal <= 0) {
    document.getElementById("planet_info_res_crystal").className = 'resfullcolor';
  }
  else {
    document.getElementById("planet_info_res_crystal").className = 'rescrystalcolor';
  }

  if (check_plastic <= 0) {
    document.getElementById("planet_info_res_plastic").className = 'resfullcolor';
  }
  else {
    document.getElementById("planet_info_res_plastic").className = 'resplasticcolor';
  }

  if (check_fuel <= 0) {
    document.getElementById("planet_info_res_fuel").className = 'resfullcolor';
  }
  else {
    document.getElementById("planet_info_res_fuel").className = 'resfuelcolor';
  }

  if (check_food <= 0) {
    document.getElementById("planet_info_res_food").className = 'resfullcolor';
  }
  else {
    document.getElementById("planet_info_res_food").className = 'resfoodcolor';
  }

  if (check_people <= 0) {
    document.getElementById("planet_info_people").className = 'resfullcolor';
  }
  else {
    document.getElementById("planet_info_people").className = 'respeoplecolor';
  }

  if (rest_power <= 0) {
    document.getElementById("planet_info_power").className = 'resfullcolor';
  }
  else {
    document.getElementById("planet_info_power").className = 'respowercolor';
  }


  var res_metal = FormatNumber('return', res_metal, 0, '', '');
  var res_crystal = FormatNumber('return', res_crystal, 0, '', '');
  var res_plastic = FormatNumber('return', res_plastic, 0, '', '');
  var res_fuel = FormatNumber('return', res_fuel, 0, '', '');
  var res_food = FormatNumber('return', res_food, 0, '', '');
  var people = FormatNumber('return', people, 0, '', '');
  var use_power = FormatNumber('return', use_power, 0, '', '');

  var store_metal = FormatNumber('return', store_metal, 0, '', '');
  var store_crystal = FormatNumber('return', store_crystal, 0, '', '');
  var store_plastic = FormatNumber('return', store_plastic, 0, '', '');
  var store_fuel = FormatNumber('return', store_fuel, 0, '', '');
  var store_food = FormatNumber('return', store_food, 0, '', '');
  var people_place = FormatNumber('return', people_place, 0, '', '');
  var prod_power = FormatNumber('return', prod_power, 0, '', '');

  if (rest_power >= 0) {
    var rest_power = FormatNumber('return', rest_power, 0, '', '');
  }
  else {
    var rest_power = '-' + FormatNumber('return', Math.abs(rest_power), 0, '', '');
  }


  //Roshtoff Anzeigen
  document.getElementById("planet_info_res_metal").firstChild.nodeValue = '' + res_metal + ' t';
  document.getElementById("planet_info_res_crystal").firstChild.nodeValue = '' + res_crystal + ' t';
  document.getElementById("planet_info_res_plastic").firstChild.nodeValue = '' + res_plastic + ' t';
  document.getElementById("planet_info_res_fuel").firstChild.nodeValue = '' + res_fuel + ' t';
  document.getElementById("planet_info_res_food").firstChild.nodeValue = '' + res_food + ' t';
  document.getElementById("planet_info_power").firstChild.nodeValue = rest_power;
  document.getElementById("planet_info_people").firstChild.nodeValue = people;


  //Alle Beschriftungen anzeigen
  document.getElementById("planet_info_text_building").innerHTML = '<a href=\"?page=buildings&change_entity=' + planet_id + '\">Bauhof:</a>';
  document.getElementById("planet_info_text_shipyard").innerHTML = '<a href=\"?page=shipyard&change_entity=' + planet_id + '\">Schiffswerft:</a>';
  document.getElementById("planet_info_text_defense").innerHTML = '<a href=\"?page=defense&change_entity=' + planet_id + '\">Waffenfabrik:</a>';
  document.getElementById("planet_info_text_res").firstChild.nodeValue = 'Ressourcen';
  document.getElementById("planet_info_text_res_metal").className = 'resmetalcolor';
  document.getElementById("planet_info_text_res_crystal").className = 'rescrystalcolor';
  document.getElementById("planet_info_text_res_plastic").className = 'resplasticcolor';
  document.getElementById("planet_info_text_res_fuel").className = 'resfuelcolor';
  document.getElementById("planet_info_text_res_food").className = 'resfoodcolor';
  document.getElementById("planet_info_text_people").className = 'respeoplecolor';
  document.getElementById("planet_info_text_power").className = 'respowercolor';
  document.getElementById("planet_info_text_res_metal").firstChild.nodeValue = 'Titan:';
  document.getElementById("planet_info_text_res_crystal").firstChild.nodeValue = 'Silizium:';
  document.getElementById("planet_info_text_res_plastic").firstChild.nodeValue = 'PVC:';
  document.getElementById("planet_info_text_res_fuel").firstChild.nodeValue = 'Tritium:';
  document.getElementById("planet_info_text_res_food").firstChild.nodeValue = 'Nahrung:';
  document.getElementById("planet_info_text_people").firstChild.nodeValue = 'Bewohner:';
  document.getElementById("planet_info_text_power").firstChild.nodeValue = 'Energie:';
}

function htmlEntities(str) {
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function ajaxRequest(path, queryData, callbackFunction, errorFunction) {
  $.getJSON(path, queryData, function (data) {
    var errorMsg;
    $.each(data, function (key, val) {
      if (key == "error") {
        errorMsg = val;
      }
    });
    if (errorMsg) {
      if (typeof errorFunction !== 'undefined') {
        errorFunction(errorMsg);
      }
    } else {
      callbackFunction(data);
    }
  });
}

/**
* TODO
*/
function fleetBookmarkSearchShipList(val) {
  ajaxRequest('/api/ships/search', { q: val }, function (data) {
    if (data.count > 0) {
      var items = [];
      $.each(data.entries, function (key, val) {
        items.push(val.name);
      });
      alert(items.join('\n'));
    }
  }, alert);
}

/**
 * Add a ship to the fleet bookmark list
 */
function fleetBookmarkAddShipToList(shipId, shipCount) {

  ajaxRequest('/api/ships/search-info', { ship: shipId }, function (data) {
    if (data.id) {
      if ($('#ship_row_' + data.id).length == 0) {

        var numberInput = '-';
        if (data.launchable == 1) {
          numberInput = $('<input>')
            .attr('id', 'ship_count_' + data.id)
            .attr('name', 'ship_count[' + data.id + ']')
            .attr('size', 10)
            .attr('value', typeof shipCount !== 'undefined' ? shipCount : 1)
            .attr('title', 'Anzahl Schiffe eingeben, die mitfliegen sollen')
            .click(function (evt) { evt.currentTarget.select(); })
            .keyup(function (evt) { FormatNumber(evt.currentTarget.id, evt.currentTarget.value, '', '', '') });
        }

        $('#bookmarkShiplistInputTable > tbody:last').append($('<tr>')
          .attr('id', 'ship_row_' + data.id)
          .append($('<td>')
            .addClass('thumb')
            .append($('<img>')
              .attr('src', data.image)
            )
          )
          .append($('<td>')
            .text(data.name)
            .mouseover(function (evt) { showTT(data.name, data.tooltip, 1, evt, evt.currentTarget) })
            .mouseout(hideTT)
          )
          .append($('<td>')
            .addClass('count')
            .append(numberInput)
          )
          .append($('<td>')
            .addClass('actions')
            .append($('<a>')
              .append($('<img>')
                .attr('src', 'images/icons/delete.png')
                .attr('alt', 'Entfernen')
                .attr('title', 'Entfernen')
              )
              .click(function () { fleetBookmarkRemoveShipFromList(data.id) })
            )
          )
        );
      }
      $('#shipname').val('');
      $('#saveShips').show();
    }
  }, alert);
}

/**
* Remove a ship from the fleet bookmakr list
*/
function fleetBookmarkRemoveShipFromList(shipId) {
  $('#ship_row_' + shipId).remove();
}

//
// User setup
//
$(function () {
  if ($('#register_user_race_id').length > 0) {
    $('#register_user_race_id').change(function () {
      getRaceInfo($(this).val());
    });
    getRaceInfo(0);
  }
});

function rdm() {
  document.getElementById("register_user_race_id").selectedIndex = Math.random() * (document.getElementById("register_user_race_id").length - 1) + 1;
  getRaceInfo(document.getElementById("register_user_race_id").value);
}

function getRaceInfo(id) {
  ajaxRequest('/api/races/info', { id: id }, function (data) {
    if (data.content) {
      $('#raceInfo').html(data.content);
      $('#submit_setup1').show();
    } else {
      $('#raceInfo').html('Bitte Rasse auswählen!');
      $('#submit_setup1').hide();
    }
  }, alert);
}

function advanceQuest(questId, transition, cb) {
  $.ajax({
    type: 'PUT',
    url: '/api/quests/' + questId + '/advance/' + transition,
    contentType: 'application/json'
  }).done(cb);
}

// builds URL when reselecting planet
function setSelectUrl() {

  var filter_planet_id = document.getElementById("filter_planet_id");
  var value_planet = filter_planet_id.options[filter_planet_id.selectedIndex].value;

  var filter_sol_id = document.getElementById("filter_sol_id");
  var value_sol = filter_sol_id.options[filter_sol_id.selectedIndex].value;

  var url_string = window.location.href;
  var url = new URL(url_string);
  var setup_sy = url.searchParams.get("setup_sy");
  var setup_sx = url.searchParams.get("setup_sx");

  document.location = '?setup_sx=' + setup_sx + '&setup_sy=' + setup_sy + '&filter_p=' + value_planet + '&filter_s=' + value_sol;
}
