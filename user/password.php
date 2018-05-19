<?php
	require_once '../core.php';
	title('Change password');
	top();
?>

<div class="row">
	<div class="col-lg-3"></div>
	<div class="col-lg-6">
		<form id="changepassword">
		  <div class="form-group">
			<label for="password">Password</label>
			<input type="password" required class="form-control" id="password" placeholder="Password">
		  </div>
		  <button type="submit" class="btn btn-primary">Change</button>
		</form>
	</div>
</div>
<script>
$changepassword = ((e)=>{
	e.init = ()=>{
		$('#changepassword').on('submit', function(e){
			e.preventDefault();
			$tool.lock();
			$tool.axios.post(ctx + "/user/password.api.php", {
				token: $tool.param('token'),
				password: $('#password').val()
			}).then((res)=>{
				$tool.unlock();
				if (res.data.status === 'error'){
					$tool.flash(0, res.data.data);
				} else if (res.data.status === 'ok'){
					$tool.flash(1, res.data.data);
					$app.logout();
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
	};
	
	return e;
})({});
$changepassword.init();

</script>


<?=bottom()?>