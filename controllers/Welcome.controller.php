<?php
class WelcomeController extends EController
{
	public function index($args)
	{
        EStructure::view("header", array("title"=>"Welcome page"));
        
        $page = array(
                    'title' => "Welcome page",
                    'content' => "Hi, welcome to your newly created web application. This dummy website is running on the gfx framework."
                    );
        EStructure::view("page", $page);
        
        EStructure::view("footer");
	}
    
    public function about()
    {
        EStructure::view("header", array("title"=>"About page"));
        
        $page = array(
                    'title' => "About us",
                    'content' => "Our company has been founded in 1900. We have strong experience and sense of community. Let's do business together."
                    );
        EStructure::view("page", $page);
        
        EStructure::view("footer");
    }
}
?>
