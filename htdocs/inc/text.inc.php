<?PHP

	/**
	*	BB-Code Wrapper
	*
	* @param string $string Text to wrap BB-Codes into HTML
	* @return string
	*
	* @author MrCage | Nicolas Perrenoud
	*
	* @last editing: Lamborghini 14.04.2009
	* Die gleiche Funktion gibt es in Javscript, diese immer mit anpassen bei �nderungen!
	*/

	function text2html($string)
	{
		$string = str_replace("	 ", "&nbsp;&nbsp;", $string);

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

		$string = preg_replace('#\[list=1]([^\[]*)\[/list\]#i', '<ol style="list-style-type:decimal">\1</ol>', $string);
		$string = preg_replace('#\[list=a]([^\[]*)\[/list\]#i', '<ol style="list-style-type:lower-latin">\1</ol>', $string);
		$string = preg_replace('#\[list=a]([^\[]*)\[/list\]#i', '<ol style="list-style-type:lower-latin">\1</ol>', $string);
		$string = preg_replace('#\[list=I]([^\[]*)\[/list\]#i', '<ol style="list-style-type:upper-roman">\1</ol>', $string);
		$string = preg_replace('#\[list=i]([^\[]*)\[/list\]#i', '<ol style="list-style-type:upper-roman">\1</ol>', $string);

		$string = preg_replace('#\[page=([^\[]*)\]([^\[]*)\[/page\]#i', '<a href="?page=\1">\2</a>', $string);


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

		$string = preg_replace('#\[quote]([^\[]*)\[/quote\]#i', '<fieldset class="quote"><legend class="quote"><b>Zitat</b></legend>\1</fieldset>', $string);
		$string = preg_replace('#\[quote ([^\[]*)\]([^\[]*)\[/quote\]#i', '<fieldset class="quote"><legend class="quote"><b>Zitat von:</b> \1</legend>\2</fieldset>', $string);
		$string = preg_replace('#\[quote=([^\[]*)\]([^\[]*)\[/quote\]#i', '<fieldset class="quote"><legend class="quote"><b>Zitat von:</b> \1</legend>\2</fieldset>', $string);
		$string = preg_replace('#\[img\]([^\[]*)\[/img\]#i', '<img src="\1" alt="\1" border="0" />', $string);
		$string = preg_replace('#\[img ([0-9]*) ([0-9]*)\]([^\[]*)\[/img]#i', '<img src="\3" alt="\3" width="\1" height="\2" border="0" />', $string);
		$string = preg_replace('#\[img ([0-9]*)\]([^\[]*)\[/img]#i', '<img src="\2" alt="\2" width="\1" border="0" />', $string);
		$string = preg_replace('#\[flag ([^\[]*)\]#i', '<img src="images/flags/'.strtolower('\1').'.gif" border="0" alt="Flagge \1" class=\"flag\" />', $string);
		$string = preg_replace('#\[thumb ([0-9]*)\]([^\[]*)\[/thumb]#i', '<a href="\2"><img src="\2" alt="\2" width="\1" border="0" /></a>', $string);

		$string = preg_replace("#^http://([^ ,\n]*)#i", "[url]http://\\1[/url]", $string);
		$string = preg_replace("#^ftp://([^ ,\n]*)#i", "[url]ftp://\\1[/url]", $string);
		$string = preg_replace("#^www\\.([^ ,\n]*)#i", "[url]http://www.\\1[/url]", $string);

		$string = preg_replace_callback('#\[url=([^\[]*)\]([^\[]*)\[/url\]#i', 'bbcode_urls_to_links_with_newtab', $string);
		$string = preg_replace_callback('#\[url ([^\[]*)\]([^\[]*)\[/url\]#i', 'bbcode_urls_to_links_with_newtab', $string);
		$string = preg_replace_callback('#\[url\]([^\[]*)\[/url\]#i', 'bbcode_urls_to_links_with_newtab', $string);
		$string = preg_replace_callback('#\[page ([^\[]*)\]([^\[]*)\[/page\]#i', 'bbcode_page_to_links', $string);

		$string = preg_replace('#\[mailurl=([^\[]*)\]([^\[]*)\[/mailurl\]#i', '<a href="mailto:\1">\2</a>', $string);
		$string = preg_replace('#\[mailurl ([^\[]*)\]([^\[]*)\[/mailurl\]#i', '<a href="mailto:\1">\2</a>', $string);
		$string = preg_replace('#\[mailurl\]([^\[]*)\[/mailurl\]#i', '<a href="mailto:\1">\1</a>', $string);
		$string = preg_replace('#\[email=([^\[]*)\]([^\[]*)\[/email\]#i', '<a href="mailto:\1">\2</a>', $string);
		$string = preg_replace('#\[email ([^\[]*)\]([^\[]*)\[/email\]#i', '<a href="mailto:\1">\2</a>', $string);
		$string = preg_replace('#\[email\]([^\[]*)\[/email\]#i', '<a href="mailto:\1">\1</a>', $string);

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

		if (!defined('SMILIE_DIR')) {
			define('SMILIE_DIR', "images/smilies");
		}
		$string = str_replace(' :) ', '<img src="'.SMILIE_DIR.'/smile.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :-) ', '<img src="'.SMILIE_DIR.'/smile.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' ;) ', '<img src="'.SMILIE_DIR.'/wink.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' ;-) ', '<img src="'.SMILIE_DIR.'/wink.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :p ', '<img src="'.SMILIE_DIR.'/tongue.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :-p ', '<img src="'.SMILIE_DIR.'/tongue.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :P ', '<img src="'.SMILIE_DIR.'/tongue.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :-P ', '<img src="'.SMILIE_DIR.'/tongue.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :0 ', '<img src="'.SMILIE_DIR.'/laugh.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :D ', '<img src="'.SMILIE_DIR.'/biggrin.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :-D ', '<img src="'.SMILIE_DIR.'/biggrin.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :( ', '<img src="'.SMILIE_DIR.'/frown.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :-( ', '<img src="'.SMILIE_DIR.'/frown.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' 8) ', '<img src="'.SMILIE_DIR.'/cool.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' 8-) ', '<img src="'.SMILIE_DIR.'/cool.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :angry: ', '<img src="'.SMILIE_DIR.'/angry.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :sad: ', '<img src="'.SMILIE_DIR.'/sad.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :anger: ', '<img src="'.SMILIE_DIR.'/anger.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :pst: ', '<img src="'.SMILIE_DIR.'/pst.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :holy: ', '<img src="'.SMILIE_DIR.'/holy.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :cool: ', '<img src="'.SMILIE_DIR.'/cool.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);
		$string = str_replace(' :rolleyes: ', '<img src="'.SMILIE_DIR.'/rolleyes.gif" style="border:none;" alt="Smilie" title="Smilie" />', $string);


		$string = preg_replace('#\[font ([^\[]*)\]#i', '<font style=\"font-family:\1">', $string);
		$string = preg_replace('#\[color ([^\[]*)\]#i', '<font style=\"color:\1">', $string);
		$string = preg_replace('#\[size ([^\[]*)\]#i', '<font style=\"font-size:\1pt">', $string);
		$string = preg_replace('#\[font=([^\[]*)\]#i', '<font style=\"font-family:\1">', $string);
		$string = preg_replace('#\[color=([^\[]*)\]#i', '<font style=\"color:\1">', $string);
		$string = preg_replace('#\[size=([^\[]*)\]#i', '<font style=\"font-size:\1pt">', $string);

		$string = str_replace('[/font]', '</font>', $string);
		$string = str_replace('[/FONT]', '</font>', $string);
		$string = str_replace('[/color]', '</font>', $string);
		$string = str_replace('[/COLOR]', '</font>', $string);
		$string = str_replace('[/size]', '</font>', $string);
		$string = str_replace('[/SIZE]', '</font>', $string);

		$string = stripslashes($string);

		return $string;
	}

	/**
	*	Callback-Funktion für preg_replace_callback zum Unterscheiden externer URLs in bbcode
	*
	* @param $match Array mit [0]=> ganzer String, [1]..[n]=> subpatterns in ()
	* @return String mit html-links
	*
	* @author river
	*
	* Im javascript gibt es bereits sowas.
	* Diese Funktion überprüft nicht, ob eine valide URL vorliegt.
	*/
	function bbcode_urls_to_links_with_newtab($match)
	{
		$url = $match[1];
		$scheme = parse_url($url, PHP_URL_SCHEME);
		$host = parse_url($url, PHP_URL_HOST);
		$path = parse_url($url, PHP_URL_PATH);
		// bei relativen / unvollständigen URLs automatisch
		// scheme, host und path hinzufügen.
		// Setzt eine gültige URL voraus.
		if($scheme === NULL)
		{
			if($host === NULL)
			{
				if($path === NULL)
				{
					$url = '/'.$url;
				}
				$url = $_SERVER['SERVER_NAME'].$url;
			}
			$url = 'http://'.$url;
		}
		$intern = (preg_match('#etoa.ch$|etoa.net$#i',parse_url($url, PHP_URL_HOST)) === 1);

		return '<a href="'.$url.'"'.($intern?'':' target="_blank"').'>'.(isset($match[2])?$match[2]:$match[1]).'</a>';
	}

	function bbcode_page_to_links($match) {
		$parts = array();
		if (preg_match('/^([a-z\_]+)(?:\s+(.+))?$/i', $match[1], $parts)) {
			$page = $parts[1];
			$url = '?page='.$page;
			if (isset($parts[2])) {
				foreach(preg_split('/\s+/', $parts[2]) as $e) {
					$url.='&'.$e;
				}
			}
		} else {
			$url = $match[1];
		}
		$label = (isset($match[2])?$match[2]:$match[1]);
		return '<a href="'.$url.'">'.$label.'</a>';
	}

	function ctype_alsc($str)
	{
		return ctype_alpha(str_replace('_','',$str));
	}

	function ctype_aldotsc($str)
	{
		return ctype_alpha(str_replace('_','',str_replace('.','',$str)));
	}

	function strip_bbcode($string) {
		return preg_replace('#\[(.*)\]([^\[]*)\[/(.*)\]#i', '\2', $string);
	}
?>
