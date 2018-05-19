var $tool = ((e)=>{
	
	// ui ----------------------------------------------------------
	e.flash = (type,content)=>{
		if($('#wrapperShowHide').length==0){
			var wrapper = '<div id="wrapperShowHide" style="width: 300px;top:5%; left:69%;position: fixed;z-index:10000; font-size:13px"></div>';
			$('body').before(wrapper);
		}
		var count=0;
		while($('#showHideMessage'+count).length>0){
			count++;
		}
		var messageType="alert-success";
		var header="";
		switch(type) {
			case 0:
				messageType="alert-danger";
				header="";
				break;
			case 1:
				messageType="alert-success";
				header="";
				break;
			case 2:
				messageType="alert-warning";
				header="";
				break;
		}
		var content = '<div class="alert '+messageType+'" id="showHideMessage'+count+'" style="width: 400px;"><button type="button" class="close" data-dismiss="alert">x</button><strong>'+header+'</strong>'+content+'</div>';
		$("#wrapperShowHide").append(content);
		
		$("#showHideMessage"+count).fadeTo(5000, 1500).fadeOut(1000, function(){
			$(this).alert('close');
			if($('#wrapperShowHide > div').length==0){
				$('#wrapperShowHide').remove();
			}
		});   
	};
	e.lock= function(isDisable = true, id = "body"){
		var genId=id +"loading";
		var prefix="";
		var position="fixed";
		var measure="100%";
		if(id != "body"){
			prefix="#";
			position="absolute";
			measure="100%";
		}
		if(isDisable === true){
			var divloading = '<div class="screenlocker" id="'+genId+'" style="position:'+position+'; z-index:100000; top:0;'; 
			divloading += 'left:0;height:'+measure+';width: '+measure+'; background:rgba( 255, 255, 255, .8 ) ';
			divloading +='url('+ctx+'/res/img/ajax-loader.gif) 50% 50% no-repeat;"></div>';
			$(prefix+id).append(divloading);
		}else{
			$(".screenlocker").remove();
		}
	};
	e.unlock= function(){
		e.lock(false);
	};
	
	// axios  ----------------------------------------------------------
	e.axios =  axios.create({
	  baseURL: '',
	  headers: {'Bearer': $app.getData().token}
	});
	
	// url -------------------------------------
	e.param = function(name) {
	  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
	};
	
	return e;
})({});