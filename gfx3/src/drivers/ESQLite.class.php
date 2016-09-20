<?php
/*
 *   TRT GFX 4.1.0 (beta build) BackToSlash
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the GPLv3 License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

/** 
 * This class implements a database driver, which is mysql
 * and serves both as an executor (query, single query...)
 * and as a query builder.
 */
class ESQLite implements DatabaseDriverInterface {
	
    //server config
	private static $db_name;
	private static $db_host;
	private static $db_user;
	private static $db_pass;
	
	private static $db_link = 0;
	
	private static $opened = false;
	private static $debug = true;
	
	private static $queries = 0;
	private static $status = 0;
    
    private static $last_executed_query;
    
    private static $db_file_path; //definition in ::load()
	
	/**
	 * set temporary database information for the database class
	 * @param string $name
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @return null
	 */
	
	/**
	 * Prints the database provider actually
	 * implemented
	 */
	public static function provider(){
		return "sqlite";
	}
	
	public static function load(){
		//loading vars from config file
		self::$db_name = EConfig::$data["sqlite"]["name"];
		
        self::$db_file_path = ELoader::$prev_path."/sqlite/" . self::$db_name;
        
		self::open_session();
	}
	
	public static function open_session()
	{
		//opening session
		self::$db_link = new SQLite3(self::$db_file_path);
        
		if(self::$status==0){
			self::$opened = true;
		} else {
			self::$opened = false;
		}
		return self::$opened;
	}
	
	public static function set_db_info($name,$host,$user,$pass){
		self::$db_name = $name;
		self::$db_host = $host;
		self::$db_user = $user;
		self::$db_pass = $pass;
	}
	
	public static function get_db_name(){ return self::$db_name; }
	public static function get_db_host(){ return self::$db_host; }
	public static function get_db_user(){ return self::$db_user; }
	public static function get_db_pass(){ return self::$db_pass; }
	public static function last_executed_query() { return self::$last_executed_query; }
    
	/*
	 * This function is to assure that string or string array 
	 * are safe to be executed as parts of SQL queries
	 */
	public static function safe($s){
		if(is_array($s)){
			foreach($s as $key => $value){
				$s[$key] = SQLite3::escapeString($s[$key]);
			}
			return $s;
		} else {
			$s = SQLite3::escapeString($s);
			return $s;
		}
	}
	
	/**
	 * execute query on database
	 * @param string $q
	 * @return null
	 */
	public static function q($q){
		if(self::$opened==true){
			self::$queries += 1;
            self::$last_executed_query = $q;
			$ret = self::$db_link->query($q);
			$error = self::$db_link->lastErrorMsg();
			if($error!="not an error"){
				ELog::warning($error."<br>Query string: ".$q);
			}
			return $ret;
		} else {
			if(self::$debug==false){
				ELog::warning("sql session not already opened!");
			}
		}
	}
	
	public static function sq($q){
		if(self::$opened==true){
			self::$queries += 1;
            self::$last_executed_query = $q;
			$ret = self::$db_link->query($q);
			$error = self::$db_link->lastErrorMsg();
			if($error!="not an error"){
				ELog::warning($error."<br>Query string: ".$q);
			}
			while($row = $ret->fetchArray()){
				$number = $row[0];
			}
			return $number;
		} else {
			$error = " Query not executed due to mysql session not opened. Try to open one using open method. ";
			ELog::error($error);
		}
	}
	
	public static function table_exists($table){
		$r = self::$db_link->query("SELECT name FROM sqlite_master WHERE name='$table'");
		if(self::$opened) {
			if($r->numColumns()==0){
				return false;
			} else {
				return true;
			}
		} else {
			ELog::error("Database not opened!");
			return false;
		}
	}
	
