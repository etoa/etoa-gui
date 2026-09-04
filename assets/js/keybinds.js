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
  keys[spaceBarKey] = "/game/overview";

  keys[104] /* 'h' */ = "/game/haven/show";
  keys[103] /* 'g' */ = "/game/buildings";
  keys[102] /* 'f' */ = "/game/research";
  keys[119] /* 'w' */ = "/game/shipyard";
  keys[100] /* 'd' */ = "/game/defense";
  keys[114] /* 'r' */ = "/game/missiles";
  keys[109] /* 'm' */ = "/game/market/home";
  keys[115] /* 's' */ = "/game/stats/total";
  keys[107] /* 'k' */ = "/game/galaxy";
  keys[110] /* 'n' */ = "/game/messages/inbox";
  keys[98]  /* 'b' */ = "/game/reports/all";
  keys[97]  /* 'a' */ = "/game/alliance";
  keys[118] /* 'v' */ = "/game/bookmarks/target";
  keys[108] /* 'l' */ = "/game/fleets";
  keys[112] /* 'p' */ = "/game/economy";
  keys[252] /* 'ü' */ = "/game/fleetstats";

  shiftKeys[80] /* 'P' */ = "/game/planetstats";
  shiftKeys[86] /* 'V' */ = "/game/bookmarks/fleet";
  shiftKeys[66] /* 'B' */ = "/game/population";

  $(document).ready(keybindsInit);
}
