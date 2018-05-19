<?php 
	session_start();
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
	
	/* Accept post and authenticated user */
	if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_SESSION["username"])){
		die();
	}
	
	/* Get id */
	if (!isset($_POST["id"])){
		die();
	}
	$id = $_POST["id"];
	
	/* Open database */
	$con = open_con();
	
	/* Check username */
	$stmt = mysqli_prepare($con, "SELECT username, step, set_id FROM cards WHERE id = ?;");
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
	$stmt = mysqli_prepare($con, "DELETE FROM cards WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$stmt = mysqli_prepare($con, "UPDATE sets SET cards = cards - 1 WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $card["set_id"]);
	mysqli_stmt_execute($stmt);
	mysqli_close($con);

?>