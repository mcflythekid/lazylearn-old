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
	
	$stmt = mysqli_prepare($con, "SELECT id, name, public, cards, category, username, UNIX_TIMESTAMP(created) AS created, url FROM sets WHERE id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($result) > 0) {
		$set = mysqli_fetch_assoc($result); 
	}else{
		mysqli_close($con);
		header("Location: /");
		die();
	}
	mysqli_stmt_close($stmt);
	
	if ($set["public"] == 0 && (!isset($_SESSION["username"]) || $_SESSION["username"] !== $set["username"])){
		mysqli_close($con);
		header("Location: /");
		die();  
	}
	
	$stmt = mysqli_prepare($con, "SELECT id, front, back FROM cards WHERE set_id = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($result) > 0) {
		$cards = array();
		while($row = mysqli_fetch_assoc($result)) {
			$cards[] = $row;
		}
	}
	mysqli_stmt_close($stmt);
	
	mysqli_close($con);
	
	$is_authenticated = isset($_SESSION["username"]);
	if ($is_authenticated){
		$is_owner = $_SESSION["username"] === $set["username"];
	}else{
		$is_owner = false;
	}
	
	if (!$is_authenticated || !$is_owner){
		error("fuck you, it's not yours");
	}
	
	$title = $set["name"];
	
?>

<?php require_once("../private/navbar2.php"); ?>
<?php require_once("../private/graph.php"); ?>



<style>
.slideContainer {
    overflow-x: auto;
    white-space: nowrap;
	width: 100%;
}
.slide {
    display: inline-block;
    width: 100%;
    white-space: normal;
}
</style>




<h1><?=$set["name"]?></h1>	



<div class="slideContainer">
	<div class="slide">
		<?php graph($set["username"], $id); ?>
	</div>
</div>


<!-- Delete form -->
<div id="dialog-confirm" title="Delete this card set?"></div>
<form name="delete" action="/flashcard/delete.php" method="post" onSubmdit="return confirm('Are you sure?');" style="display: none;">
	<input type="hidden" name="id" value="<?php echo $set["id"]; ?>">
</form><!-- End delete form -->

		
<?php if ($is_owner){?>
	<div class="row">
		<div class="col-lg-12">
			<div class="btn-group btn-group-justified" role="group" aria-label="Justified button group with nested dropdown"> 
				<a href="/flashcard/study.php?id=<?php echo $set["id"]; ?>&study_fresh" class="btn btn-default" role="button">
					<img alt="Study Fresh Card" src="<?php echo $ASSET; ?>/img/study_old.png" class="study_opt hidden-xs">
					<?=$lang["set"]["study_fresh"]?>
				</a>
				<a href="/flashcard/study.php?id=<?php echo $set["id"]; ?>&study_old" class="btn btn-default" role="button">
					<img alt="Study Old Card" src="<?php echo $ASSET; ?>/img/study_old.png" class="study_opt hidden-xs">
					<?=$lang["set"]["study_old"]?>
				</a>
				<a href="/flashcard/study.php?id=<?php echo $set["id"]; ?>" class="btn btn-default" role="button">
					<img alt="Spaced Repetition" src="<?php echo $ASSET; ?>/img/leitner_system_icon.png" class="study_opt hidden-xs">
					<?=$lang["set"]["study"]?>
				</a>
				<a href="/flashcard/study.php?id=<?php echo $set["id"]; ?>&review_all" class="btn btn-default" role="button">
					<img alt="All Flashcards" src="<?php echo $ASSET; ?>/img/world.png" class="study_opt hidden-xs">
					<?=$lang["set"]["review"]?>
				</a>	
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="btn-group btn-group-justified" role="group" aria-label="Justified button group with nested dropdown">
				<a href="/flashcard/export.php?id=<?php echo $set["id"]; ?>"  class="btn btn-default" role="button"><?=$lang["set"]["export"]?></a>
				<a href="/flashcard/import.php?id=<?php echo $set["id"]; ?>"  class="btn btn-default" role="button"><?=$lang["set"]["import"]?></a>
				<a href="/flashcard/setting.php?id=<?php echo $set["id"]; ?>" class="btn btn-default" role="button"><?=$lang["set"]["setting"]?></a>
				<a href="javascript:delete_confirm()"                         class="btn btn-default" role="button"><?=$lang["set"]["delete"]?></a>
			</div>
		</div>
	</div>
	
	<br>
	

	<div class="row" id="lzcard_newform">
		<div class="col-lg-11">
			<div class="row">
				<div class="col-xs-6">
					<div id="new_front"></div>
				</div>
				<div class="col-xs-6">
					<div id="new_back"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-1">
			<button type="submit" id="new_submit" class="btn btn-success pull-right">
				<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
			</button>
		</div>
	</div>
	

	
<?php } else { /** not is owner **/?>
	<div class="row">
		<div class="col-lg-6">
			<a href="/flashcard/clone.php?id=<?php echo $id;?>" class="btn btn-default" role="button">Learn</a>
			<?php if (isset($_SESSION["username"]) && $_SESSION["username"] == $ROOT) { ?>
				<a href="javascript:delete_confirm()" class="btn btn-default" role="button"><?=$lang["set"]["delete"]?></a>
			<?php } ?>
		</div>
	</div>
	<br>
<?php }?>

