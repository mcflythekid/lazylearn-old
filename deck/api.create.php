<?php
require '../core.php';
auth();
require_authed();
require_post();
db_open();
$deck_name = db_esc(request_post("name"));
$deck_id = random_id();
db_query("insert into deck (id, user_id, create_on, name) values ('$deck_id', '$user_id', now(), '$deck_name')");
ok($deck_id);