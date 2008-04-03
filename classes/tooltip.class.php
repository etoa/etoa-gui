<?PHP

define('TOOLTIP_TEXT_COLOR','#eef');
define('TOOLTIP_COMMENT_COLOR','#FFD517');
define('TOOLTIP_TITLE_COLOR','#fff');
define('TOOLTIP_COND_GOD_COLOR','#0f0');
define('TOOLTIP_COND_BAD_COLOR','#f00');

class Tooltip
{
	private $text;
	
	function Tooltip($bgimage="")
	{
		if ($bgimage!="")
		{
			$this->text = '<div style="background:url('.$bgimage.') no-repeat;">';
		}
		else
		{
			$this->text="";
		}
	}
	
	private function add($obj)
	{
		$this->text = $this->text.$obj;
	}
	  	
	function __toString()
	{
		return tt($this->text);
	}	

	function addIcon($path)
	{
		$this->text = '<div style="float:right;"><img src="'.$path.'" alt="Icon" /></div>';
	}
	  
	function addText($text)
	{
		$this->add(text2html($text)."<br/>");
	}	

	function addHtml($text)
	{
		$this->add($text."<br/>");
	}	

	function addTitle($text)
	{
		$this->add("<div style=\"color:".TOOLTIP_TITLE_COLOR."\"><b>".$text."</b></div>");
	}	

	function addGoodCond($text)
	{
		$this->add("<div style=\"color:".TOOLTIP_COND_GOD_COLOR."\">".$text."</div>");
	}	
	function addBadCond($text)
	{
		$this->add("<div style=\"color:".TOOLTIP_COND_BAD_COLOR."\">".$text."</div>");
	}	
		
	function addComment($text)
	{
		$this->add("<div style=\"color:".TOOLTIP_COMMENT_COLOR."\">".$text."</div>");
	}	
		
	function addImage($path)
	{
		$this->add("<img src=\"".$path."\" alt=\"TTImage\" style=\"background:#000;\" /><br/>");
	}
	
}



?>