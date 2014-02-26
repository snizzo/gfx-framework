<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class MainController extends EController
{
	public function index($args)
	{
		EStructure::view("home");
	}
	
	public function last_news()
	{
		$articles = new ArticlesModel();
		EStructure::view("articles", $articles->getAll());
	}
}

?>
