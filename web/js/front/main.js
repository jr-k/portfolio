jQuery(function(){

	$('.popin-middle').magnificPopup({
		type: 'inline',
		fixedContentPos: false,
		fixedBgPos: true,
		overflowY: 'hidden',
		closeBtnInside: true,
		closeOnBgClick: false,
		preloader: false,
		midClick: true,
		alignTop:true,
		removalDelay: 300,
		mainClass: 'my-mfp-zoom-in',
		callbacks: {
			open: function() {

			}
		}
	});

});