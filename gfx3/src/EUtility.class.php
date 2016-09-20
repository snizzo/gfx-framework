<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

class EUtility {
	
	//actually this function is needed by some files, but I don't actually know why.
	//and what this function did. Put this for now, but can generate errors.
	public static function stripslashes($string) {
		return str_replace('\\','',$string);
	}
	
	public static function br2nl($string) { 
		return str_replace('<br>', '\r\n', $string);
	}
	
	public static function nl2br($string) {
		$string = str_replace('\r\n', '<br>', $string);
		$string = str_replace('\r', '<br>', $string);
		$string = str_replace('\n', '<br>', $string);
		return $string;
	}
	
    /**
     * Automatically parse eventually added get parameters
     * 
     */
	public static function redirect($page)
	{
        $result = "";
        $fields = array();
        
        $chunks = explode("?", $page);
        $result2 = $chunks[0];
        if(count($chunks)>0){
            $result .= $chunks[0]."?".$chunks[1];
            for($i=2; $i<count($chunks); $i++){
                $result .= "&" . $chunks[$i];
            }
        }
        
        $chunks = explode("?", $result);
        if(count($chunks)>0){
            $chunks = $chunks[1];
            $chunks = explode("&", $chunks);
            foreach($chunks as $chunk){
                $chunk = explode("=", $chunk);
                $field = $chunk[0];
                if(!in_array($field, $fields)){
                    $fields[$chunk[0]] = $chunk[1];
                }
            }
        }
        
        $firsttime = true;
        foreach($fields as $key => $value){
            if($firsttime){
                $result2 .= "?$key=$value";
                $firsttime = false; 
            } else {
                $result2 .= "&$key=$value";
            }
        }
		header("location: $result2");
	}
	
	public static function hide_output()
	{
		ob_start();
	}
	
	public static function show_output()
	{
		return ob_get_clean();
	}
	
	/**
	 * DEPRECATED
	 * moved to EPageProperties
	 * kept this for retrocompatibility
	 * use EPageProperties
	 */
	public static function get_domain($domain, $debug = false)
	{
		return EPageProperties::get_domain($domain, $debug);
	}
	
	/**
	 * DEPRECATED
	 * moved to EPageProperties
	 * kept this for retrocompatibility
	 * use EPageProperties
	 */
	public static function get_clear_domain($domain)
	{
		return EPageProperties::get_clear_domain($domain);
	}

	
}

?>
