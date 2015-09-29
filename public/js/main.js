$(document).ready(function() {
	/* Ajax forms
	========================================================= */
	$('.form[data-ajax=true]').submit(function( event ) {
		event.preventDefault();

		var action = $(this).attr('action'),
			data   = $(this).serialize();

		$.post(action, data, function (data, status) {
			if (status == "success") {
				if (data.success) {
					alert('Успех. '+data.success);
				}
				if (data.error) {
					alert('Ошибка. '+data.error);
				}
				if (data.redirect) {
					$(location).attr('href',data.redirect);
				}
			} else {
				alert('Ошибка. Непредвиденная ошабка');
			}
		});
		console.log('Адрес формы: '+action);
		console.log('Дата: '+data);
	});
	/* Scroll To Top/Bottom/Post
	========================================================= */
	$('a[href=#up]').click(function() {
		$('html, body').animate({ 'scrollTop': 0 });
		return false;
	});
	$('a[href=#down]').click(function() {
		$('html, body').animate({ 'scrollTop': $(document).height() });
		return false;
	});
	function scrollToPost(num) {
	    $('html, body').animate({ 'scrollTop': $('.post#-' + num).offset().top });
	}
});
