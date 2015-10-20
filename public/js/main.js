function scrollToPost(id) {
	return $('html, body').animate({ 'scrollTop': $('.post#' + id).offset().top });
}
function insert(text) {
	var form = $('#shampoo');
	form.val(form.val() + text);
	form.focus();
	return false;
}
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
	$('a[data-num]').click(function() {
		scrollToPost( $(this).attr('data-num') );
		return false;
	});
	/* Нажатия
	========================================================= */	
	$('a[data-reflink]').click(function() {
		insert( '>>' + $(this).attr('data-reflink') + '\n' );
		return false;
	});
	$('a[data-expand]').click(function() {
		//$(this).children('img').attr('src', $(this).attr('href'));
		
		alert( $(this).attr('href') );
		return false;
	});
});
