<?php
$user_id = null;
function auth(){
	global $user_id;
	if (!isset(getallheaders()['Bearer'])){
		return;
	}
	db_open();
	$token = db_esc(getallheaders()['Bearer']);
	$token_obj = db_object("select user_id from token t where t.token = '$token';");
	if ($token_obj){
		$user_id = $token_obj["user_id"];
		db_query("update token t set t.use_on = now() where t.token = '$token';");
	}
	db_close();
}
function auth_token(){
	db_open();
	$token = db_esc(getallheaders()['Bearer']);
	db_close();
	return $token;
}