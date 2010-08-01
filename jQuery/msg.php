<?PHP
	 if (isset($_POST['userId']))
	 {
		  //TODO find a solution where u can use the function
		  $id = (int)$_POST['userId'];
		 // $nick = get_user_nick($id);
    }
    else
    {
        $nick = '';
    }

	 if (isset ($_POST['preview']))
	 {
		  $msgPreview = (bool)$_POST['preview'];
	 }
	 else
		  $msgPreview = false;


?>
<form name="msgform">
    <table style="width: 98%;" class="tb boxLayout">
        <tbody>
            <tr>
                <th colspan="20" class="infoboxtitle">Nachricht verfassen</th>
            </tr>
            <tr>
                <th width="50" valign="top">Empfänger:</th>
                <td width="250" colspan="2">
                    <input type="text" style="width: 330px;" maxlength="255" value="" autocomplete="off" id="user_nick" name="message_user_to"> Mehrere Empfänger mit ; trennen<br />
                </td>
            </tr>
            <tr>
                <th width="50" valign="top">Betreff:</th>
                <td width="250" colspan="2">
                    <input type="text" maxlength="255" style="width: 97%;" value="" id ="message_subject" name="message_subject">
                </td>
            </tr>
            <tr>
                <th width="50" valign="top">Text:</th>
                <td width="250"
                    <textarea onkeyup="text2html(this.value,'msgPreview');" cols="60" rows="10" id="message" name="message_text"></textarea>
                </td>
                <td>
                    <input type="button" style="font-weight: bold;" value="B" onclick="bbcode(this.form,'b','');text2html(document.getElementById('message').value,'msgPreview');">
                    <input type="button" style="font-style: italic;" value="I" onclick="bbcode(this.form,'i','');text2html(document.getElementById('message').value,'msgPreview');">
                    <input type="button" style="text-decoration: underline;" value="U" onclick="bbcode(this.form,'u','');text2html(document.getElementById('message').value,'msgPreview');">
                    <input type="button" style="text-align: center;" value="Center" onclick="bbcode(this.form,'c','');text2html(document.getElementById('message').value,'msgPreview');">
                    <br /><br />
                    <input type="button" value="Link" onclick="namedlink(this.form,'url');text2html(document.getElementById('message').value,'msgPreview');">
                    <input type="button" value="E-Mail" onclick="namedlink(this.form,'email');text2html(document.getElementById('message').value,'msgPreview');">
                    <input type="button" value="Bild" onclick="bbcode(this.form,'img','http://');text2html(document.getElementById('message').value,'msgPreview');">
                    <br /><br />
                    <select onclick="text2html(document.getElementById('message').value,'msgPreview');" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'size');" id="sizeselect">
                        <option value="0">Grösse</option>
                        <option value="7">winzig</option>
                        <option value="10">klein</option>
                        <option value="12">mittel</option>
                        <option value="16">groß</option>
                        <option value="20">riesig</option>
                    </select>
                    <select onclick="text2html(document.getElementById('message').value,'msgPreview');" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'color');" id="colorselect">
                        <option value="0">Farbe</option>
                        <option style="color: skyblue;" value="skyblue">sky blue</option>
                        <option style="color: royalblue;" value="royalblue">royal blue</option>
                        <option style="color: blue;" value="blue">blue</option>
                        <option style="color: darkblue;" value="darkblue">dark-blue</option>
                        <option style="color: orange;" value="orange">orange</option>
                        <option style="color: orangered;" value="orangered">orange-red</option>
                        <option style="color: crimson;" value="crimson">crimson</option>
                        <option style="color: red;" value="red">red</option>
                        <option style="color: firebrick;" value="firebrick">firebrick</option>
                        <option style="color: darkred;" value="darkred">dark red</option>
                        <option style="color: green;" value="green">green</option>
                        <option style="color: limegreen;" value="limegreen">limegreen</option>
                        <option style="color: seagreen;" value="seagreen">sea-green</option>
                        <option style="color: deeppink;" value="deeppink">deeppink</option>
                        <option style="color: tomato;" value="tomato">tomato</option>
                        <option style="color: coral;" value="coral">coral</option>
                        <option style="color: purple;" value="purple">purple</option>
                        <option style="color: indigo;" value="indigo">indigo</option>
                        <option style="color: burlywood;" value="burlywood">burlywood</option>
                        <option style="color: sandybrown;" value="sandybrown">sandy brown</option>
                        <option style="color: sienna;" value="sienna">sienna</option>
                        <option style="color: chocolate;" value="chocolate">chocolate</option>
                        <option style="color: teal;" value="teal">teal</option>
                        <option style="color: silver;" value="silver">silver</option>
                    </select>
                    <br /><br />
                    <a onclick="addText(':-)', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/smile.gif">
                    </a>&nbsp;
                    <a onclick="addText(';-)', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/wink.gif">
                    </a>&nbsp;
                    <a onclick="addText(':-P', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/tongue.gif">
                    </a>&nbsp;
                    <a onclick="addText(':0', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/laugh.gif">
                    </a>&nbsp;
                    <a onclick="addText(':-D', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/biggrin.gif">
                    </a>&nbsp;<br>
                    <a onclick="addText(':-(', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/frown.gif">
                    </a>&nbsp;
                    <a onclick="addText('8-)', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/cool.gif">
                    </a>&nbsp;
                    <a onclick="addText(':angry:', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/angry.gif">
                    </a>&nbsp;
                    <a onclick="addText(':sad:', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/sad.gif">
                    </a>&nbsp;
                    <a onclick="addText(':pst:', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/pst.gif">
                    </a>&nbsp;<br>
                    <a onclick="addText(':holy:', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/holy.gif">
                    </a>&nbsp;
                    <a onclick="addText(':rolleyes:', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/rolleyes.gif">
                    </a>&nbsp;
                    <a onclick="addText(':anger:', '', false, document.msgform);text2html(document.getElementById('message').value,'msgPreview');" href="javascript:;">
                        <img title="Smilie" alt="Smilie" style="border: medium none ;" src="images/smilies/anger.gif">
                    </a>&nbsp;
                </td>
            </tr>
