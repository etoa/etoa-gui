/* fastchat-jq.js von river */

var minId = 0;
var lastInsertedId = 0;
var chanId = 0;
var userName = '';
var numUsers = 0;

var msgStack=new Array();
var msgHistory = new Array();
var msgHistoryIdx = -1;

var chatPollTimeout = 0;

/* Basic chat and server communication functionality */

// polls for new chat messages every second

function poll(doLoop)
{
  ajaxRequest('chat_poll', { 
    minId:minId,
    chanId:chanId 
  }, function(data) {
    
    // selects the action according to the returned text
    // and executes the corresponding function
    if (data.cmd) {    
      if (data.cmd == 'lo')
        logOut();
      else if (data.cmd == 'ki')
        kicked(data.msg);
      else if (data.cmd == 'li')
        logIn(data.msg);
      else if (data.cmd == 'bn')
        banned(data.msg);
      else if (data.cmd == 'up')
        update(data.out, data.lastId);
      else
        msgFail('Serverfehler: notProtocol');
    }   
    
    if (doLoop) {
      chatPollTimeout = setTimeout(function(){poll(true);},1000);
    }
        
    hideLoading();
   
  }, function(err){ localMsg(
    'Verbindung zum Server fehlgeschlagen ('+err+'). Möglicherweise' +
    ' ist der Server überlastet. Versuche den Chat neu zu laden.'
  )});
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

function kicked(rtext) {

  if (rtext != '')
  {
    alert('Du wurdest aus dem Chat gekickt!\n\nGrund:\n'+rtext);
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
  if(rtext != '')
  {
    msgStack.push(rtext);
    poll(false);
  }
  else
  {
    msgFail('Serverfehler: noLoginMessage');
  }
}


// closes the chat if the user is banned
// and displays a reason message.

function banned(rtext)
{
  if (rtext != '')
  {
    alert('Du wurdest aus dem Chat gebannt!\n\nGrund:\n'+rtext);
  }
  else
  {
    alert('Du wurdest aus dem Chat gebannt!');
  }
  exitChat();
}


// displays new chat messages

function update(out, lastId)
{
  if(out)
  {
    if(isNaN(lastId))
    {
      msgFail('Serverfehler: NaN');
    }
    else
    {
      // check whether the user has scrolled up, avoid auto-scrolling in that case
      var doScroll = ($('#chatitems').prop('scrollHeight')-$('#chatitems').outerHeight() == $('#chatitems').prop('scrollTop'));
      // appends the messages to the chat window
      $.each(out, function(key, val) {
        var elem = $('<div>');
        if (val.userId=="0") {
          elem.addClass('systemMessage');
          elem.text('<' + val.time + '> ' + val.text);
        } else {
          
          if (val.admin) {
            switch(val.admin)
            {
              //yellow star
              case "1":
                elem.append($('<img>')
                  .attr('src', 'images/star_y.gif')
                  .attr('alt', 'Admin')
                  .attr('title', 'Admin')
                );
                break;
              case "2":
                elem.append($('<img>')
                  .attr('src', 'images/star_g.gif')
                  .attr('alt', 'Chat-Moderator')
                  .attr('title', 'Chat-Moderator')
                );
                break;
              case "3":
                elem.append($('<img>')
                  .attr('src', 'images/star_r.gif')
                  .attr('alt', 'Entwickler')
                  .attr('title', 'Entwickler')
                );
                break;
              case "4":
                elem.append($('<img>')
                  .attr('src', 'images/star_green.gif')
                  .attr('alt', 'Leiter Team Community')
                  .attr('title', 'Leiter Team Community')
                );
                break;
              case "5":
                elem.append($('<img>')
                  .attr('src', 'images/star_c.gif')
                  .attr('alt', 'Entwickler')
                  .attr('title', 'Entwickler')
                );
                break;                  
            }
          }
          
          var link = $('<a>')
            .attr('href','index.php?page=userinfo&id=' + val.userId)
            .attr('target','main')
            .text(val.nick);
            
          if (val.color != "")  {
            elem.css('color', val.color);
            link.css('color', val.color);
          }
          
          elem.append('&lt;');
          elem.append(link);
          elem.append(' | ' + val.time + '&gt; ' + val.text);
        }
        // Insert only if id differs from last insterted element 
        // (mitigates possible race condition when ordinary polling is executed the same time as the chat send polling)
        if (lastInsertedId != val.id) {
          $('#chatitems').append(elem);
        }
        lastInsertedId = val.id;
      });
      
      if (msgStack.length > 0) {
        localMsg(msgStack.pop());
      }
      
      // reset error message
      msgFail('');
      
      if (doScroll && minId != lastId) {
        scrollDown();
      }
      minId = lastId;
    }
  }
  else
  {
    msgFail('Serverfehler: noParam');
  }
}

/* User list functions */

function updateUserList()
{
  ajaxRequest('chat_userlist', null, 
    function(data) {
      if(data.length == 0)
      {
        $('#userlist').empty().append('Keine User online');
      }
      else
      {
        $('#userlist').empty();
        $.each(data, function(key, val) {
          $('#userlist').append($('<div>')
            .append($('<a>')
              .attr('href','index.php?page=userinfo&id='+val.id)
              .attr('target','main')
              .text(val.nick)));
        });
        $('#tabs ul:first li a[href=#tabs-user]').text('User ('+data.length+')');
      }
    }, function(err) {
      msgFail('Serverfehler: '+err)
    });
    
  setTimeout(function() { updateUserList(); }, 5000);
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

function msgFail(rtext) {
  $('#loadingMessage').empty().append(rtext);
}

function msgClear() {
  $('#loadingMessage').empty();
}

function showLoading() {
  $('#loadingAnimation img').show();
}

function hideLoading() {
  $('#loadingAnimation img').fadeOut();
}

// adds a "server message" into the chat window

function localMsg(text, cssClass)
{
  var elem = $('<div>');
  elem.addClass(cssClass ? cssClass : 'serverMessage');
  elem.text(text);
  $('#chatitems').append(elem);

  scrollDown();
}

function scrollDown()
{
  $('#chatitems').animate({ scrollTop: $('#chatitems').prop('scrollHeight') }, "fast");
}

// logs the user out from the chat.

function logoutFromChat()
{
  ajaxRequest('chat_logout', null, closeChat, closeChat);
}

/* actual chat send function */

// sends a request to the server and handles the response.

function sendChat()
{
  showLoading();
  
  var input = $('#ctext');
  if(input.size() == 0)
  {
    msgFail('Fehler: wrongID');
    return;
  }
  
  var ctext = input.val();
  if(ctext)
  {
    if (msgHistory[msgHistory.length-1] != ctext) {
      msgHistory.push(ctext);
    }
    msgHistoryIdx = -1;

    ajaxRequest('chat_push', {"ctext":ctext}, 
      function(data) {  
  
        if (data.cmd) {    

          if (data.cmd == 'nu' || data.cmd == 'nl')
            logOut();
          else if (data.cmd == 'aa')
            adminMsg(data.msg);
          else if (data.cmd == 'de')
            msgFail('Doppelter Text');
          else if (data.cmd == 'bl')
            banlist(data.list);
          else
            msgFail('Serverfehler: notProtocol');
        } else {
          //poll, but only once, to insta-load pushed text
          //otherwise the user won't get immediate feedback
          poll(false);
        }
        $('#ctext').val('').focus();
      }
    );
  }
  else
  {
    msgFail('Kein Text');
  }  
  hideLoading();
}

// Alerts an admin message

function adminMsg(msg)
{
  localMsg(msg, 'adminMessage');
}

// displays the banlist

function banlist(data)
{
  localMsg("Gebannte User:");
  $.each(data, function(key, val) {
    localMsg(val.nick + " (" + val.date + ", " + val.reason + ")");
  });
}



/* chat utilities */

// removes all content and tries to close the chat frame.

function closeChat()
{
  try { 
    parent.top.location = parent.main.location;
  }
  catch(e) { 
    window.close(); 
  }
}

function handleCTextKey(event) {

  // Arrow key up -> history back
  if(event.which == 38) {
    if (msgHistoryIdx < 0) {
      msgHistoryIdx = msgHistory.length -1;
    } else if (msgHistoryIdx > 0) {
      msgHistoryIdx--;
    }
    $('#ctext').val(msgHistory[msgHistoryIdx]);
  }
  // Arrow key down -> history forward
  else if(event.which == 40) {
    if (msgHistoryIdx>= 0 && msgHistoryIdx < msgHistory.length) {
      msgHistoryIdx++;
      $('#ctext').val(msgHistory[msgHistoryIdx]);
    }
  }  
}