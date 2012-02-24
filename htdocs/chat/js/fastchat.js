/* fastchat.js von river */

window.onload = startpolling;

var minId = 0;
var userName = '';
var numUsers = 0;

function startpolling()
{
    var itemtest = document.getElementById('chatitems');
    if(typeof itemtest != 'undefined' && itemtest != null)
	poll();
    else logOut();
}

function poll(noTimeout)
{
    msgFail('...');
    var xr = new XMLHttpRequest();
    xr.open('POST','fastchatpoll.php',false);
    xr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xr.send('minId='+minId);
    var ret = false;
    if(xr.responseText)
    {
	ret = handleResponse(xr.responseText);
    }
    else
	msgFail('');
    window.scrollBy(0,100000);
    if(typeof noTimeout != 'boolean' || !noTimeout)
	setTimeout('startpolling();',1000);
    return ret;
}

function handleResponse(rtext)
{
    if(rtext.size < 2)
	return false;
    var command = rtext.substr(0,2);
    if      (command == 'lo')
	logOut();
    else if (command == 'ki')
	kicked(rtext);
    else if (command == 'li')
	logIn(rtext);
    else if (command == 'bn')
	banned(rtext);
    else if (command == 'up')
	update(rtext);
    else
	msgFail('Serverfehler: notProtocol');
    return true;
}

function logOut()
{
    try
    {
	document.getElementsByTagName('body')[0].innerHTML = '<p>Sie sind ausgeloggt</p>';
	parent.top.location = parent.main.location;
    }
    catch(e)
    {
	window.close();
    }
}

function kicked(rtext)
{
    if (rtext.length > 3)
    {
	alert('Du wurdest aus dem Chat gekickt!\n\nGrund:\n'+rtext.substring(3));
    }
    else
    {
	alert('Du wurdest aus dem Chat gekickt!');
    }
    parent.top.location = parent.main.location;
}

function logIn(rtext)
{
    var voider = poll(true);
    if(rtext.length > 3)
    {
	userName = rtext.substring(3);
    }
    else
    {
	msgFail('Serverfehler: noUname');
    }
    var ltext = 'Hallo ' + userName + ',  willkommen im EtoA-Chat. Bitte beachte das wir Spam nicht dulden und eine gepflegte Ausdrucksweise erwarten. Bei Verstössen gegen diese Regeln werden wir mit Banns und/oder Accountsperrungen vorgehen!';
    document.getElementById('chatitems').innerHTML += '<span style="color: #aaa;">' + ltext + '</span><br />';
}

function banned(rtext)
{
    if (rtext.length > 3)
    {
	alert('Du wurdest aus dem Chat gebannt!\n\nGrund:\n'+rtext.substring(3));
    }
    else
    {
	alert('Du wurdest aus dem Chat gebannt!');
    }
    parent.top.location = parent.main.location;
}

function msgFail(rtext)
{
    try
    {
	document.getElementById('loading').innerHTML = rtext;
    }
    catch(e)
    {
	alert(rtext);
    }
}

function update(rtext)
{
    if(rtext.length > 3)
    {
	var splitted = rtext.split(':');
	if(splitted.length > 1)
	{
	    var num = Number(splitted[1]);
	    if(isNaN(num))
	    {
		msgFail('Serverfehler: NaN');
	    }
	    else
	    {
		minId = num;
		document.getElementById('chatitems').innerHTML += rtext.substring(4+splitted[1].length);
		msgFail('+');
	    }
	}
	else
	{
	    msgFail('Serverfehler: missingParam');
	}
    }
    else
    {
	msgFail('Serverfehler: noParam');
    }
}

function showUserList()
{
    var xr = new XMLHttpRequest();
    xr.open('GET','fastchatuserlist.php',false);
    xr.send(null);
    var txt;
    if(!xr.responseText)
    {
	txt = 'Keine User online';
    }
    else
    {
	txt = xr.responseText;
	var split = txt.split(':');
	if(!split || split.length < 2)
	{
	    msgFail('Serverfehler: notProtocol');
	    return;
	}
	numUsers = split[0];
	txt = split[1];
    }
    var ul = document.getElementById('userlist');
    if(ul)
    {
	ul.innerHTML = txt;
	ul.style.display = 'block';
	var ulb = document.getElementById('userListButton');
	if(ulb)
	{
	    ulb.setAttribute('onclick','hideUserList();');
	    ulb.setAttribute('value','User verbergen (' + numUsers + ')');
	}
    }
    window.scrollBy(0,100000);
}

function hideUserList()
{
    var ul = document.getElementById('userlist');
    if(ul)
    {
	ul.style.display = 'none';
	var ulb = document.getElementById('userListButton');
	if(ulb)
	{
	    ulb.setAttribute('onclick','showUserList();');
	    ulb.setAttribute('value','User anzeigen (' + numUsers + ')');
	}
    }
    window.scrollBy(0,100000);
}