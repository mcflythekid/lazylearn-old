<?php
require '../core.php';
auth();
if (!is_authed()) error('error');
db_open();
$user = db_object("select email from user where id = '$user_id'");
ok(["email" => $user["email"]]);