<style>
.lzcard_side {
	padding-left: 10px;
	padding-right: 10px;
    background-color: #fff;
    border-bottom: 1px solid #cecece;
    border-left: 1px solid #d8d8d8;
    border-right: 1px solid #cecece;
    border-top: 1px solid #d8d8d8;
    min-height: 6em;
}
.lzcard_side img{
	max-width:100%;
	max-height:100%;
}
.lzcard{
	margin-bottom:10px;
}
#lzcard_newform{
	margin-bottom:30px;
}
#lzcard_sample{
	display: none;
}
</style>



<div class="row lzcard" id="lzcard_sample">
	<div class="col-lg-11">
		<div class="row">
			<div class="col-lg-6">
				<div class="front lzcard_side"></div>
			</div>
			<div class="col-lg-6">
				<div class="back lzcard_side"></div>
			</div>
		</div>
	</div>
	
	<?php if ($is_owner){?>
	<div class="col-lg-1">
	
		<div class="btn-group-vertical lzcard_control pull-right" role="group" aria-label="Basic example">
			<button type="submit" class="btn btn-default edit">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
			</button>
			<button type="submit" class="btn btn-default delete">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
			</button>
		</div>
		
		<div class="btn-group-vertical lzcard_editor pull-right nonedisplay" role="group" aria-label="Basic example">
			<button type="submit" class="btn btn-success edit">
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			</button>
			<button type="submit" class="btn btn-danger delete">
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</button>
		</div>

	</div>
	<?php } ?>
</div>
<div id="lzcard_all"></div>

<!-- Initialize Quill editor -->

