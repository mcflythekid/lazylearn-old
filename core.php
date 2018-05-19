<?php
// Deploy data---------------------------------------------------------------------------------------------------
require_once __DIR__ . '/config.php';

// Configurations---------------------------------------------------------------------------------------------------
$TIMEZONE = 'UTC';
$VERSION = 53;
$ROOT = "mcflythekid";
$REMEMBER_ME_DAY = 30;
$FORGET_PASSWORD_DAY = 1;
$LEITNER[1] = 86400; // 1 day
$LEITNER[2] = 259200; // 3 days
$LEITNER[3] = 604800; // 7 days
$LEITNER[4] = 2592000; // 30 days
$LEITNER[5] = 10368000; // 4 months
$LEITNER[6] = 63072000; // 2 years

// Automatic configurations---------------------------------------------------------------------------------------------------
date_default_timezone_set($TIMEZONE);
if (is_dev()){
	$ASSET =  "/res_" . rand(1,10000);
} else {
	$ASSET =  "/res_" . $VERSION;
}
$LEITNER_SIZE = sizeof($LEITNER);


// Load files---------------------------------------------------------------------------------------------------
require_once __DIR__ . '/component/auth.php';
require_once __DIR__ . '/component/db.php';
require_once __DIR__ . '/component/top.php';
require_once __DIR__ . '/component/bottom.php';

// Require---------------------------------------------------------------------------------------------------
function require_post(){
	if ($_SERVER['REQUEST_METHOD'] !== "POST"){
		error('POST is required');
	}
}
function require_authed(){
	if (!is_authed()){
		error('Login is required');
	}
}

// Is --------------------------------
function is_authed(){
	global $user_id;
	return isset($user_id);
}
function is_post(){
	return $_SERVER['REQUEST_METHOD'] == "POST";
}
function is_dev() {
	$whitelist = array( '127.0.0.1', 'localhost', 'lazylearn.localhost' );
	if( in_array( $_SERVER['SERVER_NAME'], $whitelist) )
		return true;
	return false;
}

//Common-------------------------------
function dump($variable){
	echo '<pre>' . var_dump($variable) . '</pre>';
	die;
}
function redirect($url){
	header("Location:" . $url);
	die;
}
function title($str){
	global $title;
	$title = $str;
}
function request_post($key = null){
	if ($key) return json_decode(file_get_contents('php://input'), true)[$key];
	return json_decode(file_get_contents('php://input'), true);
}
function random_id($length = 32) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Response
function error($data){
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([
			'status' => 'error',
			'data' => $data
	]);
	die;
}
function json($data){
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data);
	die;
}
function ok($data){
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([
			'status' => 'ok',
			'data' => $data
	]);
	die;
}
