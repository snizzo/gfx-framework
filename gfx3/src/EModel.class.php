<?php

/*
 *   GFX 4.1
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

/*
 * This class abstracts popular database operations using
 * the provided database driver (mysqli, sqlite etc...).
 * 
 * It is encouraged to use this class instead of directly
 * querying the database in order to build a backend-indipendent
 * application.
 */

class EModel {
	//in order to avoid avoidable queries
	private $ready = false;
	private $table = false;
	
	//conditions are generate through database driver
	private $conditions = "";
	private $is_first_condition = true;
	
	//limit and ordering are generated through database driver
	private $limit = "";
	private $ordering = "";
	private $orderby = "";
    private $groupby = "";
	
	//default driver is mysqli driver
	private static $driver = 'EMysql';
	
	public function __construct($tbl){
		if(!empty($tbl)){
			$this->table = $tbl;
			
			$this->dbg = false;
		} else {
			ELog::error("Can't use EModel without a table name.");
		}
	}
	
	/**
	 * Static methods used for database driver abstraction
	 */
	public static function get_driver()
	{
		return EModel::$driver;
	}
	
	public static function set_driver($drv)
	{
		EModel::$driver = $drv;
	}
	
	/**
	 * Set this to true in order to make EData just simulate modifications
	 * and echo the query. Use only for debug purposes.
	 */
	public function set_simulate($b){
		$this->noquery = $b;
	}
	
	/**
	 * Rewrite debug rule.
	 */
	public function set_debug($b){
		$this->dbg = $b;
	}
	
	/*
	 * ----------------------------------------------
	 * ------------CONDITIONS METHODS----------------
	 * ----------------------------------------------
	 */
	public function clear_conditions()
	{
		//clearing conditions
		$this->conditions = '';
	}
	
	public function add_condition($c){
		$driver = self::get_driver();
		if($this->is_first_condition){
			$this->conditions .= $driver::add_first_condition($c);
			$this->is_first_condition = false;
		} else {
			$this->conditions .= $driver::add_consequent_condition($c);
		}
	}
	
	/**
	 * this is mainly called by internal operations and
	 * is reset every time a modifier (find, insert, update, delete...)
	 * is run
	 */
	public function clear_limit(){
		$this->limit = "";
	}
	
	public function clear_ordering(){
		$this->ordering = "";
	}
	
	public function set_limit($from,$limit)
	{
		$driver = self::get_driver();
		$this->limit = $driver::limit($from,$limit);
	}
	
	public function set_ascending()
	{
		$driver = self::get_driver();
		$this->ordering = $driver::ascending();
	}
	
	public function set_descending()
	{
		$driver = self::get_driver();
		$this->ordering = $driver::descending();
	}
	
	public function order_by($field)
	{
		$driver = self::get_driver();
		$this->orderby = $driver::orderby($field);
	}
    
    public function group_by($field)
	{
		$driver = self::get_driver();
		$this->groupby = $driver::groupby($field);
	}
	
	/* 
	 * --------------------------------
	 * ---------MODIFIERS--------------
	 * --------------------------------
	 */
	
	/*
	 * Extrapolates data and map it into an associative array
	 * 
	 * @param $what can be either string or array (of strings) containing " * "
	 * 				or a list of fields to retrieve
	 */
	public function find($what=" * ") {
		$driver = EModel::$driver;
		
		//build the query, if any condition is present, attach right conditions
		$q = $driver::select($what, $this->table, $this->conditions, $this->limit, $this->ordering, $this->orderby, $this->groupby);
		
		//executing query and preparing output
		$result = $driver::fetch_assoc($q);
		
		if(isset($result)){
			return $result;
		} else {
			return false;
		}
	}
    
	/**
	 * UPDATE
	 *  a table with conditions
	 */
	public function update($what=array()) {
		$driver = EModel::$driver;
		
		if(is_array($what)){
			if(!empty($what)){
				//build the query, if any condition is present, attach right conditions
				$q = $driver::update($what, $this->table, $this->conditions, $this->limit, $this->ordering, $this->orderby, $this->orderby);
				
				$driver::q($q);
			} else {
				ELog::error('EModel::update() needs to be passed an array with some data (not empty).');
			}
		} else {
			ELog::error('EModel::update() expects first parameter to be array.');
		}
	}
	
	/**
	 * INSERT
	 * Performs an insert into a table
	 * (doesn't consider conditions in this case)
	 * 
	 * @param $what array(key => value) containing field and value associated
	 */
	public function insert($what=array(), $where="") {
		$driver = EModel::$driver;
		
		//build the query, if any condition is present, attach right conditions
		$q = $driver::insert($what, $this->table, $this->conditions, $this->limit, $this->ordering, $this->orderby, $this->groupby);
		
		$driver::q($q);
		return true;
	}
	
	/**
	 * Return result from a single query.
	 * Example: COUNT(....) returns 56. This method returns 56.
	 */
	public function take($what=" * ", $where="") {
		$driver = EModel::$driver;
		
		$q = $driver::select($what, $this->table, $this->conditions, $this->limit, $this->ordering, $this->orderby, $this->groupby);
		
		$result = $driver::sq($q);
		
		return $result;
		
	}
	
	/**
	 * Performs counts on selected table.
	 */
	public function count($what=array("id"), $additional_fields=array()){
		$driver = EModel::$driver;
		
		$q = $driver::select_count($what, $additional_fields, $this->table, $this->conditions, $this->limit, $this->ordering, $this->orderby, $this->groupby);
		
		$r = $driver::sq($q);
		
		return intval($r);
	}
	
	/**
	 * check if exists a $field in $this->table $where
	 */
	public function is_there($field=" * "){
		$result = $this->count($field);
        
		if($result){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Deletion method.
	 */
	public function delete(){
		$driver = EModel::$driver;
		
		$q = $driver::delete($this->table, $this->conditions, $this->limit);
		
		$driver::q($q);
	}
	
	/**
	 * Returns last inserted id, after and insert()
	 */
	public function last_insert_id(){
		$driver = self::get_driver();
		$r = $driver::last_insert_id();
		return $r;
	}
    
    /*********************************************
     ****** helpers for building other queries
     *********************************************/
    public function count_field($fields){
        $driver = self::get_driver();
		$r = $driver::count($fields);
        return $r;
    }
    
}

?>
