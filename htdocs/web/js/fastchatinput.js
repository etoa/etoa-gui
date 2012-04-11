/* fastchatinput.js von river */

window.onload = setFocus;

function setFocus()
{
    try
    {
	var input = document.getElementById('ctext');
	if(typeof input == 'undefined' || input == null)
	{
	    msgFail('Fehler: wrongID');
	    return false;
	}
	//if(input.hasFocus()
	input.focus();
    }
    catch(e)
    {
	msgFail('Fehler: idNotFound');
    }
}

function logoutFromChat()
{
    var xr = new XMLHttpRequest();
    xr.open('GET','fastchatlogout.php',true);
    xr.onreadystatechange = function(){ if(xr.readyState == 4) finish_logoutFromChat(xr); }
    xr.send(null);
}

function finish_logoutFromChat(xobj)
{
    try
    {
	parent.top.location = parent.main.location;
    }
    catch(e)
    {
	try
	{
	    document.getElementById('chatinput').innerHTML = '';
	}
	catch(e2)
	{
	    
	}
    }
}

function sendChat(id)
{
    msgFail('...');
    var input = document.getElementById(id);
    if(typeof input == 'undefined' || input == null)
    {
	msgFail('Fehler: wrongID');
	return false;
    }
    var ctext = input.value;
    if(ctext)
    {
	var xr = new XMLHttpRequest();
	xr.open('POST','fastchatpush.php',false);
	xr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xr.onreadystatechange = function()
	{
	    var ret = false;
	    if(xr.responseText)
	    {
		ret = handleResponse(xr.responseText);
	    }
	    else
	    {
		msgFail('+');
	    }
	    input.value = '';
	    input.focus();
	}
	xr.send('ctext='+ctext);
    }
    else
    {
	msgFail('noText');
	return false;
    }
    return false;
}

function msgFail(rtext)
{
    document.getElementById('msg').innerHTML = rtext;
}

function handleResponse(rtext)
{
    if(rtext.size < 2)
	return false;
    var command = rtext.substr(0,2);
    if      (command == 'nu' || command == 'nl')
	logOut();
    else if (command == 'aa')
	adminMsg(rtext);
    else if (command == 'de')
	msgFail('&ndash;');
    else if (command == 'bl')
	banlist(rtext);
    else
	msgFail('Serverfehler: notProtocol');
    return true;
}

function logOut()
{
    try
    {
	msgFail('Sie sind ausgeloggt');
	parent.top.location = parent.main.location;
    }
    catch(e)
    {
	window.close();
    }
}

function adminMsg(msg)
{
    alert(msg.substring(3));
}

function banlist(msg)
{
    alert(msg.substring(3));
}
