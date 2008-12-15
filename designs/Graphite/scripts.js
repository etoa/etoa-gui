function hideAllHbarMenus()
{		
	if (document.getElementById('hbarmapslide').style.display=='') Effect.SlideUp('hbarmapslide',{duration:0.1}); 
	if (document.getElementById('hbarallianceslide').style.display=='') Effect.SlideUp('hbarallianceslide',{duration:0.1}); 
	if (document.getElementById('hbarsettingsslide').style.display=='') Effect.SlideUp('hbarsettingsslide',{duration:0.1}); 
	if (document.getElementById('hbarhelpslide').style.display=='') Effect.SlideUp('hbarhelpslide',{duration:0.1}); 
}