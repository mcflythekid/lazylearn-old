<?php
require '../core.php';
require_post();
auth();
if (is_authed()) error('already logged in');

db_open();
$email = db_esc(request_post('email'));
$password = db_esc(request_post('password'));
$hashed_password = md5($password . $SALT);
$user_id = random_id();

$user = db_object("select * from user u where u.email = '$email'");
if ($user) error('email already exists');

db_query("insert into user (id, email, hashed_password, create_on) values('$user_id', '$email', '$hashed_password', now())");

$token = random_id(256);
db_query("insert into token(user_id, token, create_on, use_on) values('$user_id', '$token', now(), now())");
ok(['token'=> $token, 'email'=> $email]);