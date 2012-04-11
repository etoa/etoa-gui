/* fastchatinput-jq.js von river */


// onload function
// gives focus to the input field.

$('document').ready(function()
{
    if($('#ctext').focus().size() == 0)
    {
	msgFail('Fehler: wrong id');
    }
});

// logs the user out from the chat.

function logoutFromChat()
{
    $.post('fastchatlogout.php').complete(function(){
	closeChat();
    });
}


/* actual chat send function */

// sends a request to the server and handles the response.

function sendChat()
{
    msgFail('...');
    var input = $('#ctext');
    if(input.size() == 0)
    {
	msgFail('Fehler: wrongID');
	return;
    }
    var ctext = input.val();
    if(ctext)
    {
	var xr =$.post('fastchatpush.php',{'ctext':ctext});
	xr.success(function(data)
	{
	    if(data.length > 0)
	    {
		handleResponse(data);
	    }
	    else
	    {
		msgFail('+');
	    }
	    $('#ctext').val('').focus();
	});
    }
    else
    {
	msgFail('noText');
    }
}



/* request handling */


// selects the action according to the returned text
// and executes the corresponding function

function handleResponse(rtext)
{
    if(rtext.length < 2)
	return;
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
}

// function called if the user isn't logged in

function logOut()
{
    msgFail('Sie sind ausgeloggt');
    closeChat();
}

// Alerts an admin message

function adminMsg(msg)
{
    alert(msg.substring(3));
}

// displays the banlist

function banlist(msg)
{
    alert(msg.substring(3));
}



/* chat utilities */

// removes all content and tries to close the chat frame.

function closeChat()
{
    try
    { 
	parent.chat.close();
	parent.top.location = parent.main.location;
    }
    catch(e) { window.close(); }
}


// simple error message display function

function msgFail(rtext)
{
    $('#msg').empty().append(rtext);
}

