/* Расчёт нажатия Ctrl
========================================================= */
var ctrl = false;
$(window).keydown(function(e) {
    if (e.keyCode == 17) ctrl = true;
})
.keyup(function(e) {
    if (e.keyCode == 17) ctrl = false;
})
.blur(function() {
    ctrl = false;
});
/* Определение типа устройства
========================================================= */
var mobile = false;
$(window).resize(function() {
	if ($(window).width() >= (650 - 17)) mobile = false;
	if ($(window).width() <= (650 - 17)) mobile = true;
	console.log(mobile);
});
/* Вспомогательные функции
========================================================= */
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
	$('.form[data-ajax]').submit(function(e) {
		e.preventDefault();

		var action = $(this).attr('action'),
			data   = $(this).serialize();

		$.post(action, data, function (data, status) {
			if (status == "success") {
				if (data.success) {
					$.ambiance({ message: data.success, title: 'Успех', type: 'success' });
				}
				if (data.error) {
					$.ambiance({ message: data.error, title: 'Ошибка', type: 'error' });
				}
				if (data.redirect) {
					// А это, тащемта, только при создании треда
					$(location).attr('href', data.redirect);
				}
			} else {
				$.ambiance({ message: 'Сервер недоступен', title: 'Ошибка', type: 'error' });
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
	/* Reply
	========================================================= */
	$(document).on('click', 'a[data-reply]', function() {
		if (is_thread == false) {
			var thread_id = $(this).attr('data-reply-thread');
			$('#yarn').val(thread_id);
			$('.form-name').html('Ответ в тред #' + thread_id + ' <a href="#" data-reply-remove="true">Отмена</a>');
		}
		insert( '>>' + $(this).attr('data-reply') + '\n' );
		return false;
	});
	$(document).on('click', 'a[data-reply-remove]', function() {
		$('#yarn').val('0');
		$('.form-name').html('Создать тред');
		return false;
	 });
	/* Thread
	========================================================= */
	$(document).on('click', 'a[data-thread-expand]', function() {
		//$.cryApi({ action: 'expandThread', id: '1'  });
		return false;
	});
	$(document).on('click', 'a[data-thread-open]', function() {
		return true;
	});
	$(document).on('click', 'a[data-thread-refresh]', function() {
		return false;
	});
	/* Images
	========================================================= */
	$(document).on('click', 'a[data-image-expand]', function() {
		//$(this).children('img').attr('src', $(this).attr('href'));
		$.ambiance({message: 'Картинка ' + $(this).attr('href') + ' развёрнута', type: "default"});
		return false;
	});
	/* Send form by Ctrl + Enter
	========================================================= */
    $('#shampoo').keydown(function(e) {
        if (e.keyCode == 13 && ctrl)
			$('#submit').click();
    });
});
