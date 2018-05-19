<?php
require '../core.php';
require_post();
db_open();
$email = db_esc(request_post('email'));

$user = db_object("select * from user where email = '$email';");
if (!$user) ok('If you are correct, then the email was sent');
$user_id = $user["id"];
$id = random_id();
$token = random_id(256);
$url = is_dev()? 'http://lazylearn.localhost' : 'https://lazylearn.com';
db_query("insert into forget (id, user_id,token, create_on) values('$id', '$user_id', '$token', now());");
$msg = sprintf("Dear user<br><br>Your token is<br>%s<hr>Please click here<br><a href='%s/user/reset.php?token=%s'>%s/user/reset.php?id=%s</a><br><br>Sincerely,<br>Lazylearn Team", $token, $url, $token, $url, $token);
$msg = wordwrap($msg,70);
$headers =  'MIME-Version: 1.0' . "\r\n"; 
$headers .= 'From: Lazylean Team <support@lazylearn.com>' . "\r\n";
$headers .= 'Content-type: text/html; utf-8' . "\r\n"; 
mail($email,"[Lazylearn Team] Reset your password",$msg, $headers);
ok('If you are correct, then the email was sent');