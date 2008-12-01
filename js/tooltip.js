/*
* Simple tooltip for EtoA
*
* (C) 2008 Nicolas Perrenoud
*
* Needs:
*
* <div class="tooltip" id="tooltip" >
*  	<div class="tttitle" id="tttitle"></div>
*  	<div class="ttcontent" id="ttcontent"></div>
* </div> 
*
*
* Example (clickable):
*  <a href="?" onclick="showTT('fooTitle','fooContent',0,event,this);return false;" >A Link</a> 
* 
* Example (movable):
*  <a href="?" onmouseover="showTT('fooTitle','fooContent',1,event,this);" onmouseout="hideTT()">A Link</a> 
*
*/

wmtt = null;
wmttSender = null;
wmttMode = 0;

document.onmousemove = updateTT;
function updateTT(e) 
{
  if (wmtt != null && wmttMode==1) 
  {
    x = (document.all) ? window.event.x + wmtt.offsetParent.scrollLeft : e.pageX;
    y = (document.all) ? window.event.y + wmtt.offsetParent.scrollTop  : e.pageY;
    wmtt.style.left = (x + 20) + "px";
    wmtt.style.top   = (y + 20) + "px";
  }
}

document.onclick = hideTTCheck;
function hideTTCheck(e)
{
	if (wmtt && wmtt.style.display == "block")
	{
		elem = (e.srcElement) ? e.srcElement : e.target;
		if (wmttSender!= elem && elem != document.getElementById('tooltip') && !isParent(elem, document.getElementById('tooltip')))
			hideTT();
	}
}

function isParent(elem,reference)
{
	if (elem.parentNode)
	{
		if (elem.parentNode == reference)
			return true;
		return isParent(elem.parentNode,reference);
	}
	return false;	
}
 
function showTT(tTitle, tElement,mode,e,s) 
{
	wmttMode = mode;
	wmttSender = s;
 	wmtt = document.getElementById('tooltip');
 	wmtTitle = document.getElementById('tttitle');
 	wmtContent = document.getElementById('ttcontent');
 	
 	newContent = (document.getElementById(tElement)) ?  document.getElementById(tElement).innerHTML : tElement;
 	
	if (wmtt.style.display == "block" && wmtContent.innerHTML == newContent && wmtTitle.innerHTML == tTitle )
	{
		hideTT();
	}
	else
	{
		hideTT();
		if (tTitle!='' && tTitle!=null)
		{
			wmtTitle.innerHTML = tTitle;	
			wmtTitle.style.display='block';	
		}
		else
		{
			wmtTitle.style.display='none';	
		}

		wmtContent.innerHTML = newContent;

  	wmtt.style.display = "block";
  	
    x = (document.all) ? window.event.x + wmtt.offsetParent.scrollLeft : e.pageX;
    y = (document.all) ? window.event.y + wmtt.offsetParent.scrollTop  : e.pageY;
    wmtt.style.left = (x + 5) + "px";
    wmtt.style.top   = (y + 5) + "px";
	}
}

function hideTT() {
  wmtt.style.display = "none";
}
