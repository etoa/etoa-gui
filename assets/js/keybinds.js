/* EtoA keybind navigation by river */

var rightArrowKey = 39;
var leftArrowKey = 37;

//Fix for Bug in Webkit and IE (missing arrow keys support)
var rightArrowKeyAlternative = 50;
var leftArrowKeyAlternative = 49;

var spaceBarKey = 32;
var enterKey = 13;

// array for all used keycodes
var keys = new Array();
var shiftKeys = new Array();

// Initialize keybinding events
function keybindsInit() {

  if ($) {
    // add an event handler for keypress
    $('body').keypress(function (e) {
      // disable keybinds if inside input or textarea
      if (!($(e.target).prop('tagName') === 'INPUT' || $(e.target).prop('tagName') === 'TEXTAREA' || $(e.target).prop('tagName') === 'SELECT')) {
        // check whether keybinds are enabled
        if (!e.metaKey && !e.ctrlKey && !e.altKey) {
          // even jquery doesn't get all keycodes into one value,
          // so use the one that isn't zero
          var pressedKey = (e.which || e.keyCode);
          // change url if the pressed key is in our array
          if (!e.shiftKey && keys[pressedKey]) {
            window.location = keys[pressedKey];
            // prevent things like horizontal scrolling between
            // right arrow key pressed and new site loading
            e.preventDefault();
          }
          else if (e.shiftKey && shiftKeys[pressedKey]) {
            window.location = shiftKeys[pressedKey];
            // prevent things like horizontal scrolling between
            // right arrow key pressed and new site loading
            e.preventDefault();
          }
        }
      }
    });
  }
}

if ($) {
  // catch undefined strings here, the keypress handler doesn't.
  keys[rightArrowKey] = window.nextEntityUrl || "#";
  keys[rightArrowKeyAlternative] = window.nextEntityUrl || "#";
  keys[leftArrowKey] = window.prevEntityUrl || "#";
  keys[leftArrowKeyAlternative] = window.prevEntityUrl || "#";
  //keys[enterKey]          = "chatframe.php"; // this results in a bug (multiple chats open)
  keys[spaceBarKey] = "?page=overview";

  keys[104] /* 'h' */ = "?page=haven";
  keys[103] /* 'g' */ = "?page=buildings";
  keys[102] /* 'f' */ = "?page=research";
  keys[119] /* 'w' */ = "?page=shipyard";
  keys[100] /* 'd' */ = "?page=defense";
  keys[114] /* 'r' */ = "?page=missiles";
  keys[109] /* 'm' */ = "?page=market";
  keys[115] /* 's' */ = "?page=stats";
  keys[107] /* 'k' */ = "?page=galaxy";
  keys[110] /* 'n' */ = "?page=messages";
  keys[98]  /* 'b' */ = "?page=reports";
  keys[97]  /* 'a' */ = "?page=alliance";
  keys[118] /* 'v' */ = "?page=bookmarks";
  keys[108] /* 'l' */ = "?page=fleets";
  keys[112] /* 'p' */ = "?page=economy";
  keys[252] /* 'ü' */ = "?page=fleetstats";

  shiftKeys[80] /* 'P' */ = "?page=planetstats";
  shiftKeys[86] /* 'V' */ = "?page=bookmarks&mode=fleet";
  shiftKeys[66] /* 'B' */ = "?page=population";

  $(document).ready(keybindsInit);
}
