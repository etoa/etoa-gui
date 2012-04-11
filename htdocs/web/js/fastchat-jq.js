/* fastchat-jq.js von river */

var minId = 0;
var userName = '';
var numUsers = 0;
var loginFlag = false;


/* Basic chat and server communication functionality */

// OnLoad function

$(document).ready(function(){
    if($('#chatitems').size() != 0)
    {
	poll();
	$('#userListButton').toggle(
	    function(){showUserList();},
	    function(){hideUserList();}
	);
    }
    else
    {
	logOut();
    }
});


// polls for new chat messages every second

function poll()
{
    var req = $.post('fastchatpoll.php',{'minId' : minId},null,'text');
    req.success(function(data){handleResponse(data);});
    if(loginFlag)
    {
	req.success(function(data)
	{
	    if(userName == '')
	    {
		exitChat();
	    }
	    else
	    {
		var ltext = 'Hallo ' + userName + ',  willkommen im EtoA-Chat.' +
		' Bitte beachte, dass wir Spam nicht dulden und eine gepflegte' +
		' Ausdrucksweise erwarten. Bei Verst&ouml;ssen gegen diese ' +
		'Regeln werden wir mit Banns und/oder Accountsperrungen vorgehen!';
		chatMsg(ltext);
	    }
	});
	loginFlag = false;
    }
    else
    {
	req.success(function(){ setTimeout(function(){poll();},1000); });
    }
    req.error(function(){ chatMsg(
	'Verbindung zum Server fehlgeschlagen. M&ouml;glicherweise' +
	' ist der Server &uuml;berlastet. Versuche den Chat neu zu laden.'
    ) });
}


// selects the action according to the returned text
// and executes the corresponding function

function handleResponse(rtext)
{
    if(rtext.length < 2)
    {
	msgFail('');
	return;
    }
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
}


/* Response handling functions */

// displays a message if the user is not logged in
// on this etoa server.

function logOut()
{
    $('body').empty().append('<p>Sie sind ausgeloggt</p>');
    exitChat();
}


// closes the chat if the user is kicked
// and displays the reason message.

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
    exitChat();
}


// Sets a flag for poll() to display a welcome message

function logIn(rtext)
{
    if(rtext.length > 3)
    {
	userName = rtext.substring(3);
	loginFlag = true;
	// now there exist two poll cycles for a short time.
	// but the first one that discovers the login flag
	// won't call itself again.
	poll();
    }
    else
    {
	msgFail('Serverfehler: noUname');
    }
}


// closes the chat if the user is banned
// and displays a reason message.

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
    exitChat();
}


// displays new chat messages

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
		// appends the messages to the chat window
		$('#chatitems').append(rtext.substring(4+splitted[1].length));
		$('html').scrollTop(100000);
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



/* User list functions */

// refreshes the list of users in chat
// and displays it

function showUserList()
{
    updateUserList(function()
    {
	$('#userListButton').attr(
	    'value','User verbergen (' + numUsers + ')'
	);
	$('#userlist').show();
	$('html').scrollTop(100000);
    });
}


// hides the list of online users

function hideUserList()
{
    updateUserList(function()
    {
	if($('#userlist').hide().size() != 0)
	{
	    $('#userListButton').attr(
		'value','User anzeigen (' + numUsers + ')'
	    );
	}
	$('html').scrollTop(100000);
    });
}

function updateUserList(callback)
{
    var req = $.post('fastchatuserlist.php');
    req.success(function(data){
	var txt = '';
	if(data.length == 0)
	{
	    txt = 'Keine User online';
	}
	else
	{
	    var split = data.split(':');
	    if(!split || split.length < 2)
	    {
		msgFail('Serverfehler: notProtocol');
		return;
	    }
	    numUsers = split[0];
	}
	if(isNaN(numUsers))
	{
	    msgFail('Serverfehler: usercount is NaN');
	    return;
	}
	else
	{
	    txt = split[1];
	    $('#userlist').empty().append(txt);
	}
    });
    if(callback)
    {
	req.success(callback);
    }
}



/* text display and exit functions */

// closes the chat (or at least tries it)

function exitChat()
{
    try
    {
	parent.top.location = parent.main.location;
    }
    catch(e)
    {
	window.close();
    }
}


// adds a short plaintext message (for errors)

function msgFail(rtext)
{
    $('#loading').empty().append(rtext);
}


// adds a "server message" into the chat window

function chatMsg(text)
{
    $('#chatitems').append('<span style="color: #aaa;">' +
	text + '</span><br />'
    );
    $('html').scrollTop(100000);
}

