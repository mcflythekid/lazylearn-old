<?php
	session_start();
	require_once("../private/config.php"); 
	require_once("../private/lib.php");
	require_once("../private/remember.php");
	
	if (!isset($_GET["id"])){
		header("Location: /");
		die();
	}
	$id = $_GET["id"];
	
	$con = open_con();
	
	$stmt = mysqli_prepare($con, "SELECT id, name, public, cards, category, username FROM sets WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($result) > 0) {
		$set = mysqli_fetch_assoc($result); 
	}else{
		echo "Not Found";
		mysqli_stmt_close($stmt);
		mysqli_close($con);
		die();
	}
	mysqli_stmt_close($stmt);
	
	if ($set["public"] == 0 && (!isset($_SESSION["username"]) || $_SESSION["username"] !== $set["username"])){
		echo "Forbidden";
		mysqli_close($con);
		die();
	}
	
	if (isset($_POST["g-recaptcha-response"])){
		
		$form_url = "https://www.google.com/recaptcha/api/siteverify";

		$data_to_post = array();
		$data_to_post['secret'] = '6LfIlgsTAAAAAJzTX3QQx8Xh6pCOsyIDfTTmJqO3';
		$data_to_post['response'] = $_POST["g-recaptcha-response"];
		$data_to_post['remoteip'] = $_SERVER['REMOTE_ADDR'];

		$curl = curl_init();

		curl_setopt($curl,CURLOPT_URL, $form_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

		curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);


		$check = curl_exec($curl);


		curl_close($curl);
		
		if (json_decode($check)->success){
		
			$filename=sprintf("%s.txt", $set["name"]);
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", FALSE);
			header("Content-Type: text/plain; charset=UTF-8");
			header("Content-disposition: attachment;filename=\"$filename\"");
			
			$stmt = mysqli_prepare($con, "SELECT id, front, back FROM cards WHERE set_id = ?;");
			mysqli_stmt_bind_param($stmt, "i", $id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) > 0) {
				$str = "";
				while($row = mysqli_fetch_assoc($result)) {
					$str = $str . $row["front"] . "\t" . $row["back"] . "\r\n";
				}
				mysqli_close($con);
				echo $str;
			}else{
				mysqli_close($con);
				echo " ";
			}
			die();
		}else{
			$captcha_failed = true;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Export <?php echo $set["name"]; ?></title>
<link rel="shortcut icon" href="/favicon.ico"  />
<link rel="stylesheet" href="<?php echo $ASSET?>/css/style.css">
<meta name="robots" content="noindex">
</head>
<body>
<div id="wrapper">
<?php require_once("../private/navbar.php"); ?>
<div id="main">




<?php if (isset($captcha_failed)){  ?>
	<div id="warning">Please verify that you are human.</div>
<?php } ?>

<h1 id="export_title">Export <?php echo $set["name"]; ?></h1><br>


<div id="export_back">
	<b><a href="/flashcard/view.php?id=<?php echo $set["id"];?>" class="actionlink"> << Return to Card Set</a></b>
</div>

<div>
	<form method="post">

		<div class="g-recaptcha" data-sitekey="6LfIlgsTAAAAAIHd9V3c4PcwT8A4Lpskpv0LZv0w"></div>
	
		<p>
			<input type="submit" value="Export" class="big_button"/>
		</p>
		
	</form>
</div>


</div>
</div>
<?php require_once("../private/footer.php"); ?>
<script src="//www.google.com/recaptcha/api.js"></script>
</body>
</html>