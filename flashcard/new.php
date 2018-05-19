<?php

	session_start();
	
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
	require_once("../private/remember.php");
	
	if (!isset($_SESSION["username"])){
		header("Location: /login.php?redirect=/flashcard/new.php");
		die();
	}
	
	$new_failed = false;
	
	if ($_SERVER['REQUEST_METHOD'] == "POST"){
	
		$name = trim($_POST["name"]);
		$category = trim($_POST["category"]);
		if (isset($_POST["private"]) && $_POST["private"] == "on"){
			$public = 0;
		}else{
			$public = 1;
		}

		
		if (preg_match("/^(.{1,250})$/", $name) &&  preg_match("/^([^\/\?\&]{0,30})$/", $category)){
			
			$url = strtolower($name);
			$url = trim($url);
			$url = preg_replace('/đ/', 'd', $url);
			$url = iconv('UTF-8', 'ASCII//TRANSLIT', $url);
			$url = preg_replace('/[^a-z0-9]/', '-', $url);
			$url = preg_replace('/[\-]+/', '-', $url);
			$url = trim($url, '-');
			if (strlen($url) > 0) { $url.= "-"; }
			
			$con = open_con();
			$stmt = mysqli_prepare($con, "INSERT INTO sets (name,category,username, public, url) value (?,?,?,?, ?);");
			mysqli_stmt_bind_param($stmt, "sssis", $name, $category , $_SESSION["username"], $public, $url);
			mysqli_stmt_execute($stmt);
				
			$new_set_id = mysqli_insert_id($con);
			
			if ($public == 1){
				$stmt = mysqli_prepare($con, "INSERT INTO searches (id, name) value (?,?);");
				mysqli_stmt_bind_param($stmt, "is", $new_set_id, strtolower($name));
				mysqli_stmt_execute($stmt);
			}

			if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) { /*checks that file is uploaded */
				
				$query = "INSERT INTO cards (set_id, username, front, back) values ";
				$file = fopen($_FILES['file']['tmp_name'], "r");
				$cards_count = 0;
				while(!feof($file)){
					$line = fgets($file);
					$arr = explode("\t", $line);  
					if (sizeof($arr) >= 2){
						$front = trim($arr[0]);
						$back = trim($arr[1]);
						if (!empty($front) && !empty($back) && strlen($front) <= 1024 &&  strlen($back) <= 1024){
							$cards_count++;
							$front = mysqli_real_escape_string($con, $front);
							$back = mysqli_real_escape_string($con, $back);
							$query = $query . sprintf("(%u, '%s', '%s', '%s')", $new_set_id, $_SESSION["username"], $front, $back ) . ",";
						}
					}
				}
				$query = rtrim($query, ",") . ";";
				fclose($file);
				unlink($_FILES['file']['tmp_name']);
								
				if ($cards_count > 0){
					$stmt = mysqli_prepare($con, $query);
					mysqli_stmt_execute($stmt);
					$stmt = mysqli_prepare($con, "UPDATE sets SET cards = ?  WHERE id = ?;");
					mysqli_stmt_bind_param($stmt, "is", $cards_count,$new_set_id );
					mysqli_stmt_execute($stmt);
				}

			}
			mysqli_close($con);
			header("Location: /flashcard/" . $url . $new_set_id);
			die();
		}else{
			$new_failed = true;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
<title><?=$lang["flashcard"]["new"]["title"]?></title>
<link rel="shortcut icon" href="/favicon.ico"  />
<link rel="stylesheet" href="<?php echo $ASSET?>/css/style.css">
</head>
<body>
<div id="wrapper">
<?php require_once("../private/navbar.php"); ?>
<div id="main">




<div id="set_new_edit" class="greybox">
	<h1><?=$lang["flashcard"]["new"]["h1"]?></h1>
	<form method="post" id="form" enctype="multipart/form-data" accept-charset="UTF-8">
		<p><label for="name"><strong><?=$lang["flashcard"]["new"]["name"]?></strong></label><br><input name="name" size="60" value="<?php if (isset($_POST['name'])) {echo trim($_POST['name']);} ?>"/><br><span class="error" id="error_name"></span></p>
		<p><a href="javascript:void(0)" id="showhide">&#x25BC; <?=$lang["flashcard"]["new"]["show_options"]?></a></p>
		<div id="advance">
			<p>
				<label for="category"><strong><?=$lang["flashcard"]["new"]["category"]?> </strong>(<small><?=$lang["flashcard"]["new"]["optional"]?></small>)<br><input name="category" size="30" value="<?php if (isset($_POST['category'])) {echo trim($_POST['category']);} ?>"/>
				<input name="private" id="private" type="checkbox"><label id="private" for="private"><strong><?=$lang["flashcard"]["new"]["private"]?> </strong>(<small><?=$lang["flashcard"]["new"]["optional"]?></small>)</label>
				<br><span class="error" id="error_category"></span>
			</p>
			<p>
				<label id="file" for="file"><strong><?=$lang["flashcard"]["new"]["import_data"]?> </strong>(<small><?=$lang["flashcard"]["new"]["optional"]?></small>)</label><br><input name="file" id="file" type="file" accept="text/plain">
			</p>
		</div>
		<p><input class="big_button" type="submit" value="<?=$lang["flashcard"]["new"]["create"]?>" /></p>
	</form>
</div>
	
	
	
	
</div>	
</div>
<?php require_once("../private/footer.php"); ?>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="<?php echo $ASSET?>/js/cardsets_new_edit.js"></script>
<?php if ($new_failed){  ?><script>check_name();check_category();</script><?php } ?>
</body>
</html>