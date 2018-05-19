<?php
$con = null;
function db_esc($str){
	global $con;
	return mysqli_real_escape_string($con, $str);
}
function db_open(){
	global $con;
	global $MYSQL_SERVER;
	global $MYSQL_USER;
	global $MYSQL_PASS;
	global $MYSQL_DB;
	$con = mysqli_connect($MYSQL_SERVER, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
	if (!$con) {
		error("Connection failed: " . mysqli_connect_error());
	}
	return $con;
	mysqli_set_charset($con,"utf8");
}
function db_close(){
	global $con;
	mysqli_close($con);
}
function db_object($sql){
	global $con;
	$rs = mysqli_query($con, $sql);
	if (mysqli_num_rows($rs) > 0) {
		$obj = mysqli_fetch_assoc($rs); 
		return $obj;
	}
	return false;
}
function db_count($sql){
	global $con;
	$rs = mysqli_query($con, $sql);
	if (mysqli_num_rows($rs) > 0) {
		$obj = mysqli_fetch_assoc($rs);
		return $obj["count(*)"];
	}
	return false;
}
function db_list($sql){
	global $con;
	$rs = mysqli_query($con, $sql);
	if (mysqli_num_rows($rs) > 0) {
		
		$rows = array();
		while($row = mysqli_fetch_assoc($rs)) {
			$rows[] = $row;
		}
		return $rows;
	}
	return array();
}
function db_query($sql){
	global $con;
	return mysqli_query($con, $sql);
}
function db_insert_id(){
	global $con;
	return mysqli_insert_id($con);
}