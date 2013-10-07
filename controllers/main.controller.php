<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class MainController extends EController
{
	public function index($args)
	{
		echo "Hello from main controller!";
	}
	
	public function last_news()
	{
		$articles = new ArticlesModel();
		EStructure::view("articles", $articles->getAll());
	}
	
	public function cancel()
	{
		echo "cancelling";
	}
}

?>
