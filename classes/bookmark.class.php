<?PHP
	
	class Bookmark
	{
		
		
		function Bookmark(&$arr)
		{
		
							array_push(
							$bookmarks,
							array(
							"cell_sx"=> $parr['cell_sx'],
							"cell_sy"=> $parr['cell_sy'],
							"cell_cx"=> $parr['cell_cx'],
							"cell_cy"=> $parr['cell_cy'],
							"planet_solsys_pos"=> 0,
							"automatic"=>0,
							"bookmark_comment"=> $parr['bookmark_comment'],
							"nebula"=> $parr['cell_nebula'],
							"asteroid"=> $parr['cell_asteroid'],
							"wormhole"=> $parr['cell_wormhole_id']));
			
		}
	}

?>