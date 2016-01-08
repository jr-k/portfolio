jQuery(function($){

	$('.editable').editable();
	$('.datepicker').datepicker({dateFormat:'dd/mm/yy'});
	$('.editable-select').each(function(){
		var attributeName = $(this).data('name').split(';')[0];
		var attributeWidget =  $('.editable-'+attributeName);
		var source = attributeWidget.attr('data-inplace');
		var jsonSource = jQuery.parseJSON(source);
		var currentValue = $(this).data('current');
		$(this).editable({
			source: jsonSource,
			value: currentValue
		});
	});

	// Check all tr
	$(document).on('click','#global-checkbox',function(){
		$(this).parents('table').eq(0).children('tbody').find('input[type=checkbox]').prop('checked',$(this).is(':checked'));
	});

	// Limit row
	$(document).on('change','.global-limit',function(){
		var action = $(this).attr('action');
		var value = $(this).val();
		document.location.href = action.replace('!l!',value);
	});

	// Bulk delete tr
	$(document).on('click','#bulk-delete',function(){
		var entity_target = $(this).data('target');
		var table_target = $('#table-'+entity_target);
		var selected = table_target.find('.entity-checkbox:checked');

		if (selected.length > 0 && confirm('Etes-vous sûr de vouloir supprimer tous les éléments sélectionnés ?')) {
			var password = isset($(this).attr('password')) ? calcHash(prompt('Mot de passe'),'TEXT','SHA-512','HEX',1) : '-1';
			var action = $(this).attr('action') + '?password=' + password;
			var data = selected.serializeArray();
			RELOAD_SLINGSHOT_AJAX_LOADER();
			$.ajax({
				url: action,
				data: data,
				type: 'POST',
				success: function(data) {
					selected.each(function(){
						var id = $(this).val();
						table_target.find('tr[data-id='+id+']').fadeOut(function(){
							$(this).remove();
						});
					});
				},
				error: function(data) {
					return rawError(data);
				}
			});
		}
	});

	// Single delete tr
	$(document).on('click','.single-delete',function(){
		if (confirm('Etes-vous sûr de vouloir supprimer cet élément ?')) {
			var password = isset($(this).attr('password')) ? calcHash(prompt('Mot de passe'),'TEXT','SHA-512','HEX',1) : '-1';
			var action = $(this).attr('action') + '?password=' + password;
			var tr_target = $(this).parents('tr').eq(0);
			RELOAD_SLINGSHOT_AJAX_LOADER();
			$.ajax({
				url: action,
				type: 'GET',
				success: function(data) {
					tr_target.fadeOut(function(){
						$(this).remove();
					});
				},
				error: function(data) {
					if (data.status == 409) {
						alert('Des enregistrements utilisateurs utilisent ces données.')
					} else {
						return rawError(data);
					}
				}
			});
		}
	});

	$(document).on('click','.sortable-field',function(){
		var field = $(this).data('value');
		var value = $('.sortByWay').val();
		var way = value.indexOf(';DESC') >= 0 ? 'ASC' : 'DESC';
		// If no sort at the beggining, choose ASC
		// If sort field A to field B, reset to ASC
		way = value.length == 0 || value.indexOf(field) < 0 ? 'ASC' : way;
		$('.sortByWay').val(field+';'+way);
		$('form').find('button[type=submit][value=filter]').click();
	});

	$(document).on('click','.boolean-switch',function(){
		var action = $(this).attr('action');
		var $this = $(this);
		RELOAD_SLINGSHOT_AJAX_LOADER();
		$.ajax({
			url: action,
			type: 'GET',
			success: function(data) {
				$this.html(data);
			},
			error: function(data) {
				return rawError(data);
			}
		})
	});

	$(document).on('click','.boolean-switch-unique',function(){
		var action = $(this).attr('action');
		var $this = $(this);
		RELOAD_SLINGSHOT_AJAX_LOADER();
		$.ajax({
			url: action,
			type: 'GET',
			success: function(data) {
				var response = jQuery.parseJSON(data);
				$this.parents('table').eq(0).find('.unique a').html(response.false);
				$this.html(response.true);
			},
			error: function(data) {
				return rawError(data);
			}
		})
	});


	$(document).on('click','.delete-wrapper', function(e) {
		e.preventDefault();
		var wrapper = $(this).data('wrapper');
		$(this).parents('.'+wrapper+'-container-wrap').fadeOut('100', function() {
			$(this).remove();
			$('#type_'+wrapper).hide().removeClass('hidden').fadeIn().find('input').focus();
		});
	});
});




function autoCompleteOn(field,min) {
	var fieldName = '#wrapper_'+field+'_source';
	var $autocompleter = $(fieldName);
	var $configs = [];
	$configs.source = function(request, response) {
		$.ajax({
			url: $('#'+field+'_ajax_route').val(),
			type: 'GET',
			dataType: 'json',
			data: {term:request.term},
			success: function (data) {
				response($.map(data, function (value, key) {
					return {
						value: value.name,
						label: value.fullname,
						picture: value.picture,
						id: key,
						html: value.html
					};
				}));
			}
		});
	};

	$.ui.autocomplete.prototype._renderItem = function( ul, item ) {
		var imgItem = $('<img>').addClass('wrapper-picture').attr('src',item.picture).attr('alt',' ');
		var linkItem = $('<a>').addClass('identity').attr('tabindex','-1').append(imgItem).append(item.label);
		var liItem = $('<li>').addClass('ui-menu-item').attr('role','presentation').append(linkItem);
		return liItem.appendTo( ul );
	}

	$configs.select = function (event, ui) {
		var key = ui.item.id;
		var label = ui.item.label;
		var html = ui.item.html;
		$('#wrapper_'+field+'_source').val(label);
		$('.wrapper_'+field+'_target').val(key);
		$('#'+field+'_placeholder').html(html);
		$('#type_'+field).hide();
	}


	$configs.appendTo = fieldName+'_autocomplete';
	$configs.minLength = min;
	$autocompleter.autocomplete($configs);
}
