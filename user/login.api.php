<?php
require_once '../core.php';

require_post();
auth();
if (is_authed()) ok('Already logged in');

db_open();
$email = db_esc(request_post('email'));
$password = request_post('password');

$user = db_object("select * from user u where u.email = '$email'");
if (!$user) error('Email not found');
if (md5($password . $SALT) !== $user['hashed_password']) error('Wrong password');

$token = random_id(256);
$user_id = $user["id"];
db_query("insert into token(user_id, token, create_on, use_on) values('$user_id', '$token', now(), now())");
ok(['token'=> $token, 'email'=> $email]);