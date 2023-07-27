<?php
/*
 * Global Vars
 */
$connectiontype = "";
$db_all_queries = "";
/*
define('DB_CONNECTION_RO', 'ro');
define('DB_CONNECTION_RW', 'rw');

if(FT_SERVER_NAME === 'mypcisev'){
	define( 'DB_SERVER', "localhost");
	define( 'DB_NAME', "mypcisev_funding");
	define( 'DB_USER_RO', "mypcisev_ro");
	define( 'DB_PASS_RO', "DxTT?{p3yZSh");
	define( 'DB_USER_RW', "mypcisev_rw");
	define( 'DB_PASS_RW', "zKn^Nn]saK6z");
}elseif(FT_SERVER_NAME === 'fundingt'){
	define( 'DB_SERVER', "localhost");
	define( 'DB_NAME', "fundingt_funding");
	define( 'DB_USER_RO', "fundingt_ro");
	define( 'DB_PASS_RO', "3MdhIFF4IQ2usw7F6D");
	define( 'DB_USER_RW', "fundingt_rw");
	define( 'DB_PASS_RW', "KkB2FNPcpEJmlcW6ph");
}elseif(FT_SERVER_NAME === 'azure'){
	define( 'DB_SERVER', "us-cdbr-azure-southcentral-f.cloudapp.net");
	define( 'DB_NAME', "fundingtracking");
	define( 'DB_USER_RO', "b94985f23400d9");
	define( 'DB_PASS_RO', "3e8b72ec");
	define( 'DB_USER_RW', "b94985f23400d9");
	define( 'DB_PASS_RW', "3e8b72ec");
} else {
	throw new Exception('Invalid Server Name');
}
*/

	/*
	 * ------------------------------------------------------------
	 * DB connection and disconnection methods
	 * ------------------------------------------------------------
	 */
function db_connect_ro(){
    global $connection;
	global $connectiontype;
	
	if(isset($connection) && $connectiontype==DB_CONNECTION_RW){
		db_disconnect();
	}
	
	if(empty($connection)){
		
		//$connection = mysql_connect(DB_SERVER, DB_USER_RO, DB_PASS_RO);
		
		$connection = mysqli_connect(DB_SERVER, DB_USER_RO, DB_PASS_RO, DB_NAME);
		if(!$connection) {
			
			die ("Database connection failed: " . mysqli_connect_error());
		}
		mysqli_query($connection, "SET SESSION sql_mode = ''");
	    /*
		$db_select = mysql_select_db(DB_NAME, $connection);
		if (!$db_select) {
			die ("Database selection failed: " . mysql_error());
		}
		*/
		$connectiontype = DB_CONNECTION_RO;
	}
}


function db_connect_rw(){
	
	global $connection;
	global $connectiontype;
	
	if(isset($connection) && $connectiontype==DB_CONNECTION_RO){
		db_disconnect();
	}
	
	if(empty($connection)){
		//$connection = mysql_connect(DB_SERVER, DB_USER_RW, DB_PASS_RW);
		
		$connection = mysqli_connect(DB_SERVER, DB_USER_RW, DB_PASS_RW, DB_NAME);
		if(!$connection) {
			
			die ("Database connection failed: " . mysqli_connect_error());
		}
		mysqli_query($connection, "SET SESSION sql_mode = ''");
	    /*
		$db_select = mysql_select_db(DB_NAME, $connection);
		if (!$db_select) {
			die ("Database selection failed: " . mysql_error());
		}
		*/
		$connectiontype = DB_CONNECTION_RW;
	}
}

function db_disconnect(){
	global $connection;
	global $connectiontype;
	mysqli_close($connection);
	$connection = "";
	$connectiontype = "";
}
	/*
	 * ------------------------------------------------------------
	 * db_execute is used to grab any SQL statements before executed
	 * This just gives me an easy way to grab any SQL statement I may want 
	 * ------------------------------------------------------------
	 */
function db_execute($query) {
	global $connection;
	global $connectiontype;
	// global $db_all_queries;
	if(!$connection || !$connectiontype){
		echo "DB_EXECUTE error, no connection($connection) or connectiontype($connectiontype) defined";
		return false;
	}
	// $db_all_queries .= "conn:$connectiontype query:$query\n";
	//$result = mysql_query($query);
	$result = mysqli_query($connection,$query);
	//if($error = mysql_error()){
	if($error = $connection->connect_errno){
		echo "DB_EXECUTE error, bad query: $query error: $error";
		return false;
	}
	return $result;
}

function ensure_connected(){
	global $connection;
	global $connectiontype;
	if(!$connection || !$connectiontype){
		db_connect_ro();
	}
}
	/*
	 * ------------------------------------------------------------
	 * Where and Like clause builders
	 * ------------------------------------------------------------
	 */
