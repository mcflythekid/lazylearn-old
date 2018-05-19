<?php
require '../core.php';
auth();
require_post();
require_authed();

db_open();
$password = db_esc(request_post('password'));
$hashed_password = md5($password . $SALT);
db_query("update user set hashed_password = '$hashed_password', update_on = now() where id = '$user_id';");
ok('Change password success');