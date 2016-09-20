<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */
 
/**
 * This class aims to do some quite simple URL Rewriting.
 * 
 * @depends on a working EModel database driver/configuration
 */

class EPaginator{
	
    private $maxitems = NULL;
    private $page = 1;
    private $itemsperpage = 10;
    private $model = NULL;
    private $results = NULL;
    
    /**
     * @param $model a model with the preferred
     * conditions already set
     */
	public function __construct($model,$itemsperpage=10)
	{
        $this->itemsperpage = $itemsperpage;
        $this->model = $model;
        
		if(EHeader::exists_get('page')){
            $this->page = EHeader::db_get('page');
            if($this->page<1){ $this->page = 1; }
        }
        
        if(EHeader::exists_get('limit')){
            $this->itemsperpage = EHeader::db_get('limit');
        }
	}
    
    public function get_results()
    {
        $this->retrievemaxitems();
        
        $this->model->set_limit(($this->page-1)*$this->itemsperpage, $this->itemsperpage);
        
        return $this->model->find();
    }
    
    public function get_links()
    {   
        $links      = $this->itemsperpage;
        $list_class = 'pagination';
        $last       = ceil( $this->maxitems / $this->itemsperpage );
        
        $start      = ( ( $this->page - $links ) > 0 ) ? $this->page - $links : 1;
        $end        = ( ( $this->page + $links ) < $last ) ? $this->page + $links : $last;
        
        $html       = '<ul class="' . $list_class . '">';
        
        if($this->page == 1){
            $html       .= '<li class="disabled"><a href="#">&laquo;</a></li>';
        } else {
            $html       .= '<li><a href="?limit=' . $this->itemsperpage . '&page=' . ( $this->page - 1 ) . '">&laquo;</a></li>';
        }
        
        if ( $start > 1 ) {
            $html   .= '<li><a href="?limit=' . $this->itemsperpage . '&page=1">1</a></li>';
            $html   .= '<li class="disabled"><span>...</span></li>';
        }
        
        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $this->page == $i ) ? "active" : "";
            $html   .= '<li class="' . $class . '"><a href="?limit=' . $this->itemsperpage . '&page=' . $i . '">' . $i . '</a></li>';
        }
        
        if ( $end < $last ) {
            $html   .= '<li class="disabled"><span>...</span></li>';
            $html   .= '<li><a href="?limit=' . $this->itemsperpage . '&page=' . $last . '">' . $last . '</a></li>';
        }
        
        if($this->page == $last){
            $html       .= '<li class="disabled"><a href="#">&raquo;</a></li>';
        } else {
            $html       .= '<li><a href="?limit=' . $this->itemsperpage . '&page=' . ( $this->page + 1 ) . '">&raquo;</a></li>';
        }
        
        $html       .= '</ul>';
        
        return $html;
    }
    
    public function retrievemaxitems()
    {
        if($this->maxitems==NULL){
            $this->model->set_limit(0,80);
            $this->maxitems = $this->model->count();
        }
    }
}

?>