function db_build_where($where){
    global $connection;
	if(is_array($where)){
		ensure_connected();
		$wheres = array();
		foreach($where as $column => $value){
			//array_push($wheres, " $column = '" . mysql_real_escape_string($value) . "' ");
		    array_push($wheres, " $column = '" . $connection->real_escape_string($value) . "' ");
		}
		$where_clause = join(' AND ', $wheres);
		$where_clause = " WHERE $where_clause ";
	} elseif (!empty($where)){
		$where_clause = " WHERE " . $where;
	} else {
		$where_clause = "WHERE 1 ";
	}
	
	return $where_clause;
}


function db_build_like($like){
	global $connection;
	ensure_connected();
	$likes = array();
	foreach($like as $column => $value){
		array_push($likes, " $column LIKE '" . mysqli_real_escape_string($connection,$value) . "%' ");
	}
	$where_clause = join($likes, ' OR ');
	$where_clause = "WHERE $where_clause";
	return $where_clause;
}


function db_build_where_like($where, $like){
	global $connection;
	ensure_connected();
	$wheres = array();
	foreach($where as $column => $value){
		array_push($wheres, " $column = '" . mysqli_real_escape_string($connection,$value) . "' ");
	}
	$where_clause = join($wheres, ' AND ');
	
	$likes = array();
	foreach($like as $column => $value){
		array_push($likes, " $column LIKE '" . mysqli_real_escape_string($connection,$value) . "%' ");
	}
	$like_clause = join($likes, ' OR ');
	
	$where_clause = " WHERE $where_clause AND ( $like_clause ) ";
	
	return $where_clause;
}

	/*
	 * ------------------------------------------------------------
	 * SQL helpful functions
	 * ------------------------------------------------------------
	 */

function db_get_insert_id(){
	global $connection;
	//return mysql_insert_id($connection);
	return mysqli_insert_id($connection);
}

function db_get_affected(){
	global $connection;
	return mysqli_affected_rows($connection);
}

function db_exists_multi($table, $where){
	global $connection;
	db_connect_ro();
	$whereclause = db_build_where($where);
	$sql = "SELECT count(*) c FROM `$table` $whereclause";
	$result = db_execute($sql);
	if(!$result){
		echo "error checking for existance of " . implode(', ', $where). " in $table\n" . mysqli_connect_error() . "\n";
		die;
	}
	$row = mysqli_fetch_assoc($result);
	if($row['c'] > 0){
		return true;
	} else {
		return false;
	}
}

function db_exists($table, $column, $value){
    global $connection;
	db_connect_ro();
	//$sql = "SELECT count(*) c FROM `$table` WHERE `$column` = '" . mysql_real_escape_string($value) . "'";
	$sql = "SELECT * FROM ".$table." WHERE ".$column." = '" . mysqli_real_escape_string($connection, $value) . "'";
	$result = db_execute($sql);
	if(!$result){
		echo "error checking for existance of c:$column = v:$value in t:$table\n" . mysqli_connect_error() . "\n";
		die;
	}
	//$row = mysql_fetch_assoc($result);
	$row = mysqli_fetch_assoc($result);
	if($row){
		return true;
	} else {
		return false;
	}
}

	/*
	 * ------------------------------------------------------------
	 * SQL Select methods
	 * ------------------------------------------------------------
	 */
/* builds and executes a full SQL SELECT statement
 * provide where and like associative arrays and it will automatically check the comparator using mysql_real_escape_string
 * importantly, it returns an array of associative arrays, use db_select_one if you want only a single row */
function db_select($table, $where="", $like="", $columns=" * ", $limit = "", $extra = ""){
	db_connect_ro();
	
	$where_clause = "";
	$like_clause = "";
	$limit_clause = "";
	$columns_clause = "";
	
	if($where){
		if($like){
			//where and like
			$where_clause = db_build_where_like($where, $like);
		} else {
			//just where
			$where_clause = db_build_where($where);
		}
	} elseif ($like){
		//just like
		$where_clause = db_build_like($like);
	} else {
		$where_clause = '';
	}
	//where clause will have everything including the WHERE for the SELECT statement, or nothing (empty)
	
	if(is_array($columns)){
		$columns_clause = implode(', ', $columns);
	} else {
		$columns_clause = $columns;
	}
	//columns_clause will now have either a * or a CSV list of columns to return.
	//note that a single column here could be somehting like count(*) or whatever advanced column you could want
	
	if($limit){
		$limit_clause = "LIMIT $limit";
	}
	//limit clause will be blank or LIMIT + the limit text.
	//Limit should only be the numbers such as '0,30' or whatever
	
	//extra is taken at face value and could contain things like "ORDER BY col asc/desc" without issue
	
	$sql = "SELECT $columns_clause FROM `$table` $where_clause $extra $limit_clause";
	$result = db_execute($sql);
	if(!$result){
		return false;
	}
	$data = array();
	//while($row = mysql_fetch_assoc($result)){
	while($row = mysqli_fetch_assoc($result)){
		array_push($data, $row);
	}
	return $data;
}

