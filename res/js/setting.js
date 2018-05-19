$("input[name=email]").keypress(function (e) {
  if (e.which == 13) {
    $("input#change_email" ).click();
    return false; 
  }
});
$("input[name=email]").blur(function(e) {
	$("#error_email").html("");
});
$("input#change_email" ).click(function() {
	
	// Get email
	var email = $.trim($('input[name=email]').val());
	
	// Skip if no changed
	if (email == old_email){
		return;
	}
	
	// Disable button
	$("input#change_email" ).prop('disabled', true);
	$("input#change_email" ).val("Changing...");
	$("#error_email").html("");
	
	
	// Send ajax
	$.post("/jax/change_email.php", {email : email},  function(result){
		
		if(result == 0){ // Duplicated
			$("#error_email").html('<a style="color:red;">This email address is already registered, please try another one.</a>'); // TODO
		}else if (result == 1){ // Success change
			old_email = email;
			$("a#new_email").html(email);
			$("#error_email").html('<a style="color:green;">Success.</a>'); // TODO
		}else if (result == -1){ // Wrong email
			$("#error_email").html('<a style="color:red;">Invalid email address.</a>'); // TODO
		}else if (result == 2){ // Success remove
			$("a#new_email").html("Not set");
			old_email = email;
			$("#error_email").html('<a style="color:green;">Email address removed.</a>'); // TODO
		}
		
		// Restore button
		$("input#change_email" ).prop('disabled', false);
		$("input#change_email" ).val("Change");
	});  
});




// Check on submit
$('#form').submit(function (e) {
	check_password1(e);
	check_password2(e);
});
// Check password when blur
$("input[name=password1]").blur(function() {
	check_password1();
});
// Check password2 when blur
$("input[name=password2]").blur(function() {
	check_password2();
});
function check_password1(e){
	var password1 = $('input[name=password1]').val();
	if (!password1){
		$("#error_password1").html("You can't leave this empty.");
		if (e){e.preventDefault();}
		return;
	} else if (!/^(.{8,254})$/.test(password1)){
		$("#error_password1").html("From 8 to 254 character.");
		if (e){e.preventDefault();}
		return;
	}
	$("#error_password1").html("");
}
function check_password2(e){
	var password1 = $('input[name=password1]').val();
	var password2 = $('input[name=password2]').val();
	if (!password2){
		$("#error_password2").html("You can't leave this empty.");
		if (e){e.preventDefault();}
		return;
	} else if (password1 && password1 !== password2){
		$("#error_password2").html("Password not matched, please try again.");
		if (e){e.preventDefault();}
		return;
	}
	$("#error_password2").html("");
}