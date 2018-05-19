<?php

	session_start();
	
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
	require_once("../private/remember.php");
	
	if (!isset($_GET["id"])){
		echo "Not Found";
		die();
	}
	$id = $_GET["id"];
	
	if (!isset($_SESSION["username"])){
		header("Location: /login.php?redirect=/flashcard/edit.php?id=" . $id);
		die();
	}
	
	$new_failed = false;
	
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

	if ($_SESSION["username"] !== $set["username"]){
		echo "Forbidden";
		mysqli_close($con);
		die();
	}
	
	if ($_SERVER['REQUEST_METHOD'] == "POST"){
	
		$name = trim($_POST["name"]);
		$category = trim($_POST["category"]);
		if (isset($_POST["private"]) && $_POST["private"] == "on"){
			$public = 0;
		}else{
			$public = 1;
		}

		if (preg_match("/^(.{1,250})$/", $name) &&  preg_match("/^(.{0,30})$/", $category)){
							
			$con = open_con();
			$stmt = mysqli_prepare($con, "UPDATE sets SET name = ?, category = ?, public = ? WHERE id = ?;");
			mysqli_stmt_bind_param($stmt, "ssis", $name, $category, $public, $id);
			mysqli_stmt_execute($stmt);
				
			if ($public == 0){
				$stmt = mysqli_prepare($con, "DELETE FROM searches WHERE id = ?;");
				mysqli_stmt_bind_param($stmt, "i", $id);
				mysqli_stmt_execute($stmt);
			}else{
				$stmt = mysqli_prepare($con, "REPLACE INTO searches(id, name) VALUES(?, ?);");
				mysqli_stmt_bind_param($stmt, "is", $id, strtolower($name));
				mysqli_stmt_execute($stmt);
			}	
			
			mysqli_close($con);
			header("Location: /flashcard/view.php?id=" . $id);
			die();
		}else{
			$new_failed = true;
		}
	}
	
	mysqli_close($con);
?>
<!DOCTYPE html>
<html>
<head>
<title>Setting: <?php echo noHTML($set["name"]); ?></title>
<link rel="shortcut icon" href="/favicon.ico"  />
<link rel="stylesheet" href="<?php echo $ASSET?>/css/style.css">
</head>
<body>
<div id="wrapper">
<?php require_once("../private/navbar.php"); ?>
<div id="main">




<div id="set_new_edit" class="greybox">
	<h1>Setting: <?php echo noHTML($set["name"]); ?></h1>
	<form method="post" id="form" enctype="multipart/form-data">
		<p><label for="name">Name:</label><br><input name="name" size="60" value="<?php echo noHTML($set["name"]); ?>"/><br><span class="error" id="error_name"></span></p>
		<p>
			<label for="category">Category:<br><input name="category" size="30" value="<?php echo noHTML($set["category"]); ?>"/>
			<input name="private" id="private" type="checkbox" <?php if ($set["public"] == 0) echo "checked"; ?>><label id="private" for="private">Private</label>
			<br><span class="error" id="error_category"></span>
		</p>
		<p><input class="big_button" type="submit" value="Update" /></p>
	</form>
</div>




</div>
</div>
<?php require_once("../private/footer.php"); ?>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="<?php echo $ASSET; ?>/js/cardsets_new_edit.js"></script>
<?php if ($new_failed){  ?><script>check_name();check_category();</script><?php } ?>
</body>
</html>