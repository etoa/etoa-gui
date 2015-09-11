/* fastchat-jq.js von river */

var minId = 0;
var lastInsertedId = 0;
var chanId = 0;
var userName = '';
var numUsers = 0;

var unviewed = 0;

var msgStack=new Array();
var msgHistory = new Array();
var msgHistoryIdx = -1;

var chatPollTimeout = 0;
var chatPollDelayMilliseconds = 1000;

/*
* Page initialization
*/
$(function() {

	// Chat event handlers

	$('#cform').submit(function() {
		sendChat();
		return false;
	});
	
	$('#logoutButton').click(function(){
		logoutFromChat();
	});
	
	$('#ctext').keyup(function(event) {
		handleCTextKey(event);
	});
  
	// add/remove unread messages indicator on scrolling
	$('#chatitems').scroll(function(){
		updateViewed();
	});
	
	$('#usercount').click(fetchUserList);
	
	// gives focus to the input field.
	$('#ctext').focus();
	
	// Start polling
	if($('#chatitems').size() != 0)
	{
		poll(true);
		updateUserList();
	}
	else
	{
		logOut();
	}

	// Resize chat area
	function resizeUi() {
		var h = $(window).height();
		var w = $(window).width();
		$("#chatitems").css('height', $("#chatcontainer").height() - 20);
	};
	var resizeTimer = null;
	$(window).bind('resize', function() {
		if (resizeTimer) clearTimeout(resizeTimer);
		resizeTimer = setTimeout(resizeUi, 100);
	});
	resizeUi();
});

/* Basic chat and server communication functionality */

// polls for new chat messages every second