/* db_select_one is just a bit safer version of db_select in that it specifies '1' in the db_select call
 * More importantly it will just return the first row of the array */
function db_select_one($table, $where="", $like="", $columns=" * ", $extra = ""){
	$data = db_select($table, $where, $like, $columns, '1', $extra);
	if(!$data){
		return false;
	}
	$assoc = $data[0];
	return $assoc;
}

/* basic full SQL anything goes select query generator, returns one row from db_select_assoc_array
 * Be careful with this as any SQL command passed here is not checked*/
function db_select_assoc($sql){
	db_connect_ro();
	$result = db_select_assoc_array($sql);
	if(!$result){
		return false;
	}
	$row = $result[0];
	return $row;
}

/* basic full SQL anything goes select query generator, returns multiple rows of associative arrays
 * Be careful with this as any SQL command passed here is not checked*/
function db_select_assoc_array($sql){
	db_connect_ro();
	$result = db_execute($sql);
	if(!$result){
		return false;
	}
	$assoc = array();
	//while($row = mysql_fetch_assoc($result)){
    while($row = mysqli_fetch_assoc($result)){
		array_push($assoc, $row);
	}
	return $assoc;
}

/* basic full SQL anything goes select query generator, returns multiple rows of arrays
 * Be careful with this as any SQL command passed here is not checked*/
function db_select_array($sql){
    db_connect_ro();
    $result = db_execute($sql);
    if(!$result){
        return false;
    }
    $assoc = array();
    //while($row = mysql_fetch_assoc($result)){
    while($row = mysqli_fetch_array($result, MYSQLI_NUM)){
    	$assoc[] = $row;
        // array_push($assoc, $row);
    }
    return $assoc;
}
	/*
	 * ------------------------------------------------------------
	 * SQL Update methods
	 * ------------------------------------------------------------
	 */
/* given a tablename, an associative array for data, and an associative array for where
 * builds the SQL statement and calls db_update_bare to execute that query */
function db_update($table, $data, $where){
    global $connection;
	db_connect_rw();

	$where_clause = db_build_where($where);
	$sets = array();

	foreach($data as $key=>$value){
        //array_push($sets, "`$key`= '" . mysql_real_escape_string($value) . "'");
	    array_push($sets, "`$key`= '" . mysqli_real_escape_string($connection, $value) . "'");
	}
	$set_clause = implode(", ", $sets);

	$sql = "UPDATE `$table` SET $set_clause $where_clause";
	$result = db_update_bare($sql);
	if($result || $result == 0){
		return true;
	} else {
		return false;
	}
}

/* used mostly by db_update to run the update sql statement generated therein,
 * But can also be used to run any bare query and get back affected rows */
function db_update_bare($sql){
	db_connect_rw();
	$result = db_execute($sql);
	if(!$result){
		return false;
	}
	$affected = db_get_affected();
	return $affected;
}

	/*
	 * ------------------------------------------------------------
	 * SQL Insert methods
	 * ------------------------------------------------------------
	 */
/* given a single associative array, makes use of db_insert by wrapping the
 * assoc array in another */
function db_insert_assoc_one($table, $data){
	$wrapped_data = array();
	array_push($wrapped_data, $data);
	$result = db_insert_assoc($table, $wrapped_data);
	return $result;
}

/* given a table and a 2d array of associative arrays, inserts those rows into $table
 * basically this strips out the keys of the array to feed the other db_insert */
function db_insert_assoc($table, $data){
	$columns = array_keys($data[0]);
	$result = db_insert($table, $columns, $data);
	return $result;
}

/* give an array of columns, and an array of associative arrays of values
 * returns the number of records inserted */
function db_insert($table, $columns, $data){
	try{
		global $connection;
		db_connect_rw();
		$columns_clause = "";
		$value_clause = "";
		$columns_clause = implode("`, `", $columns);
		$columns_clause = "(`" . $columns_clause . "`)";
		$values = array();
		foreach($data as $row){
			$v = array();
			foreach($columns as $col){
				//array_push($v, mysql_real_escape_string($row[$col]));
				array_push($v, $connection->real_escape_string($row[$col]));
			}
			$v_clause = implode("', '", $v);
			$v_clause = "('" . $v_clause . "')";
			array_push($values, $v_clause);
		}
		$value_clause = implode(', ', $values);
		
		$sql = "INSERT INTO `$table` $columns_clause VALUES $value_clause";
		$result = db_insert_bare($sql);
	}catch(\Exception $ex){
		PrintVar($ex->getMessage());
	}
	return $result;
}

function db_insert_bare($sql){
	db_connect_rw();
	$result = db_execute($sql);

	if(!$result){
		return false;
	}
	$inserted = db_get_insert_id();
	return $inserted;
}

function db_delete_bare($sql){
	global $connection;
	db_connect_rw();
	$result = db_execute($sql);
	if(!$result){
		return false;
	}
	$deleted = mysqli_affected_rows($connection);
	return $deleted;
}