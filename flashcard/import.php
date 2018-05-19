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
	
		$lines = $_POST["text"];
		if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) { /*checks that file is uploaded */
			$lines = $lines . "\r\n" . file_get_contents($_FILES['file']['tmp_name']);
			unlink($_FILES['file']['tmp_name']);
		}
		
		$query = "INSERT INTO cards (set_id, username, front, back) values ";
		//$file = fopen($_FILES['file']['tmp_name'], "r");
		$cards_count = 0;
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $lines) as $line){
			$arr = explode("\t", $line);  
			if (sizeof($arr) >= 2){
				$front = trim($arr[0]);
				$back = trim($arr[1]);
				if (!empty($front) && !empty($back) && strlen($front) <= 1024 &&  strlen($back) <= 1024){
					$cards_count++;
					$front = mysqli_real_escape_string($con, $front);
					$back = mysqli_real_escape_string($con, $back);
					$query = $query . sprintf("(%u, '%s', '%s', '%s')", $id, $_SESSION["username"], $front, $back ) . ",";
				}
			}
		} 
		$query = rtrim($query, ",") . ";";
		//fclose($file);
		
							
		if ($cards_count > 0){
			$stmt = mysqli_prepare($con, $query);
			mysqli_stmt_execute($stmt);
			$stmt = mysqli_prepare($con, "UPDATE sets SET cards = (SELECT COUNT(*) FROM cards WHERE set_id = ? ) WHERE id = ?;");
			mysqli_stmt_bind_param($stmt, "ii", $id, $id );
			mysqli_stmt_execute($stmt);
		}
		
		mysqli_close($con);
		header("Location: /flashcard/view.php?id=" . $id);
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Import: <?php echo noHTML($set["name"]); ?></title>
<link rel="shortcut icon" href="/favicon.ico"  />
<link rel="stylesheet" href="<?php echo $ASSET?>/css/style.css">
</head>
<body>
<div id="wrapper">
<?php require_once("../private/navbar.php"); ?>
<div id="main">




<div id="set_import" class="greybox">
	<h1>Import: <?php echo noHTML( $set["name"]); ?></h1>
	<form method="post" id="form" enctype="multipart/form-data">
		<p>
			<label id="file" for="file">From file:</label><br><input name="file" id="file" type="file" accept="text/plain">
		</p>
		<p>
			<label id="text" for="file">From text:</label><br><textarea rows="15" cols="74" name="text" id="text"></textarea>
		</p>
		<p><input class="big_button" type="submit" value="Import" /></p>
	</form>
</div>




</div>
</div>
<?php require_once("../private/footer.php"); ?>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="<?php echo $ASSET; ?>/js/cardsets_import.js"></script>
</body>
</html>