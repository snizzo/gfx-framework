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

class EUser{
	
    public static function sha1_key(){
        $key = "TFfpkz3EefEO0AT9p7elz4YQeWny72fgnSG1V20ei7ymYvmHZ2MnAt9r";
        return $key;
    }
    
	public static $debug = true;
    
    private static $tablename = 'gfx_person';
	
    private static $guest = false;
	private static $logged = false;
	private static $group = "anonymous";
	private static $pass;
	private static $nick;
	private static $mail;
	private static $id;
	private static $firstname;
	private static $lastname;
	
	//geographical data
	private static $city = '';
	private static $region = '';
	private static $country = '';
	private static $continent = '';
    private static $ip;
	private static $fetched = false;
	
	public static function load($useactivated){
		
		EUser::$debug = true;
		EUser::$logged = false;
		EUser::$group = "anonymous";
		
        self::fetch_geographical_data();
        
		//if can't be loaded from session
		if(!EUser::load_from_session()){
			if(isset($_COOKIE['nick']) and isset($_COOKIE['pass'])){
				$user = $_COOKIE['nick'];
				$pass = $_COOKIE['pass'];
				
				EUser::login($user, $pass, false, $useactivated);
			} else if (EConfig::$data['generic']['users_guest']=='yes') {
                //if user can't be logged, load guest if enabled
                self::load_guest();
            }
		}
	}
	
	
	public static function status(){ return EUser::$status; }
	public static function password(){ return EUser::$pass; }
	public static function mail(){ return EUser::$mail; }
	public static function id(){ return EUser::$id; }
	public static function nick(){ return EUser::$nick; }
	public static function logged(){ return EUser::$logged; }
    public static function guest(){ return EUser::$guest; }
	public static function firstname(){ return EUser::$firstname; }
	public static function lastname(){ return EUser::$lastname; }
	public static function city(){ return EUser::$city; }
	public static function region(){ return EUser::$region; }
	public static function country(){ return EUser::$country; }
	public static function continent(){ return EUser::$continent; }
    
    /* if not set, fetch user ip address */
    public static function ip_address() {
        if(empty(EUser::$ip)){
            EUser::fetch_ip_address();
        }
        return EUser::$ip;
    }
	
