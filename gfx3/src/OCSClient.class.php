<?php

/*
 *   TRT GFX 3.0.1 (beta build) BackToSlash
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

class OCSClient{
	
	private $target_server;
	
	public function __construct($srv){
		$this->set_target_server($srv);
	}
	
	public function set_target_server($srv){
		$this->target_server = rtrim($srv,"/")."/";
	}
	
	public function get($url){
		$s = new ENetworkSocket($this->target_server);
		$raw_xml = $s->get($url);
		
		return EXmlParser::to_array($raw_xml);
	}
	
	public function post($url,$data){
		$s = new ENetworkSocket($this->target_server);
		$s->set_post_data($data);
		$raw_xml = $s->post($url);
		
		return EXmlParser::to_array($raw_xml);
	}
	
}

?>
