<?php

	session_start();
	
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
	require_once("../private/remember.php");
	
	if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_SESSION["username"])){
		header("Location: /");
		die();
	}
	
	if (!isset($_POST["id"])){
		echo "Not Found";
		die();
	}
	$id = $_POST["id"];
		
	$con = open_con();
	
	
	$stmt = mysqli_prepare($con, "SELECT id, name, public, cards, category, username FROM sets WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($result) > 0) {
		$set = mysqli_fetch_assoc($result); 
	}else{
		echo "Not Found";
		mysqli_close($con);
		die();
	}
	if ($_SESSION["username"] !== $set["username"] && $_SESSION["username"] !== $ROOT){
		echo "Forbidden";
		mysqli_close($con);
		die();
	}
	
	$stmt = mysqli_prepare($con, "DELETE FROM searches WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$stmt = mysqli_prepare($con, "DELETE FROM sets WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$stmt = mysqli_prepare($con, "DELETE FROM cards WHERE set_id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	
	mysqli_close($con);
	header("Location: /user/" . $_SESSION["username"]);
	die();
?>