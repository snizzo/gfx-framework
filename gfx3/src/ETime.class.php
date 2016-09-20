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

/*
 * Class used to do time measurement.
 */
class ETime {
	
	private static $time_start;
	
	public static function measure_from(){
		ETime::$time_start = microtime(true);
	}
	
	public static function measure_to() { 
		$time_end = microtime(true);
		$time = $time_end - ETime::$time_start;
		return $time;
	}
	
    public static function time_elapsed_to_string($timestamp){
        $difference = time() - $timestamp;
        
        if($difference>31536000){
            $res = floor($difference/31536000);
            if($res==1){ return $res. " year"; } else { return $res. " years"; }
        } else
        if($difference>2592000){
            $res = floor($difference/2592000);
            if($res==1){ return $res. " month"; } else { return $res. " months"; }
        } else
        if($difference>86400){
            $res = floor($difference/86400);
            if($res==1){ return $res. " day"; } else { return $res. " days"; }
        } else
        if($difference>3600){
            $res = floor($difference/3600);
            if($res==1){ return $res. " hour"; } else { return $res. " hours"; }
        } else
        if($difference>60){
            $res = floor($difference/60);
            if($res==1){ return $res. " minute"; } else { return $res. " minutes"; }
        } else
        if($difference>0){
            $res = $difference;
            if($res==1){ return $res. " second"; } else { return $res. " seconds"; }
        }
    }
    
}

?>
