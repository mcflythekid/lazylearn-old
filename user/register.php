<?php
	require_once '../core.php';
	title('Register');
	top();
?>

<div class="row">
	<div class="col-lg-3"></div>
	<div class="col-lg-6">
		<form id="register">
		  <div class="form-group">
			<label for="email">Email</label>
			<input type="text" required class="form-control" id="email" placeholder="Email">
		  </div>
		  <div class="form-group">
			<label for="password">Password</label>
			<input type="password" required class="form-control" id="password" placeholder="Password">
		  </div>
		  <button type="submit" class="btn btn-primary">Register</button>
		</form>
	</div>
</div>
<script>
$register = ((e)=>{
	e.init = ()=>{
		$('#register').on('submit', function(e){
			e.preventDefault();
			$tool.lock();
			$tool.axios.post(ctx + "/user/register.api.php", {
				email: $('#email').val(),
				password: $('#password').val()
			}).then((res)=>{
				$tool.unlock();
				if (res.data.status === 'error'){
					$tool.flash(0, res.data.data);
				} else if (res.data.status === 'ok'){
					if (res.data.data.token){
						$app.setData(res.data.data);
						location = ctx + "/";
					} else {
						$tool.flash(1, res.data.data);
					}
				} else {
					$tool.flash(0, 'ERROR');
					console.log(res.data);
				}
				
			}).catch((err)=>{
				$tool.unlock();
				$tool.flash(0, 'ERROR');
				console.log(err);
			});
		});
		$(document).ready(function(){
			if ($app.getData().token){
				$tool.flash(2, 'Đã được đăng nhập trước đó');
				location = ctx + "/";
			}
		});
	};
	
	return e;
})({});
$register.init();

</script>


<?=bottom()?>