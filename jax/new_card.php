<?php 
	session_start();
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
		
	// require post ans session
	if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_SESSION["username"])){
		error('Login is required / Wrong method');
	}
	
	// require fields
	$post = post_2_json();
	if (!isset($post["set_id"])){
		error('set_id is null');
	}
	if (!isset($post["front"])){
		error('front is null');
	}
	if (!isset($post["back"])){
		error('back is null');
	}
	
	// validate fields
	
	$set_id = $post["set_id"];
	$front = trim($post["front"]);
	$back = trim($post["back"]);
	if (empty($front) || empty($back) || strlen($front) > 1000000 ||  strlen($back) > 1000000){
		error();
	}
	
	/* Check owner */
	$con = open_con();
	$stmt = mysqli_prepare($con, "SELECT username FROM sets WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $set_id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($result) > 0) {
		$set = mysqli_fetch_assoc($result);
		if ($_SESSION["username"] !== $set["username"]){
			mysqli_stmt_close($stmt);
			mysqli_close($con);
			error();
		}
	}else{
		error();
	}
	
	/* Insert */
	$stmt = mysqli_prepare($con, "INSERT INTO cards (username, front, back, set_id) VALUES(?,?,?,?);");
	mysqli_stmt_bind_param($stmt, "sssi", $_SESSION["username"], $front, $back, $set_id);
	mysqli_stmt_execute($stmt);
	$new_id = mysqli_insert_id($con);
	$stmt = mysqli_prepare($con, "UPDATE sets SET cards = cards + 1 WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $set_id);
	mysqli_stmt_execute($stmt);
	mysqli_close($con);
	echo $new_id;
?>