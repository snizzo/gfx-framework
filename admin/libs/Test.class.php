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
 * Contains different methods used in testing
 * environment. Mostly for developers or used in
 * the admin panel of the gfx server 
 */
class Test{
	
	public static function install_database()
	{
		$driver = EModel::get_driver();
		
        if($driver::provider()=="mysqli"){ self::install_mysqli(); }
        if($driver::provider()=="sqlite"){ self::install_sqlite(); }
    }
    
    public static function install_sqlite(){
        $driver = EModel::get_driver();
        $driver::q("CREATE TABLE IF NOT EXISTS \"gfx_person\" (
                    \"id\" INTEGER PRIMARY KEY AUTOINCREMENT,
                    \"login\" TEXT,
                    \"password\" TEXT,
                    \"firstname\" TEXT,
                    \"lastname\" TEXT,
                    \"email\" TEXT,
                    \"tgroup\" TEXT,
                    \"active\" INTEGER DEFAULT (0),
                    \"activekey\" TEXT,
                    \"hasnotifications\" INTEGER DEFAULT (0)
                );");
        $driver::q("CREATE TABLE IF NOT EXISTS \"gfx_stats\" (
                    \"ip\" TEXT,
                    \"country\" TEXT,
                    \"region\" TEXT,
                    \"city\" TEXT,
                    \"continent\" TEXT,
                    \"action\" TEXT,
                    \"month\" TEXT,
                    \"year\" TEXT
                );");
        $driver::q("CREATE TABLE IF NOT EXISTS \"gfx_jobs\" (
                    \"id\" INTEGER PRIMARY KEY,
                    \"person\" INTEGER,
                    \"pid\" INTEGER,
                    \"wildtype_residue\" TEXT,
                    \"position\" NULL,
                    \"mutant\" TEXT,
                    \"date\" INTEGER,
                    \"computing\" INTEGER,
                    \"notification\" INTEGER,
                    \"instantnotification\" INTEGER,
                    \"sequence\" TEXT
                );");
    }
    
    public static function install_mysqli(){
        $driver = EModel::get_driver();
		$driver::q("CREATE TABLE IF NOT EXISTS `gfx_person` (
                      `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `login` varchar(45) NOT NULL,
                      `password` varchar(45) NOT NULL,
                      `firstname` varchar(45) NOT NULL,
                      `lastname` varchar(45) NOT NULL,
                      `email` varchar(100) NOT NULL,
                      `tgroup` varchar(20) NOT NULL,
                      `active` tinyint(1) NOT NULL DEFAULT '0',
                      `activekey` varchar(255) NOT NULL,
                      `hasnotifications` tinyint(1) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM");
		$driver::q("CREATE TABLE IF NOT EXISTS `gfx_stats` (
                      `ip` varchar(20) NOT NULL,
                      `country` varchar(100) DEFAULT NULL,
                      `region` varchar(100) DEFAULT NULL,
                      `city` varchar(100) DEFAULT NULL,
                      `continent` varchar(100) DEFAULT NULL,
                      `action` varchar(100) NOT NULL,
                      `month` varchar(20) NOT NULL,
                      `year` varchar(20) NOT NULL,
                      KEY `ip` (`ip`)
                    ) ENGINE=MyISAM;");
		$driver::q("CREATE TABLE IF NOT EXISTS `gfx_jobs` (
                      `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `person` int(11) NOT NULL,
                      `pid` int(11) NOT NULL,
                      `wildtype_residue` varchar(1) NOT NULL,
                      `position` int(11) NOT NULL,
                      `mutant` varchar(1) NOT NULL,
                      `date` int(11) NOT NULL,
                      `computing` tinyint(1) NOT NULL DEFAULT '0',
                      `notification` tinyint(1) NOT NULL DEFAULT '0',
                      `instantnotification` tinyint(1) NOT NULL DEFAULT '0',
                      `sequence` text NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `person` (`person`)
                    ) ENGINE=MyISAM;");
	}
	
    public static function reset_database()
	{
		$driver = EModel::get_driver();
		
        if($driver::provider()=="mysqli"){ self::reset_database_mysqli(); }
        if($driver::provider()=="sqlite"){ self::reset_database_sqlite(); }
    }
    
	public static function reset_database_mysqli()
	{
        $driver = EModel::get_driver();
		$driver::q("DROP TABLE IF EXISTS `gfx_person`;");
        $driver::q("DROP TABLE IF EXISTS `gfx_stats`;");
        $driver::q("DROP TABLE IF EXISTS `gfx_jobs`;");
		
		Test::install_database();
        
        //inserting guest account
        $person = new EModel("gfx_person");
        $person->insert(array(
                'id' => 1,
                'login' => 'guest',
                'password' => substr(sha1(microtime()), 0, 20),
                'email' => substr(sha1(microtime()), 0, 20),
                'firstname' => 'guest_name',
                'lastname' => 'guest_lastname',
                'tgroup' => 'guests',
                'active' => 0,
                'activekey' => substr(sha1(microtime()), 0, 20),
                ));
        //reserve first id for guest user
        $driver::q("ALTER TABLE `gfx_person`
                    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");
	}
    
    public static function reset_database_sqlite()
	{
        $driver = EModel::get_driver();
		$driver::q("DROP TABLE IF EXISTS `gfx_person`;");
        $driver::q("DROP TABLE IF EXISTS `gfx_stats`;");
        $driver::q("DROP TABLE IF EXISTS `gfx_jobs`;");
		
		Test::install_database();
        
        //reserve first id for guest user
        $person = new EModel("gfx_person");
        $person->insert(array(
                'id' => 1,
                'login' => 'guest',
                'password' => substr(sha1(microtime()), 0, 20),
                'email' => substr(sha1(microtime()), 0, 20),
                'firstname' => 'guest_name',
                'lastname' => 'guest_lastname',
                'tgroup' => 'guests',
                'active' => 0,
                'activekey' => substr(sha1(microtime()), 0, 20),
                ));
        $driver::q("UPDATE SQLITE_SEQUENCE SET seq = 1 WHERE name = 'gfx_person';");
	}
	
	public static function _error($s)
	{
		return '<span style="color:red">'.$s.'</span>';
	}
	
	public static function _notify($s)
	{
		return '<span style="color:green">'.$s.'</span>';
	}
	
}

?>
