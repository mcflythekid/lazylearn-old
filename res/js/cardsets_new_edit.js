// Focused when ready
$("a#showhide").click(function(){
	$("div#advance").toggle();
	if ($("div#advance").is(":hidden")) {
		$("a#showhide").html("&#x25BC; Show options");
	} else {
		$("a#showhide").html("&#x25B2; Hide options");
	}
});


// Check on submit
$('#form').submit(function (e) {
	check_name(e);
	check_category(e);
});

// Check name when blur
$("input[name=name]").blur(function() {
	check_name();
});

// Check category when blur
$("input[name=category]").blur(function() {
	check_category();
});

// Name
function check_name(e){
	var name = $('input[name=name]').val();
	name = $.trim(name);
	if (!name){
		$("#error_name").html("You can't leave this empty.");
		if (e){e.preventDefault();}
		return;
	} else if (!/^(.{1,250})$/.test(name)){
		$("#error_name").html("Maximum length is 250.");
		if (e){e.preventDefault();}
		return;
	}
	$("#error_name").html("");
}

// Category
function check_category(e){
	var category = $('input[name=category]').val();
	category = $.trim(category);
	if (category && !/^([^\/\?\&]{0,30})$/.test(category)){
		$("#error_category").html("Maximum length is 30, not contains '/?&'");
		if (e){e.preventDefault();}
		return;
	}
	$("#error_category").html("");
}