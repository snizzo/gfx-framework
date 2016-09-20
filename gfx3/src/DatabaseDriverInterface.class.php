<?php

interface DatabaseDriverInterface
{
	/**
	 * Load configuration from configuration files. All data needed has to be
	 * store inside the static driver class, therefore the method
	 * @return void
	 */
	static public function load();
	static public function open_session();
	
	/**
	 * Assures fields are safe
	 */
	public static function safe($s);
	/**
	 * Execute a generic query $q
	 */
	public static function q($q);
	/**
	 * Execute a generic query $q with a single return like COUNT()
	 */
	public static function sq($q);
	/**
	 * Checks if $table (in string format) exists
	 * and @returns true or false 
	 */
	public static function table_exists($table);
	public static function fetch_assoc($result);
	public static function last_insert_id();
	public static function unload();
	
    /**
     * Query builder
     */
    public static function add_first_condition($c);
    public static function add_consequent_condition($c);
    public static function limit($from,$limit);
    public static function ascending();
    public static function descending();
    public static function orderby($field);
    public static function groupby($field);
    public static function count($fields);
    
    public static function select($what,$tablename,$conditions,$limit,$ordering,$orderby,$groupby);
    public static function select_count($what,$additional_fields,$tablename,$conditions,$limit,$ordering,$orderby,$groupby);
    public static function insert($what,$tablename,$conditions,$limit,$ordering,$orderby,$groupby);
    public static function delete($tablename,$conditions,$limit);
    public static function update($what,$tablename,$conditions,$limit,$ordering,$groupby);
    
	/**
	 * Human readable string with real provider
	 * like mysqli, sqlite etc...
	 */
	public static function provider();
	
	
}

?>
