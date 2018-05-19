<?php
require '../core.php';
auth();
require_authed();
db_open();
$sort = db_esc($_GET["sort"]);
$order = db_esc($_GET["order"]);
$limit = db_esc($_GET["limit"]);
$offset = db_esc($_GET["offset"]);
if (isset($_GET["search"])){
	$search = db_esc($_GET["search"]);
	$condition = " and deck.name like '%$search%'";
} else {
	$condition = "";
}

$sql= 
 "select deck.id, deck.name, deck.create_on, deck.update_on, count(card.id) as quantity
 from deck
 left join card on card.deck_id = deck_id
 where deck.user_id = '$user_id' $condition
 group by deck.id, deck.name, deck.create_on, deck.update_on
 order by $sort $order limit $limit offset $offset 
 ";
$count = db_count("select count(*) from deck where deck.user_id = '$user_id' $condition");
$list = db_list($sql);
json([
	'rows' => $list,
	'status' => 'ok',
	'total' => $count
]);