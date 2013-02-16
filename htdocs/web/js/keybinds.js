/* EtoA keybind navigation by river */

var keyNavigationOn = false;

var rightArrowKey = 39;
var leftArrowKey = 37;
var spaceBarKey = 32;
var enterKey = 13;

// array for all used keycodes
var keys = new Array();

// Initialize keybinding events
function keybindsInit() {

  if(window.enableKeybinds && $)
  {
    // disable keybinds if inside input or textarea
    if($(':focus').prop('tagName')==='INPUT' || $(':focus').prop('tagName')==='TEXTAREA')
    {
        keyNavigationOn = false;
    }
    else
    {
        keyNavigationOn = true;
    }
    // disable keybinds if input or textarea gets focus and re-enable on blur
    $('input,textarea').focus(function(e)
    {
        keyNavigationOn = false;
    });
    $('input,textarea').blur(function(e)
    {
        keyNavigationOn = true;
    });
    // add an event handler for keypress
    $('body').keypress(function(e)
    {
        // check whether keybinds are enabled
        if(keyNavigationOn && !e.metaKey && !e.shiftKey && !e.ctrlKey && !e.altKey)
        {
            // even jquery doesn't get all keycodes into one value,
            // so use the one that isn't zero
            var pressedKey = (e.which || e.keyCode);
            // change url if the pressed key is in our array
            if(keys[pressedKey])
            {
                window.location = keys[pressedKey];
                // prevent things like horizontal scrolling between
                // right arrow key pressed and new site loading
                e.preventDefault();
            }
        }
    });
  }
}

if(window.enableKeybinds && $)
{
    // catch undefined strings here, the keypress handler doesn't.
    keys[rightArrowKey]     = window.nextEntityUrl || "#";
    keys[leftArrowKey]      = window.prevEntityUrl || "#";
    //keys[enterKey]          = "chatframe.php"; // this results in a bug (multiple chats open)
    keys[spaceBarKey]       = "?page=overview";
    
    keys[104] /* 'h' */     = "?page=haven";
    keys[103] /* 'g' */     = "?page=buildings";
    keys[102] /* 'f' */     = "?page=research";
    keys[119] /* 'w' */     = "?page=shipyard";
    keys[100] /* 'd' */     = "?page=defense";
    keys[114] /* 'r' */     = "?page=missiles";
    keys[109] /* 'm' */     = "?page=market";
    keys[115] /* 's' */     = "?page=stats";
    keys[107] /* 'k' */     = "?page=galaxy";
    keys[110] /* 'n' */     = "?page=messages";
    keys[98]  /* 'b' */     = "?page=reports";
    keys[97]  /* 'a' */     = "?page=alliance";
    keys[118] /* 'v' */     = "?page=bookmarks";
    keys[108] /* 'l' */     = "?page=fleets";

    $(document).ready(keybindsInit);
}