<script>
var $setview = (function(e){
	e.quill_modules = {
		  modules: {
			toolbar: [
			  ['bold', 'italic', 'underline'],
			  ['image', 'code-block']
			]
		  },
		  placeholder: '',
		  theme: 'snow'  // or 'bubble'
	};
	e.init = ()=>{
		$(document).ready(()=>{
			e.new_front = new Quill('#new_front', {
			  modules: {
				toolbar: [
				  ['bold', 'italic', 'underline'],
				  ['image', 'code-block']
				]
			  },
			  placeholder: 'Front side...',
			  theme: 'snow'  // or 'bubble'
			});
			e.new_back = new Quill('#new_back', {
			  modules: {
				toolbar: [
				  ['bold', 'italic', 'underline'],
				  ['image', 'code-block']
				]
			  },
			  placeholder: 'Back side...',
			  theme: 'snow'  // or 'bubble'
			});
			download(set_id);
		});
		$(document).on('click', '.lzcard_control .delete', function(){
			//if (confirm("Are you sure you want to delete")) {
				var card_id = $(this).attr('data-lzcard_id');
				$('.lzcard[data-lzcard_id="'+card_id+'"').remove();
				$.post("/jax/delete_card.php", {id : card_id}, function(){
					
				});
			//}

		});		
		$(document).on('click', '.lzcard_control .edit', function(){
			var card_id = $(this).attr('data-lzcard_id');
			var lzcard_node = $('.lzcard[data-lzcard_id="'+card_id+'"]');
			lzcard_node.find('.lzcard_control').hide();
			lzcard_node.find('.lzcard_editor').show();
			lzcard_node.data('quill.front', new Quill(lzcard_node.find('.front').get(0), e.quill_modules));
			lzcard_node.data('quill.back', new Quill(lzcard_node.find('.back').get(0), e.quill_modules));
		});
	};
	var download = (set_id) =>{
		axios.get('/jax/view_set.php?id=' + set_id)
			.then(function (response) {
				if (response.data.status !== 'error'){
					for(var i = 0; i < response.data.data.length; i++) {
						var obj = response.data.data[i];
						renderAppend(obj);
					}
				}else{
					flash(0, 'Error');
					console.log(response.data);
				}
			}).catch(function (error) {
				flash(0, 'Error');
				console.log(error);
			});
	};
	var clone = (card)=>{
		var node = $('#lzcard_sample').clone().removeAttr('id');
		node.find('.front').html(card.front);
		node.find('.back').html(card.back);
		node.attr('data-lzcard_id', card.id);
		node.find('.edit').attr('data-lzcard_id', card.id);
		node.find('.delete').attr('data-lzcard_id', card.id);
		return node;
	};
	var renderAppend = (card)=>{
		$('#lzcard_all').append(clone(card));
	};
	var renderPrepend = (card)=>{
		$('#lzcard_all').prepend(clone(card));
	};
	e.create = (set_id, front, back)=>{
		if (!front || !back){
			flash(0, 'Invalid data');
			return;
		}
		axios.post("/jax/new_card.php", {
			set_id : set_id,
			front : front, 
			back : back
		}).then(function (response) {
			if (response.data.status !== 'error'){
				renderPrepend({
					id: response.data,
					front: front,
					back: back
				});
			}else{
				flash(0, 'Error');
				console.log(response.data);
			}
		}).catch(function (error) {
			flash(0, 'Error');
			console.log(error);
		});
	};
	return e;
})({});
$setview.init();



var set_id = <?php echo $id; ?> ;




function delete_confirm(){
	$( "#dialog-confirm" ).dialog({
		resizable: false,
		height:140,
		modal: true,
		buttons: {
		"Delete": function() {
			document.delete.submit();
			$(this).dialog("close");
		},
		Cancel: function() {
			$(this).dialog("close");
		}
		}
	});
}

$('body').delegate('button[id^="edit_submit-"]', "click", function( event ) {
	$("#edit_error-" + id).html("");
	var id = this.id.substring(12);
	var front = $("textarea#edit_front-" + id).val();
	var back = $("textarea#edit_back-" + id).val();
	front = $.trim(front);
	back = $.trim(back);
	if (!front || !back || !/^(.{1,1024})$/.test(front) || !/^(.{1,1024})$/.test(back)){
		$("#edit_error-" + id).html("Card size can't be blank or greater than 1024.");
		return;
	}
	$("p#card-front-" + id).text(front);
	$("p#card-back-" + id).text(back);
	$("table#card_display-" + id).css("display", "table");
	$("table#card_edit-" + id).css("display", "none");
	$.post("/jax/update_card.php", {id : id, front : front, back : back},  function(result){
	});
}); 

$('body').delegate('button#new_submit', "click", function( event ) {
	$setview.create(set_id, $("#new_front .ql-editor").html().trim(), $("#new_back .ql-editor").html().trim());
	$("#new_front .ql-editor").html('');
	$("#new_back .ql-editor").html('');
}); 


$('body').delegate('a[id^="edit_cancel-"]', "click", function( event ) {
	var id = this.id.substring(12);
	$("table#card_display-" + id).css("display", "table");
	$("table#card_edit-" + id).css("display", "none");
}); 
$('body').delegate('img[id^="cardlist_card_data_edit-"]', "click", function( event ) {
	var id = this.id.substring(24);
	$("table#card_display-" + id).css("display", "none");
	$("textarea#edit_front-" + id).val($("p#card-front-" + id).text());
	$("textarea#edit_back-" + id).val($("p#card-back-" + id).text());
	$("table#card_edit-" + id).css("display", "table");
}); 









</script>



<?php require_once("../private/footer2.php"); ?>