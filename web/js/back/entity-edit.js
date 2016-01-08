jQuery(function($){

	$('.dnd-sortable').tableDnD();

	$(document).on('click','.dnd-sort',function(){
		var action = $(this).attr('action');
		var entity_target = $(this).data('target');
		var table_target = $('#table-'+entity_target);
		var selected = table_target.find('.entity-checkbox');
		var data = selected.serializeArrayNeutral();
		RELOAD_SLINGSHOT_AJAX_LOADER();
		$.ajax({
			url: action,
			data: data,
			type: 'POST',
			success: function(data) {
				return true;
			},
			error: function(data) {
				return rawError(data);
			}
		});
	});

	$('.secure-form').submit(function(event){
		$(this).find('.secure-input').each(function(){
			var value = $(this).val();
			var newValue = calcHash(value, 'TEXT','SHA-512','HEX',1);
			$(this).val(newValue);
		});
	});

});