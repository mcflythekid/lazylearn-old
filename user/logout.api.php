<?php
require_once '../core.php';
require_post();
auth();
if (!is_authed()) ok('already not logged in');

$token = auth_token();
db_open();
db_query("delete from token where token = '$token'");
db_close();
ok('token deleted');