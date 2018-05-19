<?php
require_once 'core.php';
title('Dashboard');
top();
?>
<style>
#toolbar{
	width: 300px;
}
</style>
<div class="row">
	<div class="col-lg-12">
		
		<div id="toolbar">
			<form id="newdeck">
				<div class="input-group" >
					<input type="text" class="form-control deckname" required placeholder="Deck name...">
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit">Create</button>
					</span>	
				</div>
			</form>
		</div>
		
		<table id="data"></table>
	</div>
</div>
<script>
$app.require_authed();

$('#newdeck').submit((event)=>{
	event.preventDefault();
	var deckname = $('#newdeck .deckname').val();
	$tool.lock();
	$tool.axios.post(ctx + "/deck/api.create.php", {
		name : deckname
	}).then((res)=>{
		$tool.unlock();
		if (res.data.status ==='ok'){
			$('#newdeck .deckname').val('');
			$tool.flash(1,'Success');
			$('#data').bootstrapTable('refresh');
		} else {
			console.log(res.data);
			$tool.flash(0,res.data.data);
		}
	}).catch((err)=>{
		$tool.unlock();
		console.log(err);
		$tool.flash(0,'ERROR');
	});
});



$('#data').bootstrapTable({
	url: ctx + '/deck/api.php',
	cache: false,
	striped: true,
	toolbar: '#toolbar',
	sidePagination: 'server',
	sortName: 'name',
	search: true,
	ajaxOptions: {
		headers: {
			Bearer: $app.getData().token
		}
	},
	pagination: true,
	columns: [
		{
			field: 'name',
			title: 'Name',
			sortable: true,
			formatter: (obj,row)=>{
				return '<a href="'+ctx+'/card/view.php?id='+row.id+'">'+obj+'</a>';
			}
		},
		{
			field: 'quantity',
			title: 'Cards',
			sortable: true,
		},		
		{
			field: 'create_on',
			title: 'Create',
			sortable: true,
		},
		{
			field: 'learn_on',
			title: 'Learn',
			sortable: true,
		}
	]

});
</script>
<?=bottom()?>
