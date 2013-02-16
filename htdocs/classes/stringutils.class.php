<?PHP

	/**
	* String utilities
  *
  * @author MrCage <mrcage@etoa.ch>
	*/
	class StringUtils
	{
    /**
    * Splits a string of words into an array and treats words in quotes as a single word
    *
    * Test:
    *   StringUtils::splitBySpaces('Lorem ipsum "dolor sit amet" consectetur "adipiscing \\"elit" dolor') 
    *   == array ('Lorem','ipsum','dolor sit amet','consectetur','adipiscing "elit','dolor'))
    *
    * @see http://stackoverflow.com/questions/2202435/php-explode-the-string-but-treat-words-in-quotes-as-a-single-word
    */
    static function splitBySpaces($text) {
      if (preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $text, $matches)) {
        $rtn = array();
        for ($x=0; $x < count($matches[0]); $x++) {
          $rtn[$x] = preg_replace(array('/^"/', '/"$/', '/\\\"/'), array('', '', '"'), $matches[0][$x]);
        }
        return $rtn;
      } else {
        if (strlen($text) > 0) {
          return array($text);
        }
        return array();
      }
    }    
	}
?>