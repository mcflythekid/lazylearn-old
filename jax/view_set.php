<?php 
	session_start();
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
		
		
		
	// validate
	if (!isset($_GET["id"])){
		error('id is null');
	}
	$set_id = $_GET["id"];

	
	/* query */
	$con = open_con();
	$stmt = mysqli_prepare($con, "SELECT id , front, back FROM cards WHERE set_id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $set_id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$cards = array();
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			$cards[] = $row;
		}
	}
	mysqli_stmt_close($stmt);
	mysqli_close($con);
	
	ok($cards);
?>