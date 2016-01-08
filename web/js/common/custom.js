jQuery(document).ready(function(){

    //PLACEHOLDER IE5-IE8.
	jQuery.support.placeholder = false;
	test = document.createElement('input');
	if('placeholder' in test) jQuery.support.placeholder = true;


    //placeholder support IE.
	if(!jQuery.support.placeholder) {
		var active = document.activeElement;
		$(':text').focus(function () {
			if (isset($(this).attr('placeholder')) && $(this).attr('placeholder') == $(this).val()) {
				$(this).val('').removeClass('hasPlaceholder');
			}
		}).blur(function () {
			if (isset($(this).attr('placeholder')) && ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))) {
				$(this).val($(this).attr('placeholder')).addClass('hasPlaceholder');
			}
		});
		$(':text').blur();
		$(active).focus();
		$('form:eq(0)').submit(function () {
			$(':text.hasPlaceholder').val('');
		});
	}

	// Enable fancybox on specific divs
	$(".popbox-trigger").fancybox({
        maxWidth: 420,
        fitToView: true,
        width: '70%'
    });

	// Enable select2
	jQuery(".customselect").select2("destroy");
	jQuery(".customselect").select2({
		minimumResultsForSearch:  "20"
	});

	$('.scrollTop').click(function(){
		$('html').animate({scrollTop:0}, '500');
	});

});

function embedNavbar() {
	var scrollTop = $(window).scrollTop();
	if (scrollTop > 20) {
		$('#menu').addClass('active');
	} else {
		$('#menu').removeClass('active');
	}
}


