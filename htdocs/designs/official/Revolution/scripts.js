/* 
   ETOA Design Revolution by HeaX	
	umgesetzt durch :	ETOA-Team - Lukulus
	
	Stand: 02. Dezember 14
	Version: 0.9 Beta
*/

/* Eigene Scripte */

$(document).ready(function() {
	if (sessionStorage.chat) {
			$("#c_aside").replaceWith("<iframe id='c_aside' src='chat.php'></iframe>");
			document.getElementById("f_chat").style.display = "none";
			document.getElementById("f_chat_on").style.display = "inline";	
	}	
});


$("#f_chat").click(function(){
	$("#c_aside").replaceWith("<iframe id='c_aside' src='chat.php'></iframe>");
	document.getElementById("f_chat").style.display = "none";
	document.getElementById("f_chat_on").style.display = "inline";
	sessionStorage.chat = 1;
	console.log (sessionStorage.chat);
});


$("#f_chat_on").click(function(){
	sessionStorage.clear();
	$("#aside").load('{$templateDir}/template.html #c_aside');
	document.getElementById("f_chat_on").style.display = "none";
	document.getElementById("f_chat").style.display = "inline";
});


function js_planetlist(enable) {
	if (enable) {
		document.getElementById('planetlist').style.visibility='visible';
	}
	else {
		document.getElementById('planetlist').style.visibility='hidden';
	}
}
