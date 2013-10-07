<?php

class ArticlesModel extends EModel
{
	public function __construct()
	{
		parent::__construct("articles");
	}
	
	public function getAll()
	{
		$data = $this->find("*");
		
		return $data;
	}
	
}

?>
