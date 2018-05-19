<?php 
	session_start();
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
	
	/* Accept post and authenticated user  */
	if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_SESSION["username"])){
		die();
	}
	
	/* Get data */
	if (!isset($_POST["id"])){
		die();
	}
	if (!isset($_POST["front"])){
		die();
	}
	if (!isset($_POST["back"])){
		die();
	}
	$id = $_POST["id"];
	$front = trim($_POST["front"]);
	$back = trim($_POST["back"]);
	if (empty($front) || empty($back) || strlen($front) > 1024 ||  strlen($back) > 1024){
		die();
	}
	
	/* Open database */
	$con = open_con();
	
	/* Check username */
	$stmt = mysqli_prepare($con, "SELECT username, step FROM cards WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($result) > 0) {
		$card = mysqli_fetch_assoc($result);
		if ($_SESSION["username"] !== $card["username"]){
			mysqli_stmt_close($stmt);
			mysqli_close($con);
			die();
		}
	}else{
		die();
	}
	
	/* Change step */
	$stmt = mysqli_prepare($con, "UPDATE cards SET front = ?, back = ? WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "ssi", $front, $back, $id);
	mysqli_stmt_execute($stmt);
	mysqli_close($con);
	

?>