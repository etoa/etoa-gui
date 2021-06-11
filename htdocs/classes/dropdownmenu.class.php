<?PHP

  class DropdownMenuItem {

    public $name;
    public $link;
    public $childs = array();

    public function __construct($name, $link) {
      $this->name = $name;
      $this->link = $link;
    }
  }

	class DropdownMenu
	{
		private $tree;
		private $js;
		function __construct($js=0)
		{
			$this->tree = array();
			$this->js=$js;
		}

		function add($key,$name,$link)
		{
			$this->tree[$key] = new DropdownMenuItem($name, $link);
		}

		function addChild($key,$name,$link,$parent)
		{
      $this->tree[$parent]->childs[$key] = new DropdownMenuItem($name, $link);
		}

		function __toString()
		{
			ob_start();
			echo '<div id="dropdown"><ul id="pmenu">';
			foreach ($this->tree as $i)
			{
				if (count($i->childs)>0)
				{
					echo '<li class="tc drop">';
					if ($this->js==1)
						echo '<a href="javascript:;" onclick="'.$i->link.'">'.$i->name;
					else
						echo '<a href="'.$i->link.'">'.$i->name;
					echo '<!--[if IE 7]><!--></a><!--<![endif]-->
					<!--[if lte IE 6]><table><tr><td><![endif]-->
					<ul>';
					$cnt=0;
					foreach ($i->childs as $j)
					{
						if ($cnt==0)
							echo '<li class="enclose">';
						else
							echo '<li>';
						if ($this->js==1)
							echo '<a href="javascript:;" onclick="'.$j->link.'">';
						else
							echo '<a href="'.$j->link.'">';
						echo $j->name.'</a></li>';
						$cnt++;
					}
					echo '</ul>
					<!--[if lte IE 6]></td></tr></table></a><![endif]-->
					</li>';
				}
				else
				{
					echo '<li class="tc">';
					if ($this->js==1)
						echo '<a href="javascript:;" onclick="'.$i->link.'">';
					else
						echo '<a href="'.$i->link.'">';
					echo $i->name.'</a></li>';
				}
			}
			echo '</ul></div>';

			$rtn = ob_get_contents();
			ob_end_clean();
			return $rtn;

		}
	}
?>