function poll(doLoop)
{
  ajaxRequest('chat_poll', { 
    "minId":minId,
    "chanId":chanId 
  }, function(data) {    
    // selects the action according to the returned text
    // and executes the corresponding function
    if (data.cmd) {    
      if (data.cmd == 'lo')
        logOut();
      else if (data.cmd == 'ki')
        kicked(data.msg);
      else if (data.cmd == 'li')
      {
        logIn(data.msg);
        (new UpdateThread()).syncUpdate(data.out,data.lastId);
      }
      else if (data.cmd == 'bn')
        banned(data.msg);
      else if (data.cmd == 'up')
        (new UpdateThread()).syncUpdate(data.out,data.lastId);
      else
        msgFail('Serverfehler: notProtocol');
    }   
    
    if (doLoop) {
      chatPollTimeout = setTimeout(function(){poll(true);},chatPollDelayMilliseconds);
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

// Sets a welcome message
function logIn(rtext)
{
  if(rtext != '')
  {
    msgStack.push(rtext);
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


/* User list functions */

function updateUserList()
{
  ajaxRequest('chat_userlist', null, 
    function(data) {
      if(data.length == 0)
      {
		$('#usercount').html('Keine User online');
      }
      else
      {
		$('#usercount').html('' + data.length + ' User');
      }
    }, function(err) {
      msgFail('Serverfehler: '+err)
    });
    
  setTimeout(function() { updateUserList(); }, 5000);
}

function fetchUserList()
{
  ajaxRequest('chat_userlist', null, 
    function(data) {
		if(data.length == 0)
		{
			localMsg('Keine User online');
		}
		else
		{
			var elem = $('<div>');
			elem.addClass('serverMessage');
			elem.append($('<div>').text("User im Chat:"));
			// Append each user
			$.each(data, function(key, val) {
			  elem.append($('<div>').text(" ")
				.append($('<a>')
				  .attr('href','index.php?page=userinfo&id='+val.id)
				  .attr('target','main')
				  .text(val.nick)));
			});
			$('#chatitems').append(elem);
			$('#ctext').focus();
			scrollDown();
		}
    }, function(err) {
      msgFail('Serverfehler: '+err)
    });
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


// sets elements as read on scroll
function updateViewed(ev)
{
  $('.unviewed').filter(function(){return !isInvisible($(this))})
  .removeClass('unviewed')
  .addClass('viewed');
  unviewed = $('.unviewed').size();
  // update invisible count
  if(unviewed > 0)
  {
    $('#unread').show()
    .html(unviewed+'&nbsp;&darr;');
  }
  else
  {
    $('#unread').hide();
  } 
}

// checks whether the element is below the visible part of the chat
function isInvisible(jqElem)
{
  return (jqElem.position().top - jqElem.parent().position().top - jqElem.parent().prop('clientHeight')) > 0;
}


/* Mutex functionality
 *
 * implements Wallace's variant of
 * Lamport's bakery algorithm
 */


// Thread class for the update function

function UpdateThread()
{
  // Static variable to assign a new id to each thread
  if(!UpdateThread.ThreadCounter)
  {
    UpdateThread.ThreadCounter = 0;
  }
  // instance variable to store the thread id
  this.id = ++UpdateThread.ThreadCounter;
  
  // unsynchronized update() function
  // only call via mutex
  
  // displays new chat messages
  this.update = function(out, lastId)
  {
    // CRITICAL SECTION
    if(out)
    {
      if(isNaN(lastId))
      {
        msgFail('Serverfehler: NaN');
      }
      else
      {
        // check whether the user has scrolled up, avoid auto-scrolling in that case
        var doScroll =
            ( $('#chatitems').prop('scrollHeight') -
              $('#chatitems').prop('clientHeight') -
              $('#chatitems').prop('scrollTop') ) < 1;
        var added = 0;
        
        // appends the messages to the chat window
        $.each(out, function(key, val) {
          if($('#chatmsg_'+val.id).size() == 0)
          {
            var elem = $('<div>');
            elem.attr('id','chatmsg_'+val.id);
            elem.addClass('chatmsg');
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
            if (lastInsertedId < val.id) {
              $('#chatitems').append(elem);
              added++;
              if(doScroll || !isInvisible(elem))
              {
                elem.addClass('viewed');
              }
              else
              {
                elem.addClass('unviewed');
              }
            }
            lastInsertedId = val.id;
          }
        });
        
        if (msgStack.length > 0) {
          localMsg(msgStack.pop());
        }
        
        // reset error message
        msgFail('');
        
        if (doScroll && minId != lastId) {
          scrollDown();
        }
        updateViewed();
        minId = lastId;
      }
    }
    else
    {
      msgFail('Serverfehler: noParam');
    }
  }
  
  // synchronized update() function with mutual exclusion
  this.syncUpdate = function(out, lastId)
  {
    return (new UpdateMutex(this,out,lastId));
  }
}

// Mutex class for the update function

function UpdateMutex(threadObj, o, lid)
{
  // static variable to store synchronized update attempts
  if(!UpdateMutex.Wait)
  {
    UpdateMutex.Wait = new Object();
  }
  
  // static method for acquiring cpu time
  UpdateMutex.Run = function(commandId, startId)
  {
    UpdateMutex.Wait[commandId].attempt( UpdateMutex.Wait[startId] );
  }
  
  // instance method for attempting to run command
  this.attempt = function( startMutexObject )
  {
    // we loop until UpdateMutex.next() returns null
    for(var j=startMutexObject; j; j=UpdateMutex.next(j.threadObject.id))
    {
      if(j.enter ||
          (j.number &&
            (j.number < this.number ||
              (j.number == this.number && j.threadObject.id < this.threadObject.id)
            )
          )
        )
      {
        // we don't have the exclusive access, because there is another
        // thread with higher priority, so we sleep and try again
        return setTimeout("UpdateMutex.Run("+this.threadObject.id+","+j.threadObject.id+")",30);
      }
      // we have exclusive access now so we run update()
      // ACTUAL UPDATE() CALL IS HERE
      this.threadObject.update(this.out,this.lastId)
      // now the exclusive access can be released
      this.number = 0;
      // and we can delete this object
      UpdateMutex.remove(this.threadObject.id);
    }
    // there is no next element in the Wait queue
    return null;
  }
  
  // static wait queue manipulation functions
  
  // remove an element from the Wait queue
  // => destroy the mutex instance
  UpdateMutex.remove = function(tid)
  {
    delete UpdateMutex.Wait[tid];
  }

  // returns next mutex instance in the Wait queue
  // starting at key tid
  UpdateMutex.next = function(tid)
  {
    for(i in UpdateMutex.Wait)
    {
      // catch browser handling that also uses variables
      // inherited from Object in for-in-loops
      if(typeof UpdateMutex.Wait[i] != 'object')
      {
        break;
      }
      // if key is not specified (or was set to zero in the
      // previous loop) return the object at this key
      if(!tid)
      {
        return UpdateMutex.Wait[i];
      }
      // if we found the key, we set the key to zero
      // so the next object in the queue is returned
      if(tid == i)
      {
        tid = 0;
      }
    }
    // if there is no next element, return null
    return null;
  }
  
  // constructor code
  this.threadObject = threadObj;
  this.out = o;
  this.lastId = lid;
  
  UpdateMutex.Wait[this.threadObject.id] = this;
    
  // set number to current timestamp
  this.enter = true;
  this.number = (new Date()).getTime();
  this.enter = false;
  
  // auto-start attempt to acquire mutex
  this.attempt(UpdateMutex.next(0));
}

