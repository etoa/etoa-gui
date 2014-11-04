<?PHP
	/* fastchat von river */

	define('RELATIVE_ROOT','');
	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
		<title>EtoA Chat</title>
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
	 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-language" content="de" />
		<link rel="stylesheet" type="text/css" href="web/css/vendor/jquery-ui.game.css" />
		<link rel="stylesheet" type="text/css" href="web/css/chat.css" />
		<script type="text/javascript" src="web/js/vendor/jquery.min.js"></script>
		<script type="text/javascript" src="web/js/vendor/jquery-ui.min.js"></script>
		<script type="text/javascript" src="web/js/main.js" ></script>
		<script type="text/javascript" src="web/js/chat.js" ></script>
	</head>
	<body>
		<?PHP
			if ( !isset($_SESSION['user_id']) )
			{
				echo('<p>Du bist nicht eingeloggt.</p>');
			}
			else
			{
				$res = dbquery("
				SELECT * FROM
					chat_banns
				WHERE
					user_id=".$_SESSION['user_id'].";");
				if (!isset($res)) {
					echo "<p>Irgend etwas lief schief. Versuche den Chat neu zu laden.</p>";
				}
				elseif (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_assoc($res);
					echo "<p>Du wurdest vom Chat gebannt!<br/><br/>
					<b>Grund:</b> ".$arr['reason']."<br/>
					"/*<b>Zeit:</b> ".df($arr['timestamp'])."</p>"*/;
				}
				else
				{
          $cu = new CurrentUser($_SESSION['user_id']);
          $_SESSION['ccolor'] = $cu->properties->chatColor;        
        
					?>
          <div id="tabs">
            <ul>
              <li><a href="#tabs-global">Global</a></li>
              <li><a href="#tabs-user">User</a></li>
            </ul>
            <div id="tabs-global">
              <div id="chatitems"></div>
            </div>
            <div id="tabs-user">
              <div id="userlist"></div>
            </div>
          </div>
          
          <div id="loading">
			<span id="loadingAnimation"><img src="web/images/ajax-loader-chat.gif" alt="Loading"></span>
			<span id="loadingMessage"></span>
			<div id="unread">&nbsp;</div>
          </div>
          <div id="chatinput">
            <form action="#" method="post" autocomplete="off" id="cform">
              <input type="text" id="ctext" name="ctext" value="" size="40" maxlength="255" style="color:#<?PHP echo $cu->properties->chatColor;?>"/><input type="button" id="sendButton" value="Chat" title="Text senden" />
              <input type="button" id="logoutButton" value="X" title="Chat schliessen"/>
            </form>
          </div>          
          
					<?PHP
				}
			}
			dbclose();
		?>
    <script>
    $(function() {
    
      //
      // Chat
      //
      
      // Event handlers
      $('#cform').submit(function() {
        sendChat();
        return false;
      });
      $('#sendButton').click(function(){
        sendChat();
      });
      $('#logoutButton').click(function(){
        logoutFromChat();
      });
      $('#ctext').keyup(function(event) {
        handleCTextKey(event);
      });
	  
	  // add/remove unread messages indicator on scrolling
	  $('#chatitems').scroll(function(){updateViewed();});
      
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

      // Enable tabs
      $( "#tabs" ).tabs();

      // Resize chat area
      function resizeUi() {
          var h = $(window).height();
          var w = $(window).width();

          $("#chatitems").css('height', $("#tabs").height() - $("#tabs ul").height() - 20);
      };
      var resizeTimer = null;
      $(window).bind('resize', function() {
          if (resizeTimer) clearTimeout(resizeTimer);
          resizeTimer = setTimeout(resizeUi, 100);
      });
      resizeUi();
    });    
    </script>    
	</body>
</html>
