$(function() {
	// Load tabs
	$( ".tabs" ).tabs();

	// Load datepicker
	$( ".datepicker" ).datepicker({ dateFormat: 'dd.mm.yy', firstDay: 1,  monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'], dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'] });

	// Fix height to match sidebar (pos. absolute)
	$("#content").css('min-height', ($("#sidebar").outerHeight()));
});

function toggleBox(boxId)
{
	b = document.getElementById(boxId);
	if (b.style.display=='none')
	{
		b.style.display='';
	}
	else
	{
		b.style.display='none';
	}
}
		
function showLoader(elem)
{
	document.getElementById(elem).innerHTML='<div style=\"text-align:center;padding:10px;\"><img src="web/images/ajax-loader-circle.gif" /></div>';
}

function showLoaderPrepend(elem)
{
	document.getElementById(elem).innerHTML='<div style=\"text-align:center;padding:10px;\"><img src="web/images/ajax-loader-circle.gif" /></div>'+document.getElementById(elem).innerHTML;
}

function showLoaderInline(elem)
{
	document.getElementById(elem).innerHTML='<span style=\"text-align:center;padding:10px;\"><img src="web/images/ajax-loader-circle.gif" /></span>';
}
	