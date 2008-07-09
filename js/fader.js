/* ********************************************************************
* Buffered Text-fade Effect - v3.0
* - Copyright 2008 - Licenced for free distribution under the BSDL
*   - http://www.opensource.org/licenses/bsd-license.php
*
*   This program is licenced under the BSDL and may be distributed far
* and wide, anywhere on the planet and beyond (maybe!)  If you happen
* to get a kick out of this script, please drop me a note at:
* wyvern@greywyvern.com and tell me where you gave it a good home and
* plenty of bytes to eat, hmmm? :)  I'd be eternally grateful.
*
* Description:
*   A javascript object which allows you to direct text fades within an
* HTML page.  Fades received before the previous fade has completed are
* queued rather than displayed immediately.  This keeps all fade
* animations smooth.
*
* Changelog:
*  3.0  - Buffer for each fadeObject is stored separately for stability
*         and speed.
*       - Major structure changes.  Different arguments
*
* Support:
*   Opera   - Yes
*   IE 6    - Yes
*   IE 5.5  - Yes
*   IE 5.01 - Fails
*   Firefox - Yes
*   Safari  - Yes
*
***********************************************************************
* I) Setting Up
*
*   Copy the javascript from this page into an external .js file or
* into the <script> tag of your own HTML page.  You only need the code
* between the "Begin" and "End" lines.  This shouldn't be that
* difficult, but you wouldn't believe the kind of mail I get about
* this!  Just do it, okay?  Jeez.
*
*   a) The Fade Object
*   After that's done we need to create a fade object.  We do this by
* calling the fadeObj function with a number of arguments.  We'll use
* the default example included in the script: fader[0].  You can delete
* the fader[1] lines if you like.
*
* fader[0] = new fadeObj('fade0', 'dddddd', '000000', 20, 20);
*
*   The first argument ('fade0') is the id of the HTML tag which will
* be displaying the fading effect.  Usually you'll want to apply some
* height and width styles to this element, since it starts out with no
* text by default and will probably be collapsed.  You don't want it
* jumping around when the script writes new text to it.
*
*   The next two values are hexidecimal colour values, WITHOUT the
* preceding #.  The first value is the starting colour, or the colour
* the text is before it fades in.  The second value is the ending
* colour, or the colour the text will be when it finishes fading in.
*
*   The last two values are two integers which indicate the number of
* "steps" the script must take to complete a fade-in and a fade-out
* respectively.  With a value of 20 like in the example above, there
* will be 20 colour changes before the text is fully faded-in or faded-
* out.  The lower these numbers, the faster the fade will be.
*
*   b) The Fade Messages
*   After setting up our fade object, all we need to do now is write
* out all of the messages which will be displayed in this element.
* Remember that this script only affects text of a single colour.
* Images and multi-coloured text won't work.
*
*   Messages are included in the msg[] array.  Simply assign each
* message a number, like so:
*
* fader[0].msg[1] = "Fade text, message one.";
*
*   Each fade object can have as many messages as you want, and be in
* any numerical order.  You can even skip numbers, but note that if you
* use the fade() method pointed at a message number which doesn't
* exist, you will get an error.
*
* The msg[] array should start at element [1].  If you would like a
* default message to appear if there are no more fade-in events in the
* queue, assign the default message to element [0].
*
***********************************************************************
* II) The Events
*
*   Fades can be triggered by any DOM event, but most likely you'll be
* using mouseover and mouseout events.  I used those events as examples
* below.
*
*   To trigger a fade, you use the fade() method to add a fade action
* to the queue.  The reason we use a queue, is so that you can add this
* fade, even if another fade is already happening.  The fade() method
* takes two important arguments:
*
* Example: onmouseover="fader[0].fade(1, true);"
*
*   In this example we are still referencing fade object fader[0].
*
*   The first argument is the message this command refers to.  This one
* has been associated with msg[1] of fader[0].
*09.07.2008 14:20:14
*   The second argument indicates the direction of the fade.
*     -> true = fade in, false = fade out.
*
*   Examine the working example script to see how these events were
* placed on the <td> elements below.
*
***********************************************************************
* III) Tips
*
*   - All the text in each msg[] variable MUST be on one line in the
* code.  That means this:
*
*   fader[0].msg[1] = "Fader zero,
* message one";
*
* ... is not allowed!  The text should wrap automatically when it's
* displayed on your HTML page, but you can force line breaks with the
* <br> tag.  (If you have some Javascript experience you'll know how to
* get around this).
*
*   - If you're placing the fading text on a background image, make
* the starting colour an average sample of the background instead of
* just black or white.  This will enhance the "disappearing" effect.
*
*   - The script can only fade text, but can accept non-graphical HTML
* tags in which CSS text color is inherited, such as <p>, <table> (no
* borders), <strong> and <em>.  Use these tags to add structure and a
* simple text layout to your fades.
*
*   - To have links fade along with with the surrounding text, apply
* the CSS style: color:inherit !important; to all links within the fade
* text messages.
*
***********************************************************************
* That's it!  Isn't that amazing!?! :)
*
* If you have any problems with this script, don't hesitate to email me
* at wyvern@greywyvern.com and I'll be happy to answer your matter-of-
* life-and-death questions!  Cheers!
******************************************************************** */


