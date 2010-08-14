<?PHP

/*
 * Let us introduce the new design class
 * goal is to all the design related stuff into this class including table-design, infoboxes and many more
 *
 * @features
 * -okMsg
 * -errorMsg
 */

	class Design
	{
		static private $counter = -1;

		/*
		 * Access the box counter to make sure every (info)box has a unique id during one login
		 * which is very important for JS-DOM stuff
		 *
		 * @return $counter
		 */
		private function getCounter()
		{
			if (self::$counter == -1)
			{
				if ( isset($_SESSION['design_counter']) )
				{
					self::$counter = (int)$_SESSION['design_counter'];
				}
				else
				{
					self::$counter = $_SESSION['design_counter'] = 0;
				}
			}

			$_SESSION['design_counter'] = ++self::$counter;
			return self::$counter;
		}

		/*
		 * creates an ok msg
		 *
		 * @param <string> $text msg to show, do not use any html!!!
		 * @param <bool> $fixed if set false the div will be gone after a certain amount of time
		 * @param <int> $type add a prefix default is nothing
		 */
		static public function okMsg($text, $fixed=false, $type=0)
		{
			echo '<div class="successBox"';
			if ( !$fixed ) echo 'id="infobox'.self::getCounter().'"';
			echo '>';
			switch ($type)
			{
				case 1:
					echo '<b>Erfolg:</b>  ';
					break;
				case 2:
					echo '<b>Hurra:</b> ';
					break;
				default:
					echo '';
			}
			echo text2html($text).'</div>';
		}

		/*
		 * creates an error msg
		 *
		 * @param <string> $text msg to show, do not use any html!!!
		 * @param <bool> $fixed if set false the div will be gone after a certain amount of time
		 * @param <int> $type add a prefix default is nothing
		 * @param <bool> $exit will exit the page if true after the error msg is printed
		 * @param <int> $addition will show a link at the bottom to report the error
		 * @param <type> $stacktrace
		 */
		static public function errorMsg($text, $fixed=false, $type=0, $exit=false, $addition=0, $stacktrace=null)
		{
			//@TODO: Do check on headers
			echo '<div class="errorBox"';
			if ( !$fixed ) echo 'id="infobox'.self::getCounter().'"';
			echo '>';
			switch ($type)
			{
				case 1:
					echo '<b>Fehler:</b> ';
					break;
				case 2:
					echo '<b>Warnung:</b> ';
					break;
				case 3:
					echo '<b>Problem:</b> ';
					break;
				case 4:
					echo '<b>Datenbankproblem:</b> ';
					break;
				default:
					echo '';
			}
			echo text2html($text);

			switch ($addition)
			{
				case 1:
					echo text2html('\n\n[url http://forum.etoa.ch]Zum Forum[/url] | [email mail@etoa.ch]Mail an die Spielleitung[/email]');
					break;
				case 2:
					echo text2html('\n\n[url http://bugs.etoa.net]Fehler melden[/url]');
				break;
				default:
					echo '';
			}
			if ( isset($stacktrace) )
			{
				echo '<div style="text-align:left;border-top:1px solid #000;">
						<b>Stack-Trace:</b>
						<br />'
						.nl2br($stacktrace).'
						<br />
						<a href="http://bugs.etoa.net" target="_blank">Fehler melden</a>
					</div>';
			}
			echo '</div>';
			
			if ($exit > 0)
			{
				echo '</body></html>';
				exit;
			}
		}

		static public function tableStart($title='', $id='', $width=0, $layout='')
		{
			if ($width > 0)
			{
				$w = 'width:'.$width.'px;';
			}
			elseif ($width != '')
			{
				$w = 'width:'.$width.'';
			}
			else
			{
				global $cu;
				if (isset($cu->properties) && $cu->properties->cssStyle =='Graphite')
					$w = 'width:650px';
				else
					$w = 'width:98%';
			}

			echo '<table id="'.$id.'" ';

			if ($layout == 'double')
			{
				echo 'style="'.$w.'">
						<tr>
							<td style="width:50%;vertical-align:top;">';
			}
			elseif ($layout == 'nondisplay')
			{
				echo 'class="tb boxLayout" style="display:none;'.$w.'">';
			}
			else
			{
				echo 'class="tb boxLayout" style="'.$w.'">';
			}


			if ($title != '')
				echo '<thead id="'.$id.'_head">
						<tr>
							<th class="infoboxtitle" colspan="20">'.$title.'</th>
						</tr>
					</thead>';

			echo '<tbody id="'.$id.'_body">';
		}

		static public function tableEnd($footer='', $style='')
		{
			echo '</tbody>';

			if ($footer != '')
			{
				if ($style != '')
				{
					if ($style == 'button')
					{
						$style = 'style="border:none;margin:0;padding:0px;background:transparent;text-align:right;"';
					}
				}
				echo '<tfoot>
						<tr>
							<td '.$style.' colspan="20">'.$footer.'</td>
						</tr>
					</tfoot>';
			}
			echo "</table>";
		}
		

			
		
		
		
	}

?>