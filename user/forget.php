<?php
	require_once '../core.php';
	title('Forget password');
	top();
?>

<div class="row">
	<div class="col-lg-3"></div>
	<div class="col-lg-6">
		<form id="forget">
		  <div class="form-group">
			<label for="email">Email</label>
			<input type="text" required class="form-control" id="email" placeholder="Email">
		  </div>
		  <button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
</div>
<script>
$forget = ((e)=>{
	e.init = ()=>{
		$('#forget').on('submit', function(e){
			e.preventDefault();
			$tool.lock();
			$tool.axios.post(ctx + "/user/forget.api.php", {
				email: $('#email').val(),
				password: $('#password').val()
			}).then((res)=>{
				$tool.unlock();
				if (res.data.status === 'error'){
					$tool.flash(0, res.data.data);
				} else if (res.data.status === 'ok'){
					if (res.data.data.token){
						$app.setToken(res.data.data.token);
						window.location = ctx + "/";
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
			if ($app.getToken()){
				$tool.flash(2, 'Đã được đăng nhập trước đó');
				window.location = ctx + "/";
			}
		});
	};
	
	return e;
})({});
$forget.init();

</script>


<?=bottom()?>