/* ***** Begin ***************************************************** */
function fadeObject(id, c1, c2, s1, s2) {
  var self = this;
  this.id      = id;
  this.elem    = false;
  this.colour  = {
    stt: [parseInt(c1.substr(0, 2), 16), parseInt(c1.substr(2, 2), 16), parseInt(c1.substr(4, 2), 16)],
    end: [parseInt(c2.substr(0, 2), 16), parseInt(c2.substr(2, 2), 16), parseInt(c2.substr(4, 2), 16)],
    now: [parseInt(c1.substr(0, 2), 16), parseInt(c1.substr(2, 2), 16), parseInt(c1.substr(4, 2), 16)]
  };
  this.steps   = [s1, s2];
  this.dir     = false;
  this.active  = false;
  this.queue   = [];
  this.msg     = [];
  this.message = 0;
  function d2h(num) {
    num = Math.round(num);
    return ((num < 16) ? "0" : "") + num.toString(16);
  }
  this.fade = function(message, direction) {
    this.elem = this.elem || document.getElementById(this.id);
    this.queue.push([message, direction]);
    for (var x = 0; x < this.queue.length; x++) {
      for (var y = x + 1; y < this.queue.length; y++) {
        if (this.queue[x][0] == this.queue[y][0] && this.queue[x][1] != this.queue[y][1]) {
          this.queue.splice(x, 1);
          this.queue.splice(y - 1, 1);
        }
      }
    }
    if (!this.active) setTimeout(function() { self.fadeLoop(); }, 0);
  };
  this.fadeLoop = function() {
    if (!this.active && this.queue.length) {
      if (this.dir && this.message != this.queue[0][0]) this.queue.unshift([this.message, false]);
      var msg = this.queue.shift();
      if (this.msg[msg[0]]) {
        this.active = true;
        this.elem.innerHTML = this.msg[this.message = msg[0]];
        this.dir = msg[1];
      }
    }
    if (this.dir) {
      var c1 = this.colour.stt, c2 = this.colour.end, s = this.steps[0];
    } else var c1 = this.colour.end, c2 = this.colour.stt, s = this.steps[1];
    for (var x = 0, cnow = "", inc = 0; x < 3; x++) {
      this.colour.now[x] += inc = (c2[x] - c1[x]) / s;
      cnow += this.colour.now[x] = (inc < 0) ? Math.max(this.colour.now[x], c2[x]) : Math.min(this.colour.now[x], c2[x]);
    } this.elem.style.color = "#" + d2h(this.colour.now[0]) + d2h(this.colour.now[1]) + d2h(this.colour.now[2]);
    if (cnow == c2.join("")) {
      this.active = false;
      if (!this.queue.length) {
        if (!this.dir) {
          if (this.msg[0]) {
            this.queue.push([0, true]);
            setTimeout(function() { self.fadeLoop(); }, 0);
          } else this.elem.innerHTML = "&nbsp;";
        }
      } else setTimeout(function() { self.fadeLoop(); }, 0);
    } else setTimeout(function() { self.fadeLoop(); }, 0);
  };
  if (window.addEventListener) {
    window.addEventListener('load', function() { self.fade(0, true); }, false); 
  } else if (window.attachEvent)
    window.attachEvent('onload', function() { self.fade(0, true); });
}
/* ***** End ******************************************************* */




/* *****
 * User defined fade objects and messages
 *
 * These messages are used in fades triggered by mouseovers and
 * mouseouts on table cells.  They are the simplest type of fade and
 * require no extra Javascript code.
 */
var fader = new Array();


/* *****
 * The code below describes how to make a throbbing or automatic fade
 * sequence of messages.  It is important to note that this function is
 * NOT part of the Buffered Text-Fade Effect, but merely an example of
 * how it can be used.  In this example, the throb() function controls
 * the commands which are sent to the fade engine; it is called
 * repeatedly at set time intervals rather than using mouseover events
 * as triggers.
 *
 * Notes:
 * - A global array "hash" is used to keep track of where each
 *   animation is currently in the sequence.
 * - The list of messages defined in the fader *must* start at one (1)
 *   and count upwards without skipping any integers.
 * - The third line of the throb() function controls how fast
 *   commands get sent to the fade engine.  It waits only 100 milli-
 *   seconds when fading out, but 5000 milliseconds (5 seconds) when
 *   fading in; this means the message will remain visible for about 5
 *   seconds before fading out again.
 *
 * Other types of fade animation are possible simply by designing
 * different ways to control the fade-ins and fade-outs!
 */
var hash = new Array();
function throb(item) {

  // If the hash array does not have an entry for this item, initialise it at 2
  if (!hash[item]) hash[item] = 2;

  // Send a fade command using the hash array to tell us what parameters we should use
  fader[item].fade(Math.floor(hash[item] / 2), !(hash[item] % 2));

  // Call this function again for this same item after a certain amount of time
  setTimeout(function() { throb(item); }, (hash[item] % 2) ? 100 : 2000);

  // If we have exceeded the number of messages in this fader, start over again at 2
  if (++hash[item] > fader[item].msg.length * 2 - 1) hash[item] = 2;
}