<?PHP
    if ($msgPreview == 1)
    {
        ?>
            <tr>
                <th>Vorschau:</th>
                <td id="msgPreview" colspan="2"></td>
            </tr>
        <?PHP
    }
        ?>
        </tbody>
    </table>
    <script type="text/javascript">text2html(document.getElementById('message').value,'msgPreview');document.getElementById('user_nick').focus()</script>
    <input type="submit" onclick="if (document.getElementById('user_nick').value=='') {window.alert('Empfänger fehlt!');document.getElementById('user_nick').focus();return false;} else {xajax_sendMsg(document.getElementById('user_nick').value,document.getElementById('message_subject').value,document.getElementById('message').value);$.fancybox.close(); return false;}" value="Senden" name="submit">
</form>

<script type="text/javascript">
$(function() {

	var cache = {};
	function split(val) {
		return val.split(/,\s*/);
	}
	function extractLast(term) {
		return split(term).pop();
	}

	$("#user_nick").autocomplete({
		delay: 500,
		source: function(request, response) {
			request.term = extractLast(request.term);
			if ( request.term in cache ) {
				response( cache[ request.term ] );
				return;
			}

			$.ajax({
				url: "jQuery/user.php",
				dataType: "json",
				data: request,
				success: function( data ) {
					cache[ request.term ] = data;
					response( data );
				}
			});
		},
		search: function() {
			// custom minLength
			var term = extractLast(this.value);
			if (term.length < 2) {
				return false;
			}
		},
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function(event, ui) {
			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// check that every element is just once in the list
			terms = $.unique(terms);
			terms = $.unique(terms);
			// add placeholder to get the comma-and-space at the end
			terms.push("");
			this.value = terms.join(", ");
			return false;
		}
	});
});
</script>