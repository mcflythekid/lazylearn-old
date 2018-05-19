<?php 
function top(){ 
	global $title;
	global $ASSET;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="icon" href="/res/img/favicon.ico">
    <title><?=$title?></title>

	<!-- jquery -->
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/start/jquery-ui.min.css">
	
	<!-- bootstrap -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<!-- Footer -->
	<link rel="stylesheet" href="<?php echo $ASSET?>/footer.css">
	
	<!-- axios -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.17.1/axios.js"></script>
	
	<!-- Include the Quill library -->
	<link href="//cdn.quilljs.com/1.3.4/quill.snow.css" rel="stylesheet">
	<script src="//cdn.quilljs.com/1.3.4/quill.js"></script>
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<!-- bootstrap table -->
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.min.css">
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.min.js"></script>
	
	
	<!-- app -->
	<link rel="stylesheet" href="<?=$ASSET?>/app.css">
	<script src="<?=$ASSET?>/app.js"></script>
	<script src="<?=$ASSET?>/tool.js"></script>
</head>
<body>

  
 <!-- Static navbar -->
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">LAZYLEARN</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">

			<ul class="nav navbar-nav">
				<li class="ui--in"><a href="/">Dashboard</a></li>
				<li><a href="https://pair.lazylearn.com" >Minpair</a></li>
			</ul>

          <ul class="nav navbar-nav navbar-right">
			<li class="ui--out"><a href="/user/register.php">Register</a></li>
			<li class="ui--out"><a href="/user/login.php">Login</a></li>			
			<li class="dropdown ui--in">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
					<span class="email"></span> <span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li class="ui--in"><a href="/user/password.php">Change password</a></li>
					<li class="ui--in"><a href="#" id="logout">Logout</a></li>									
				</ul>
			</li>			
          </ul>
			
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	<script>
	var $top = ((e)=>{
		e.renderIn = (data)=>{
			if (data) {
				$('.navbar-static-top .email').text(data.email);
			}
			$('.ui--in').show();
			$('.ui--out').hide();
		};
		e.renderOut = ()=>{
			$('.ui--in').hide();
			$('.ui--out').show();
		};
		e.init = ()=>{
			if ($app.getData().token){
				e.renderIn($app.getData().email); 
			}else {
				e.renderOut();
			}
			
			$('#logout').click((event)=>{
				event.preventDefault();
				$app.logout();
			});
		};
		return e;
	})({});
	$top.init();
	</script>
	
  
    <div class="container">
<?php } ?>