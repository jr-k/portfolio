
var modalTriggerMagnific = $("#general_modal_magnific_trigger");
var modalGeneralMagnific = $("#general_modal_magnific");
var modalSkeletonMagnific = $("#skeleton_modal_magnific");
var view_default_magnific = {marginTop:0,marginBottom:0,fontSize:16,color:'#434a54',headerClass:'',modalClass:''};
var popupTemplateMagnific = doT.template(modalSkeletonMagnific.html());

modalTriggerMagnific.magnificPopup({
	type: 'inline',
	fixedContentPos: true,
	fixedBgPos: true,
	overflowY: 'hidden',
	closeBtnInside: true,
	closeOnBgClick: true,
	preloader: false,
	midClick: false,
	alignTop:false,
	removalDelay: 300,
	mainClass: 'my-mfp-zoom-in'
});

function modalRenderMagnific(output,opts) {
	if (!isset(opts)) {
		opts = {};
	}

	modalGeneralMagnific.html(output);

	modalTriggerMagnific[0].click();
}

function modalInformationMagnific(baseView,opts) {
	var view = jQuery.extend({}, baseView);
	view.multiple = false;
	$.each(view_default_magnific,function(index,val){
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

	var output = popupTemplateMagnific(view).replace(/__onclick__/g,'onclick');
	console.log(output);
	//var output = Mustache.render(modalSkeletonMagnific.html(), view).replace(/__onclick__/g,'onclick');

	modalRenderMagnific(output,opts);
}

function modalChoicesMagnific(baseView,opts,$this) {
	var view = jQuery.extend({}, baseView);
	view.multiple = true;
	$.each(view_default_magnific,function(index,val){
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

	var output = popupTemplateMagnific(view).replace(/__onclick__/g,'onclick');
	//var output = Mustache.compile(modalSkeletonMagnific.html(), view).replace(/__onclick__/g,'onclick');

	if (isset($this)) {
		var uniqueId = $this.uniqueId();
		output = output.replace(/__this__/g,"$('#"+uniqueId.attr('id')+"')");
	}

	modalRenderMagnific(output,opts);
}

