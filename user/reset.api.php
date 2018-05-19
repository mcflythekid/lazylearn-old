<?php
require '../core.php';
require_post();

db_open();
$token = db_esc(request_post('token'));
$password = db_esc(request_post('password'));
$hashed_password = md5($password . $SALT);

$forget = db_object("select * from forget u where u.token = '$token'");
if (!$forget) error('Token not found');
$user_id = $forget["user_id"];

db_query("delete from forget where token = '$token';");
db_query("update user set hashed_password = '$hashed_password', update_on = now() where id = '$user_id';");
ok('Change password success');