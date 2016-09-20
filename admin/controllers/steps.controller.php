<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class StepsController extends EController
{
	public function index()
	{
		//empty for now
	}
	
	public function _error($s)
	{
		return '<span style="color:red">'.$s.'</span>';
	}
	
	public function _notify($s)
	{
		return '<span style="color:green">'.$s.'</span>';
	}
	
	public function step1($args)
	{
        $working = false;
        
        $name = EHeaderDataParser::post('name');
        $host = EHeaderDataParser::post('host');
        $user = EHeaderDataParser::post('user');
        $pass = EHeaderDataParser::post('password');
        $pass2 = EHeaderDataParser::post('password2');
        $notification = '';
        
        $database_path = ELoader::$prev_path.'/config/database.conf.php';
		
		$cf = new EConfigFile();
		$cf->set_abs_file($database_path);
        
        //FIXME: backward compatibility
        $name = $cf->get('name');
        if((empty($name)) and !empty($name)){
			$name = $cf->get('name');
		}
		
		//FIXME: backward compatibility
		$host = $cf->get('host');
		if((empty($host)) and !empty($host)){
			$host = $cf->get('host');
		}
		
		//FIXME: backward compatibility
		$user = $cf->get('user');
		if((empty($user)) and !empty($user)){
			$user = $cf->get('user');
		}
		
		//FIXME: backward compatibility
		$password = $cf->get('password');
		if((empty($pass)) and !empty($password)){
			$pass = $pass2 = $cf->get('password');
		}
        
        if(!empty($name) and !empty($user) and !empty($host) and !empty($pass) and !empty($pass2)){
			if($pass!=$pass2){
				$this->_error('Warning! Your passwords didn\'t match! Please reinsert them!');
			} else {
				$cf->set('name', $name);
				$cf->set('user', $user);
				$cf->set('host', $host);
				$cf->set('password', $pass);
				
				EDatabase::set_db_info($name,$host,$user,$pass);
				EUtility::hide_output(); // hiding output as mysqli functions are surely outputting something
				if(!EDatabase::open_session()){
					EUtility::show_output();
					$notification = $this->_error('Couldn\'t open connection to database! Please check config!');
				} else {
					OCSTest::install_ocs_database(); //execute soft install
					$out = EUtility::show_output();
					
					if(!empty($out)){
						$notification = $this->_error('Something went wrong with install phase! Please check config!');
					} else {						
						$notification = $this->_notify('We can connect to database! Database is installed and configuration saved!');
						$working = true;
						$cf->save();
					}
				}
			}
		}
		
		$data = array();
		$data['name'] = $name;
		$data['user'] = $user;
		$data['host'] = $host;
		$data['pass'] = $pass;
		$data['pass2'] = $pass2;
		$data['working'] = $working;
		$data['notification'] = $notification;
		EStructure::view('wizard/step1', $data);
		
	}
	
	public function step3()
	{
		if($this->arg_key('save')){
			$pass1 = EHeaderDataParser::post('pass');
			$pass2 = EHeaderDataParser::post('pass2');
			
			if($pass1==$pass2){
				$cf = new EConfigFile('generic');
		
				$cf->set('password', $pass1);
				$cf->set('enabled', 'protected');
				$cf->save();
			}
			EStructure::view('wizard/step3save');
		} else {
			$data = array();
			
			if(isset(EConfig::$data['generic']['password'])){
				$data['pass'] = EConfig::$data['generic']['password'];
			} else {
				$data['pass'] = '';
			}
			
			EStructure::view('wizard/step3', $data);
		}
	}
}

?>