	public static function num_rows($result){
        $row = $result->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Takes in input a query and returns the results as an associative array
	 * 
	 * @param $query query to be executed
	 * @return an associative array containing the result of the query
	 */
	public static function fetch_assoc($query){
		$result = self::q($query);
		$r = array();
		
		while($arr = $result->fetchArray()){
			$r[] = $arr;
		}
		
		return $r;
	}
	
	public static function status(){
		return self::$status;
	}
	
	/**
	 * @return last inserted id on the table.
	 * 
	 * to be preferably called instantly
	 */
	public static function last_insert_id(){
		return self::$db_link->lastInsertRowID();
	}
	
	/**
	 * @return number of all queries executed on page
	 */
	public static function all_queries(){
		return self::$queries;
	}
	
	public static function unload(){
		if(self::$opened==true){
			self::$db_link->close();
			self::$db_link = 0;
			self::$opened = false;
		} else {
			if(self::$debug==false){
				ELog::error("TRT GFX ISSUE: unable to close mysql session because no one was already opened.");
			}
		}
	}
    
    /** --------------------------------------------
     * ---------- CONDITIONS ----------------------
     ---------------------------------------------*/
    
    /**
     * Add the first condition,
     * with WHERE in this case
     * 
     * @param $c condition to be applied
     * @return string used to build the query
     */
    public static function add_first_condition($c)
    {
        return " WHERE $c ";
    }
    
    public static function groupby($c)
    {
        if(is_array($c)){
            $res = " GROUP BY ";
            foreach($c as $field){
                $res .= " $field,";
            }
            $res = rtrim($res,",");
            return $res;
        } else {
            return " GROUP BY $c ";
        }
    }
    
    public static function add_consequent_condition($c)
    {
        return " AND $c ";
    }
    
    public static function limit($from,$limit)
    {
        if($from==NULL){
            return " LIMIT $limit ";
        } else {
            return " LIMIT $from,$limit ";
        }
    }
    
    public static function ascending()
    {
        return " ASC ";
    }
    
    public static function descending()
    {
        return " DESC ";
    }
    
    public static function orderby($field)
    {
        return " ORDER BY $field ";
    }
    
    public static function count($fields)
    {
        $results = "";
        if(is_array($fields)){
            foreach($fields as $field){
                $results .= " $field,";
            }
            $results = rtrim($results,",");
        } else {
            $results = $fields;
        }
        
        return "COUNT($results)";
    }
    
    /** --------------------------------------------
     * ----------QUERY BUILDING HELPERS ------------
     * ---------------------------------------------*/
    
    public static function select($what,$tablename,$conditions,$limit,$ordering,$orderby,$groupby)
    {
        $wcopy = "";
        if(is_array($what)){
            foreach($what as $field){
                $wcopy .= $field.", ";
            }
            $wcopy = rtrim($wcopy, ", ");
        } else {
            $wcopy = $what;
        }
        
        return "SELECT $wcopy FROM $tablename $conditions $orderby $groupby $ordering $limit";
    }
    
    public static function select_count($what,$additional_fields,$tablename,$conditions,$limit,$ordering,$orderby,$groupby)
    {
        $wcopy = "";
        if(is_array($what)){
            foreach($what as $field){
                $wcopy .= $field.", ";
            }
            $wcopy = rtrim($wcopy, ", ");
        } else {
            $wcopy = $what;
        }
        
        $additional = "";
        if(is_array($additional_fields)){
            foreach($additional_fields as $field){
                $additional .= " $field,";
            }
            $additional = rtrim($additional,",");
        } else {
            $additional = $additional_fields;
        }
        
        return "SELECT COUNT(*)$additional FROM (SELECT $wcopy FROM $tablename $conditions $limit ) AS a $groupby";
    }
    
    public static function insert($what,$tablename,$conditions,$limit,$ordering,$orderby,$groupby)
    {
        if(is_array($what)){
            $columns = "";
            $values = "";
            foreach($what as $key => $value){
                $columns .= "`".$key."`, ";
                if(is_string($value)){
                    $values .= "'".$value."', ";
                } else {
                    $values .= $value.", ";
                }
            }
        }
        
		//delete last commas and spaces, if present
		$columns = rtrim($columns, ", ");
		$values = rtrim($values, ", ");
        
        return "INSERT INTO $tablename ( $columns ) VALUES ( $values ) $limit";
    }
    
    public static function delete($tablename,$conditions,$limit)
    {
        return "DELETE FROM $tablename $conditions $limit";
    }
    
    public static function update($what,$tablename,$conditions,$limit,$ordering,$groupby)
    {
        $data = "";
        if(is_array($what)){
            foreach($what as $key => $value){
                $data .= "`".$key."`=";
                if(is_string($value)){
                    $data .= "'".$value;
                } else {
                    $data .= $value;
                }
                $data .= ", ";
            }
        }
        
		//delete last commas and spaces, if present
		$data = rtrim($data, ", ");
        
        return "UPDATE $tablename SET $data $conditions";
    }
}

?>

