<?PHP

	/**
	*	BB-Code Wrapper
	*
	* @param $string Text to wrap BB-Codes into HTML
	* @return Wrapped text
	*
	* @author MrCage | Nicolas Perrenoud
	*
	* @last editing: Demora | Selina Tanner 04.06.2007
	*/

	function text2html($string)
	{
		global $smilielist;

		$string = str_replace("  ", "&nbsp;&nbsp;", $string);

		$string = str_replace("\"", "&quot;", $string);
		$string = str_replace("<", "&lt;", $string);
		$string = str_replace(">", "&gt;", $string);
		
		$string =  preg_replace("((\r\n))", trim('<br/>'), $string);
		$string =  preg_replace("((\n))", trim('<br/>'), $string);
		$string =  preg_replace("((\r)+)", trim('<br/>'), $string);

		$string = str_replace('[b]', '<b>', $string);
		$string = str_replace('[/b]', '</b>', $string);
		$string = str_replace('[B]', '<b>', $string);
		$string = str_replace('[/B]', '</b>', $string);
		$string = str_replace('[i]', '<i>', $string);
		$string = str_replace('[/i]', '</i>', $string);
		$string = str_replace('[I]', '<i>', $string);
		$string = str_replace('[/I]', '</i>', $string);
		$string = str_replace('[u]', '<u>', $string);
		$string = str_replace('[/u]', '</u>', $string);
		$string = str_replace('[U]', '<u>', $string);
		$string = str_replace('[/U]', '</u>', $string);
		$string = str_replace('[c]', '<div style="text-align:center;">', $string);
		$string = str_replace('[/c]', '</div>', $string);
		$string = str_replace('[C]', '<div style="text-align:center;">', $string);
		$string = str_replace('[/C]', '</div>', $string);
		$string = str_replace('[bc]', '<blockquote class="blockquotecode"><code>', $string);
		$string = str_replace('[/bc]', '</code></blockquote>', $string);
		$string = str_replace('[BC]', '<blockquote class="blockquotecode"><code>', $string);
		$string = str_replace('[/BC]', '</code></blockquote>', $string);


		$string = str_replace('[h1]', '<h1>', $string);
		$string = str_replace('[/h1]', '</h1>', $string);
		$string = str_replace('[H1]', '<h1>', $string);
		$string = str_replace('[/H1]', '</h1>', $string);
		$string = str_replace('[h2]', '<h2>', $string);
		$string = str_replace('[/h2]', '</h2>', $string);
		$string = str_replace('[H2]', '<h2>', $string);
		$string = str_replace('[/H2]', '</h2>', $string);
		$string = str_replace('[h3]', '<h3>', $string);
		$string = str_replace('[/h3]', '</h3>', $string);
		$string = str_replace('[H3]', '<h3>', $string);
		$string = str_replace('[/H3]', '</h3>', $string);

		$string = str_replace('[center]', '<div style="text-align:center">', $string);
		$string = str_replace('[/center]', '</div>', $string);
		$string = str_replace('[right]', '<div style="text-align:right">', $string);
		$string = str_replace('[/right]', '</div>', $string);		
		$string = str_replace('[headline]', '<div style="text-align:center"><b>', $string);
		$string = str_replace('[/headline]', '</b></div>',$string);
		
		$string = str_replace('[CENTER]', '<div style="text-align:center">', $string);
		$string = str_replace('[/CENTER]', '</div>', $string);
		$string = str_replace('[RIGHT]', '<div style="text-align:right">', $string);
		$string = str_replace('[/RIGHT]', '</div>', $string);
		$string = str_replace('[HEADLINE]', '<div style="text-align:center"><b>', $string);
		$string = str_replace('[/HEADLINE]', '</b></div>',$string);

		$string = str_replace('[*]', '<li>', $string);
		$string = str_replace('[/*]', '</li>', $string);

		$string = eregi_replace('\[list=1]([^\[]*)\[/list\]', '<ol style="list-style-type:decimal">\1</ol>', $string);
		$string = eregi_replace('\[list=a]([^\[]*)\[/list\]', '<ol style="list-style-type:lower-latin">\1</ol>', $string);
		$string = eregi_replace('\[list=a]([^\[]*)\[/list\]', '<ol style="list-style-type:lower-latin">\1</ol>', $string);
		$string = eregi_replace('\[list=I]([^\[]*)\[/list\]', '<ol style="list-style-type:upper-roman">\1</ol>', $string);
		$string = eregi_replace('\[list=i]([^\[]*)\[/list\]', '<ol style="list-style-type:upper-roman">\1</ol>', $string);

		$string = eregi_replace('\[page=([^\[]*)\]([^\[]*)\[/page\]', '<a href="?page=\1">\2</a>', $string);


		$string = str_replace('[list]', '<ul>', $string);
		$string = str_replace('[/list]', '</ul>', $string);
		$string = str_replace('[nlist]', '<ol style="list-style-type:decimal">', $string);
		$string = str_replace('[/nlist]', '</ol>', $string);
		$string = str_replace('[alist]', '<ol style="list-style-type:lower-latin">', $string);
		$string = str_replace('[/alist]', '</ol>', $string);
		$string = str_replace('[rlist]', '<ol style="list-style-type:upper-roman">', $string);
		$string = str_replace('[/rlist]', '</ol>', $string);
		
		$string = str_replace('[LIST]', '<ul>', $string);
		$string = str_replace('[/LIST]', '</ul>', $string);
		$string = str_replace('[NLIST]', '<ol style="list-style-type:decimal">', $string);
		$string = str_replace('[/NLIST]', '</ol>', $string);
		$string = str_replace('[ALIST]', '<ol style="list-style-type:lower-latin">', $string);
		$string = str_replace('[/ALIST]', '</ol>', $string);
		$string = str_replace('[RLIST]', '<ol style="list-style-type:upper-roman">', $string);
		$string = str_replace('[/RLIST]', '</ol>', $string);
		
		$string = str_replace('[element]', '<li>', $string);
		$string = str_replace('[/element]', '</li>', $string);
		$string = str_replace('[ELEMENT]', '<li>', $string);
		$string = str_replace('[/ELEMENT]', '</li>', $string);
		
		$string = str_replace('[line]', '<hr class="line" />', $string);
		$string = str_replace('[LINE]', '<hr class="line" />', $string);

		$string = eregi_replace('\[quote]([^\[]*)\[/quote\]', '<fieldset class="quote"><legend class="quote"><b>Zitat</b></legend>\1</fieldset>', $string);
		$string = eregi_replace('\[quote ([^\[]*)\]([^\[]*)\[/quote\]', '<fieldset class="quote"><legend class="quote"><b>Zitat von:</b> \1</legend>\2</fieldset>', $string);
		$string = eregi_replace('\[quote=([^\[]*)\]([^\[]*)\[/quote\]', '<fieldset class="quote"><legend class="quote"><b>Zitat von:</b> \1</legend>\2</fieldset>', $string);
		$string = eregi_replace('\[img\]([^\[]*)\[/img\]', '<img src="\1" alt="\1" border="0" />', $string);
		$string = eregi_replace('\[img ([0-9]*) ([0-9]*)\]([^\[]*)\[/img]', '<img src="\3" alt="\3" width="\1" height="\2" border="0" />', $string);
		$string = eregi_replace('\[img ([0-9]*)\]([^\[]*)\[/img]', '<img src="\2" alt="\2" width="\1" border="0" />', $string);
		$string = eregi_replace('\[flag ([^\[]*)\]', '<img src="images/flags/'.strtolower('\1').'.gif" border="0" alt="Flagge \1" class=\"flag\" />', $string);
		$string = eregi_replace('\[thumb ([0-9]*)\]([^\[]*)\[/thumb]', '<a href="\2"><img src="\2" alt="\2" width="\1" border="0" /></a>', $string);

		$string = eregi_replace("^http://([^ ,\n]*)", "[url]http://\\1[/url]", $string);
		$string = eregi_replace("^ftp://([^ ,\n]*)", "[url]ftp://\\1[/url]", $string);
		$string = eregi_replace("^www\\.([^ ,\n]*)", "[url]http://www.\\1[/url]", $string);

		$string = eregi_replace('\[url=([^\[]*)\]([^\[]*)\[/url\]', '<a href="\1">\2</a>', $string);
		$string = eregi_replace('\[url ([^\[]*)\]([^\[]*)\[/url\]', '<a href="\1">\2</a>', $string);
	 	$string = eregi_replace('\[url\]www.([^\[]*)\[/url\]', '<a href="http://www.\1">\1</a>', $string);
		$string = eregi_replace('\[url\]([^\[]*)\[/url\]', '<a href="\1">\1</a>', $string);

		$string = eregi_replace('\[mailurl=([^\[]*)\]([^\[]*)\[/mailurl\]', '<a href="mailto:\1">\2</a>', $string);
		$string = eregi_replace('\[mailurl ([^\[]*)\]([^\[]*)\[/mailurl\]', '<a href="mailto:\1">\2</a>', $string);
		$string = eregi_replace('\[mailurl\]([^\[]*)\[/mailurl\]', '<a href="mailto:\1">\1</a>', $string);
		$string = eregi_replace('\[email=([^\[]*)\]([^\[]*)\[/email\]', '<a href="mailto:\1">\2</a>', $string);
		$string = eregi_replace('\[email ([^\[]*)\]([^\[]*)\[/email\]', '<a href="mailto:\1">\2</a>', $string);
		$string = eregi_replace('\[email\]([^\[]*)\[/email\]', '<a href="mailto:\1">\1</a>', $string);

		$string = str_replace('[table]', '<table class="bbtable">', $string);
    $string = str_replace('[/table]', '</table>', $string);
    $string = str_replace('[td]', '<td>', $string);
    $string = str_replace('[/td]', '</td>', $string);
    $string = str_replace('[th]', '<th>', $string);
    $string = str_replace('[/th]', '</th>', $string);
    $string = str_replace('[tr]', '<tr>', $string);
    $string = str_replace('[/tr]', '</tr>', $string);
    
    $string = str_replace('[TABLE]', '<table>', $string);
    $string = str_replace('[/TABLE]', '</table>', $string);
    $string = str_replace('[TD]', '<td>', $string);
    $string = str_replace('[/TD]', '</td>', $string);
    $string = str_replace('[TH]', '<th>', $string);
    $string = str_replace('[/TH]', '</th>', $string);
    $string = str_replace('[TR]', '<tr>', $string);
    $string = str_replace('[/TR]', '</tr>', $string);

		foreach ($smilielist as $smilie_id=>$smilie_img)
		{
			$string = str_replace('['.$smilie_id.']', '<img src="'.SMILIE_DIR.'/'.$smilie_img.'" style="border:none;" alt="Smilie" title="'.$smilie_img.'" />', $string);
		}

		$string = eregi_replace('\[font ([^\[]*)\]', '<font style=\"font-family:\1">', $string);
		$string = eregi_replace('\[color ([^\[]*)\]', '<font style=\"color:\1">', $string);
		$string = eregi_replace('\[size ([^\[]*)\]', '<font style=\"font-size:\1pt">', $string);
		$string = eregi_replace('\[font=([^\[]*)\]', '<font style=\"font-family:\1">', $string);
		$string = eregi_replace('\[color=([^\[]*)\]', '<font style=\"color:\1">', $string);
		$string = eregi_replace('\[size=([^\[]*)\]', '<font style=\"font-size:\1pt">', $string);
		$string = str_replace('[/font]', '</font>', $string);
		$string = str_replace('[/FONT]', '</font>', $string);
		$string = str_replace('[/color]', '</font>', $string);
		$string = str_replace('[/COLOR]', '</font>', $string);
		$string = str_replace('[/size]', '</font>', $string);
		$string = str_replace('[/SIZE]', '</font>', $string);

 		$string = stripslashes($string);

		//$string=htmlentities($string);

		$imgpacket_path = "images/imagepacks/Discovery";
		$imgpacket_ext = "png";
	
		$res = dbquery("
		SELECT 
			ship_id as id,
			ship_name as name,
			ship_shortcomment as cmt,
			cat_name as cat,
			cat_color as color,
			ship_structure,
			ship_shield,
			ship_weapon,
			race_name
		FROM
			ships
		INNER JOIN
			ship_cat
			ON ship_cat_id=cat_id
		LEFT JOIN
			races ON ship_race_id=race_id			
		;");
		while ($arr=mysql_fetch_array($res))
		{
			$tm = '<div style="background:url('.$imgpacket_path.'/ships/ship'.$arr['id'].'_small.'.$imgpacket_ext.') right top no-repeat;">';
			$tm .='<span style="color:'.$arr['color'].'">'.$arr['name']."</span><br/>".$arr['cat'];
			if ($arr['race_name']!="")
				$tm.= '<br/><span style="color:#EF0E1C">Rasse: '.$arr['race_name']."</span>";
			$tm.="<br/>Struktur: ".nf($arr['ship_structure'])." <br/>Schilder: ".nf($arr['ship_shield'])." <br/>Waffen: ".nf($arr['ship_weapon'])." <br/>";
			$tm .='<span style="color:#FFD517">'.$arr['cmt']."</span></div>";
			$string = eregi_replace('\[ship '.$arr['id'].'\]', '<span style="color:'.$arr['color'].'" class="itemTooltip" '.tt($tm).'>[<a href="?page=help&site=shipyard&id='.$arr['id'].'" style="color:'.$arr['color'].'">'.$arr['name'].'</a>]</span>', $string);
		}
		$string = eregi_replace('\[ship ([^\[]*)\]', '<span style="color:'.$arr['color'].'" class="itemTooltip" '.tt('<span style="color:#999">Ungültige Schiff-ID!</span>').'>[Ungültiges Schiff]</span>', $string);
		
		$res = dbquery("
		SELECT 
			def_id as id,
			def_name as name,
			def_shortcomment as cmt,
			cat_name as cat,
			cat_color as color,
			def_structure,
			def_shield,
			def_weapon,
			def_heal,
			def_fields,
			race_name
		FROM
			defense
		INNER JOIN
			def_cat
			ON def_cat_id=cat_id
		LEFT JOIN
			races ON def_race_id=race_id
		;");
		while ($arr=mysql_fetch_array($res))
		{
			$tm = '<div style="background:url(images/imagepacks/Discovery/defense/def'.$arr['id'].'_small.gif) right top no-repeat;">';
			$tm .='<span style="color:'.$arr['color'].'">'.$arr['name']."</span><br/>".$arr['cat'];
			if ($arr['race_name']!="")
				$tm.= '<br/><span style="color:#EF0E1C">Rasse: '.$arr['race_name']."</span>";
			
			if ($arr['def_structure']>0)
				$tm.= "<br/>Struktur: ".nf($arr['def_structure']);
			if ($arr['def_shield']>0)
				$tm.=	"<br/>Schilder: ".nf($arr['def_shield']);
			if ($arr['def_weapon']>0)
				$tm.= "<br/>Waffen: ".nf($arr['def_weapon']);
			if ($arr['def_heal']>0)
				$tm.= "<br/>Reparatur: ".nf($arr['def_heal']);
			if ($arr['def_fields']>0)
				$tm.= "<br/>Felderverbrauch: ".nf($arr['def_fields']);
			$tm .='<br/><span style="color:#FFD517">'.$arr['cmt']."</span></div>";
			$string = eregi_replace('\[def '.$arr['id'].'\]', '<span style="color:'.$arr['color'].'" class="itemTooltip" '.tt($tm).'>[<a href="?page=help&site=shipyard&id='.$arr['id'].'" style="color:'.$arr['color'].'">'.$arr['name'].'</a>]</span>', $string);
		}
		$string = eregi_replace('\[def ([^\[]*)\]', '<span style="color:'.$arr['color'].'" class="itemTooltip" '.tt('<span style="color:#999">Ungültige Verteidigungs-ID!</span>').'>[Ungültiges Verteidigung]</span>', $string);
	
	

		return $string;
	}
	
$flaglist['ch-la']="Langenthal";
$flaglist['ch-ag']="Kanton Aargau";
$flaglist['ch-ai']="Kanton Appenzell-Innerrhoden";
$flaglist['ch-ar']="Kanton Appenzell-Ausserrhoden";
$flaglist['ch-be']="Kanton Bern";
$flaglist['ch-bl']="Kanton Basel-Landschaft";
$flaglist['ch-bs']="Kanton Basel-Stadt";
$flaglist['ch-ge']="Kanton Genf";
$flaglist['ch-gr']="Kanton Graub&uuml;nden";
$flaglist['ch-ju']="Kanton Jura";
$flaglist['ch-lu']="Kanton Luzern";
$flaglist['ch-nw']="Kanton Nidwalden";
$flaglist['ch-ow']="Kanton Obwalden";
$flaglist['ch-sh']="Kanton Schaffhausen";
$flaglist['ch-so']="Kanton Solothurn";
$flaglist['ch-sz']="Kanton Schwyz";
$flaglist['ch-tg']="Kanton Thurgau";
$flaglist['ch-ti']="Kanton Tessin";
$flaglist['ch-ur']="Kanton Uri";
$flaglist['ch-vd']="Kanton Waadt";
$flaglist['ch-vs']="Kanton Wallis";
$flaglist['ch-zg']="Kanton Zug";
$flaglist['ch-zh']="Kanton Z&uuml;rich";
$flaglist['ar']="Argentinien";
$flaglist['at']="&Ouml;sterreich";
$flaglist['au']="Australien";
$flaglist['be']="Belgien";
$flaglist['benelux']="Benelux";
$flaglist['bg']="Bulgarien";
$flaglist['br']="Brasilien";
$flaglist['ca']="Kanada";
$flaglist['ch']="Schweiz";
$flaglist['cn']="China";
$flaglist['hr']="Kroatien";
$flaglist['cz']="Tschechische Republik";
$flaglist['de']="Deutschland";
$flaglist['dk']="D&auml;nemark";
$flaglist['ee']="Estland";
$flaglist['eu']="Europa";
$flaglist['fi']="Finnland";
$flaglist['fr']="Frankreich";
$flaglist['gb']="Grossbritanien";
$flaglist['gr']="Griechenland";
$flaglist['il']="Israel";
$flaglist['in']="India";
$flaglist['it']="Italien";
$flaglist['jp']="Japan";
$flaglist['kp']="Korea";
$flaglist['lv']="Lettland";
$flaglist['lu']="Luxemburg";
$flaglist['nl']="Niederlande";
$flaglist['no']="Norwegen";
$flaglist['pl']="Polen";
$flaglist['ru']="Russland";
$flaglist['sk']="Slovakei";
$flaglist['sp']="Spanien";
$flaglist['se']="Schweden";
$flaglist['ty']="T&uuml;rkey";
$flaglist['us']="USA";
$flaglist['vn']="Vietnam";
$flaglist['world']="Welt";

$colorlist['black']="Schwarz";
$colorlist['darkred']="Dunkelrot";
$colorlist['red']="Rot";
$colorlist['orange']="Orange";
$colorlist['brown']="Braun";
$colorlist['yellow']="Gelb";
$colorlist['green']="Gr&uuml;n";
$colorlist['olive']="Olive";
$colorlist['cyan']="Cyan";
$colorlist['blue']="Blau";
$colorlist['darkblue']="Dunkelblau";
$colorlist['indigo']="Indigo";
$colorlist['violet']="Violet";
$colorlist['white']="Weiss";

$sizelist['8']="Klein";
$sizelist['10']="Mittel";
$sizelist['12']="Mittelgross";
$sizelist['14']="Gross";
$sizelist['17']="Ganz gross";

$smilielist=array();

$smilielist[':)']="smile.gif";
$smilielist[':-)']="smile.gif";
$smilielist[';)']="wink.gif";
$smilielist[';-)']="wink.gif";
$smilielist[':p']="tongue.gif";
$smilielist[':-p']="tongue.gif";
$smilielist[':0']="laugh.gif";
$smilielist[':angry:']="angry.gif";
$smilielist[':sad:']="sad.gif";
$smilielist[':anger:']="anger.gif";
$smilielist[':pst:']="pst.gif";
$smilielist[':D']="biggrin.gif";
$smilielist[':-D']="biggrin.gif";
$smilielist[':holy:']="holy.gif";
$smilielist[':cool:']="cool.gif";
$smilielist['8)']="cool.gif";
$smilielist['8-)']="cool.gif";
$smilielist[':rolleyes:']="rolleyes.gif";
$smilielist[':(']="frown.gif";
$smilielist[':-(']="frown.gif";
	
	
?>