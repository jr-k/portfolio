
var modalTrigger = $("#general_modal_trigger");
var modalGeneral = $("#general_modal");
var modalSkeleton = $("#skeleton_modal");
var view_default = {marginTop:20,marginBottom:20,fontSize:12,color:'#434a54',headerClass:'bg-main',modalClass:''};
var popupTemplate = doT.template(modalSkeleton.html());
modalTrigger.fancybox();

function modalRender(output,opts) {
	if (!isset(opts)) {
		opts = {};
	}
	if (!isset(opts.maxWidth)) {
		opts.maxWidth = 500;
	}

	if (!isset(opts.minWidth)) {
		opts.minWidth = 320;
	}

	opts['content'] = output;
	$.fancybox.open(opts);
}

function modalInformation(baseView,opts) {
	var view = jQuery.extend({}, baseView);
	view.multiple = false;
	$.each(view_default,function(index,val){
		if (!isset(view[index])) {
			view[index] = val;
		}
	});

	if (isset(view.dataMessage)) {
		var messageTemplate = doT.template(view.message);
		view.message = messageTemplate(view.dataMessage);
	}

	if (isset(view.dataTitle)) {
		var messageTemplate = doT.template(view.title);
		view.title = messageTemplate(view.dataTitle);
	}

	var output = popupTemplate(view).replace(/__onclick__/g,'onclick');
	//var output = Mustache.render(modalSkeleton.html(), view).replace(/__onclick__/g,'onclick');

	modalRender(output,opts);
}

function modalChoices(baseView,opts,$this) {
	var view = jQuery.extend({}, baseView);
	view.multiple = true;
	$.each(view_default,function(index,val){
		if (!isset(view[index])) {
			view[index] = val;
		}
	});

	if (isset(view.dataMessage)) {
		var messageTemplate = doT.template(view.message);
		view.message = messageTemplate(view.dataMessage);
	}

	if (isset(view.dataTitle)) {
		var messageTemplate = doT.template(view.title);
		view.title = messageTemplate(view.dataTitle);
	}

	var output = popupTemplate(view).replace(/__onclick__/g,'onclick');
	//var output = Mustache.compile(modalSkeleton.html(), view).replace(/__onclick__/g,'onclick');

	if (isset($this)) {
		var uniqueId = $this.uniqueId();
		output = output.replace(/__this__/g,"$('#"+uniqueId.attr('id')+"')");
	}

	modalRender(output,opts);
}

