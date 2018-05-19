// Check on submit
$('#form').submit(function (e) {
	check_username(e);
	check_password(e);
	check_password2(e);
});

// Check username when blur
$("input[name=username]").blur(function() {
	check_username();
});

// Check password when blur
$("input[name=password]").blur(function() {
	check_password();
});

// Check password2 when blur
$("input[name=password2]").blur(function() {
	check_password2();
});

// Username
function check_username(e){
	var username = $('input[name=username]').val();
	if (!username){
		$("#error_username").html("You can't leave this empty.");
		if (e){e.preventDefault();}
		return;
	} else if (!/^([a-z0-9]{3,30})$/.test(username)){
		$("#error_username").html("Use a-z, 0-9, from 3 to 30 characters.");
		if (e){e.preventDefault();}
		return;
	} else{
		$.post("/jax/check_username.php", {username : username},  function(result){
			if(result === "0"){
				$("#error_username").html('This username is already registered.'); // TODO
			}else{
				$("#error_username").html("");
			}
		});  
	}
}

// Password
function check_password(e){
	var password = $('input[name=password]').val();
	if (!password){
		$("#error_password").html("You can't leave this empty.");
		if (e){e.preventDefault();}
		return;
	} else if (!/^(.{8,254})$/.test(password)){
		$("#error_password").html("From 8 to 254 character.");
		if (e){e.preventDefault();}
		return;
	}
	$("#error_password").html("");
}

// Password2
function check_password2(e){
	var password = $('input[name=password]').val();
	var password2 = $('input[name=password2]').val();
	if (!password2){
		$("#error_password2").html("You can't leave this empty.");
		if (e){e.preventDefault();}
		return;
	} else if (password && password !== password2){
		$("#error_password2").html("Password not matched, please try again.");
		if (e){e.preventDefault();}
		return;
	}
	$("#error_password2").html("");
}