    public static function fetch_ip_address()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            EUser::$ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            EUser::$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            EUser::$ip = $_SERVER['REMOTE_ADDR'];
        }
    }
    
	public static function fetch_geographical_data()
	{
		if(!EUser::$fetched){
			
            //check if geodata were already loaded for anonymouse user
            if(isset($_SESSION['continent'])){
                EUser::$city = $_SESSION['city'];
                EUser::$region = $_SESSION['region'];
                EUser::$country = $_SESSION['country'];
                EUser::$continent = $_SESSION['continent'];
                return;
            }
            
            //in order to work with proxies etc.
			EUser::fetch_ip_address();
            
            EUtility::hide_output();
			$r = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.EUser::$ip));
			if(!empty(EUtility::show_output())){
                //if request fails, assume we're under some kind of proxy and retry
                if($r['geoplugin_status']==404){
                    EUser::$ip = file_get_contents("http://ipecho.net/plain");
                    $r = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.EUser::$ip));
                }
                
                $_SESSION['city'] = EUser::$city = $r['geoplugin_city'];
                $_SESSION['country'] = EUser::$country = $r['geoplugin_countryName'];
                $_SESSION['region'] = EUser::$region = $r['geoplugin_regionName'];
                $_SESSION['continent'] = EUser::$continent = $r['geoplugin_continentCode'];
                
                $latitude = $r['geoplugin_latitude'];
                $longitude = $r['geoplugin_longitude'];
                
                //attempts timezone check
            } else {
                $_SESSION['city'] = '';
                $_SESSION['country'] = '';
                $_SESSION['region'] = '';
                $_SESSION['continent'] = '';
            }
		} else {
			//just fetch once, in order to avoid too much fetches
			EUser::$fetched = true;
		}
	}
	
	/**
	 * Just load user data from current active session
	 * if any.
	 */
	public static function load_from_session()
	{
		//check for existence
		if(isset($_SESSION['nick'])){
			EUser::$logged = true;
			EUser::$nick = $_SESSION['nick'];
			EUser::$id = $_SESSION['id'];
			EUser::$group = $_SESSION['group'];
			EUser::$mail = $_SESSION['mail'];
			EUser::$pass = $_SESSION['pass'];
			EUser::$firstname = $_SESSION['firstname'];
			EUser::$lastname = $_SESSION['lastname'];
			EUser::$city = $_SESSION['city'];
			EUser::$region = $_SESSION['region'];
			EUser::$country = $_SESSION['country'];
			EUser::$continent = $_SESSION['continent'];
			return true;
		} else {
			return false;
		}
	}
    
    /**
	 * Loads a dummy session with a guest account (if enabled).
     * Permits certain operations as guest.
	 */
	public static function load_guest()
	{
        EUser::$logged = false;
        EUser::$guest = true;
        EUser::$id = 1;
        EUser::$group = 'guest';
        EUser::$city = $_SESSION['city'];
        EUser::$region = $_SESSION['region'];
        EUser::$country = $_SESSION['country'];
        EUser::$continent = $_SESSION['continent'];
	}
	
	/**
	 * Given username and password, this method attempt a basic, stateless
	 * authentication.
	 * 
	 * @return true ($r, array of data) or false.
	 */
	public static function atomic_login($nick, $pass, $useactivated=false)
	{
        $pass = sha1($pass.EUser::sha1_key());
		//login indipendent from database driver
		$person = new EModel(EUser::$tablename);
		$person->add_condition("(login='$nick' or email='$nick')");
		$person->add_condition("password='$pass'");
		if($useactivated){ $person->add_condition("active=1"); }
		$r = $person->find("id,login,firstname,lastname,email,tgroup,password", "LIMIT 1");
		
		if($r){
			return $r[0];
		} else {
			return false;
		}
	}
	
	public static function login($nick, $pass, $remember, $useactivated=false){
		$r = EUser::atomic_login($nick, $pass, $useactivated);
		
		//if there is a result
		if($r){
			//keep first row ($r[0]) in $row
			$row = $r;
			
			EUser::$logged = true;
			
			//here for autologin
			if($remember){
				setcookie("nick",$nick, time()+2419200);
				setcookie("pass",$pass, time()+2419200);
			}
			
			EUser::fetch_geographical_data();
			
			//session save
			$_SESSION['nick'] = $row['login'];
			$_SESSION['id'] = $row['id'];
			$_SESSION['group'] = $row['tgroup'];
			$_SESSION['mail'] = $row['email'];
			$_SESSION['pass'] = $row['password'];
			$_SESSION['firstname'] = $row['firstname'];
			$_SESSION['lastname'] = $row['lastname'];
			//storing geo data in session (avoiding loading too much)
			$_SESSION['city'] = EUser::$city;
			$_SESSION['region'] = EUser::$region;
			$_SESSION['country'] = EUser::$country;
			$_SESSION['continent'] = EUser::$continent;
			
			EUser::load_from_session();
		} else {
			//user is not logged
			EUser::$logged = false;
		}
		
		return EUser::logged();
	}
	
	/** 
	 * performs hard logout
	 */
	public static function logout(){
		session_destroy();
		setcookie("nick","", time()-2419200);
		setcookie("pass","", time()-2419200);
		EUser::$logged = false;
	}
	
	public static function gdeny($g){
		$groups = explode("|", EUser::$group);
		foreach($groups as $thGroup){
			if($thGroup==$g){
				ELog::error("You're not allowed to be here.");
				die($error);
			}
		}
		if(EUser::$logged==false){
			$error = "Loggati e riprova!";
			die($error);
		}
	}
	
	public static function refresh(){
		$r = EUser::atomic_login($nick, $pass, $useactivated);
		
		//if there is a result
		if($r){
			//keep first row ($r[0]) in $row
			$row = $r;
			
			EUser::$logged = true;
			
			//session save
			$_SESSION['nick'] = $row['login'];
			$_SESSION['id'] = $row['id'];
			$_SESSION['group'] = $row['tgroup'];
			$_SESSION['mail'] = $row['email'];
			$_SESSION['pass'] = $row['password'];
			$_SESSION['firstname'] = $row['firstname'];
			$_SESSION['lastname'] = $row['lastname'];
			//storing geo data in session (avoiding loading too much)
			$_SESSION['city'] = EUser::$city;
			$_SESSION['region'] = EUser::$region;
			$_SESSION['country'] = EUser::$country;
			$_SESSION['continent'] = EUser::$continent;
			
			EUser::load_from_session();
		}
	}
	
	public static function gallow($g){
		$allowedgroups = explode("|", $g);
		$groups = explode("|", EUser::$group);
		foreach($groups as $thGroup){
			foreach($allowedgroups as $alGroup){
				if($thGroup==$alGroup){
					return true;
				}
			}
		}
		ELog::error("You're not allowed to be here.");
		return false;
	}
	
	public static function belongs_to_group($g){
		$groups = explode("|", EUser::$group);
		foreach($groups as $thGroup){
			if($thGroup==$g){
				return true;
			}
		}
		return false;
	}
	
	public static function group(){
		return EUser::$group;
	}
	
	public static function register($nick, $pass, $firstname, $lastname, $email, $activekey, $group){
        
        $person = new EModel(EUser::$tablename);
        
        $data = array(
            'login' => $nick,
            'password' => sha1($pass.EUser::sha1_key()), //encrypted password
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'activekey' => $activekey,
            'tgroup' => $group
        );
        
        if(EConfig::$data['generic']['users_verified']!="yes"){
            $data['active'] = 1;
        }
        
        $person->insert($data);
        
	}
	
	/*
	 * Obscure magic string told me by the elders.
	 * If modified, I don't guarantee you'll be safe anymore.
	 */
	public static function isvalidemail($email){
		if(preg_match("/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i", $email)){
			return true;
		} else {
			return false;
		}
	}
	
	public static  function isvalidpassword($pass){
		if(strlen($pass)>=8){
			return true;
		} else {
			return false;
		}
	}
	
    public static function is_already_present_email($email){
        $person = new EModel(EUser::$tablename);
        $person->add_condition("email='$email'");
        if($person->is_there('email')){
            return true;
        } else {
            return false;
        }
    }
    
    public static function is_already_present_login($login){
        $person = new EModel(EUser::$tablename);
        $person->add_condition("login='$login'");
        if($person->is_there(array('login'))){
            return true;
        } else {
            return false;
        }
    }
    
	public static  function isloginname($login){
		if(preg_match("([A-Za-z0-9]*)",$login)){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Attempt to activate an account
	 * using its activation key/hash
	 * 
	 * @return true if succeeded, false otherwise
	 */
	public static function activate($hash)
	{
		$person = new EModel(EUser::$tablename);
		$person->add_condition("activekey='$hash'");
		if(($r = $person->find('login,firstname,email'))){
            $data = array(
                'active' => 1
            );
			$person->update($data);
			EUser::$nick = $r[0]['login'];
			EUser::$firstname = $r[0]['firstname'];
			EUser::$mail = $r[0]['email'];
			return true;
		} else {
			return false;
		}
	}
}

?>
