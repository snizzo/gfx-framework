<?php

include_once("../gfx3/lib.php");

//manually including all supported database drivers
ELoader::include_source("drivers/EMysql");
ELoader::include_source("drivers/ESQLite");

$working = false;

$name = EHeader::post('name');
$host = EHeader::post('host');
$user = EHeader::post('user');
$pass = EHeader::post('password');
$pass2 = EHeader::post('password2');
$drv = EHeader::post('changedrv');
$notification = '';

//driver updates and updating
if($drv==false){
    $generic_path = ELoader::$prev_path.'/../config/generic.conf.php';
    $generic = new EConfigFile();
    $generic->set_abs_file($generic_path);
    $drv = $generic->get('databasedriver');
} else {
    $generic_path = ELoader::$prev_path.'/../config/generic.conf.php';
    $generic = new EConfigFile();
    $generic->set_abs_file($generic_path);
    $generic->set('databasedriver', $drv);
    $generic->save();
}

EModel::set_driver($drv);

if($drv=="EMysql"){
    $database_path = ELoader::$prev_path.'/../config/database.conf.php';

    $cf = new EConfigFile();
    $cf->set_abs_file($database_path);

    //FIXME: backward compatibility
    $key_name = $cf->get('name');
    if((empty($name)) and !empty($key_name)){
        $name = $cf->get('name');
    }

    //FIXME: backward compatibility
    $key_host = $cf->get('host');
    if((empty($host)) and !empty($key_host)){
        $host = $cf->get('host');
    }

    //FIXME: backward compatibility
    $key_user = $cf->get('user');
    if((empty($user)) and !empty($key_user)){
        $user = $cf->get('user');
    }

    //FIXME: backward compatibility
    $key_password = $cf->get('password');
    if((empty($pass)) and !empty($key_password)){
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
            
            $driver = EModel::get_driver();
            
            $driver::set_db_info($name,$host,$user,$pass);
            EUtility::hide_output(); // hiding output as mysqli functions are surely outputting something
            if(!$driver::open_session()){
                EUtility::show_output();
                $notification = $this->_error('Couldn\'t open connection to database! Please check config!');
                
            } else {
                Test::install_database(); //execute soft install
                $out = EUtility::show_output();
                
                if(!empty($out)){
                    $notification = Test::_error('Something went wrong with install phase! Please check config! <br>Error: <br>'.$out);
                } else {						
                    $notification = Test::_notify('We can connect to database! Database is installed and configuration saved!');
                    $working = true;
                    $cf->save();
                    
                    //if choice was to completely reset database
                    if(isset($_POST['reset']) and $_POST['reset']=="yes"){
                        Test::reset_database();
                        EUtility::redirect("database.php");
                    }
                    
                }
            }
        }
    }
} else if($drv=="ESQLite") {
    $driver = EModel::get_driver();
    
    EUtility::hide_output(); // hiding output as sqlite functions are surely outputting something
    if(!$driver::open_session()){
        EUtility::show_output();
        $notification = $this->_error('Couldn\'t open connection to database! Please check config!');
        
    } else {
        Test::install_database(); //execute soft install
        $out = EUtility::show_output();
        
        if(!empty($out)){
            $notification = Test::_error('Something went wrong with install phase! Please check config! <br>Error: <br>'.$out);
        } else {						
            $notification = Test::_notify('We can connect to database! Database is installed and configuration saved!');
            $working = true;
            
            //if choice was to completely reset database
            if(isset($_POST['reset']) and $_POST['reset']=="yes"){
                Test::reset_database();
                EUtility::redirect("database.php");
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
$data['driver'] = $drv;
EStructure::view('wizard/step1', $data);
?>
