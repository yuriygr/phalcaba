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

/* Ajax forms
========================================================= */
$(document).on('submit', '.form[data-ajax]', function(e) {
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
			if (data.sendPost) {
				// В принципе, делаем это при ответе
				var threadId = data.sendPost.threadId,
					afterId  = $("div[data-type=thread-"+threadId+"] .post:last").attr('id'),
					postId   = data.sendPost.postId;
				// Догружаем посты в тред threadId начиная от afterId и делаем скролл до поста postId
				$.cryApi.sendPost({ threadId: threadId, afterId: afterId , postId: postId  });
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
$(document).on('click', 'a[data-scroll]', function() {
	var to = $(this).data('scroll');
	if ( to == 'up')
		$('html, body').animate({ 'scrollTop': 0 });
	if ( to == 'down')
		$('html, body').animate({ 'scrollTop': $(document).height() });
	return false;
});
$('a[data-num]').click(function() {
	var postId = $(this).data('num');
	// Скроллим до поста
	$.cryApi.scrollToPost( postId );
	return false;
});
/* Reply
========================================================= */
$(document).on('click', 'a[data-reply]', function() {
	var threadId = $(this).data('reply-thread'),
		postId	 = $(this).data('reply');
	if (is_thread == false) {
		$('#yarn').val(threadId);
		$('.form-name').html('Ответ в тред #' + threadId + ' <a href="#" data-reply-remove="true">Отмена</a>');
	}
	// Вставляем в поле ввода текст
	$.cryApi.insertText( '>>' + postId + '\n' );
	return false;
});
$(document).on('click', 'a[data-reply-remove]', function() {
	$('#yarn').val('0');
	$('.form-name').html('Создать тред');
	return false;
 });
/* Thread +
========================================================= */
$(document).on('click', 'a[data-thread-expand]', function() {
	var threadId = $(this).data('thread-expand');
	// Загружаем все посты треда в тред
	$.cryApi.expandThread({ threadId: threadId  });
	return false;
});
$(document).on('click', 'a[data-thread-hide]', function() {
	var threadId = $(this).data('thread-hide');
	// Скрываем тред
	$.cryApi.hideThread({ threadId: threadId  });
	return false;
});
$(document).on('click', 'a[data-thread-refresh]', function() {
	var threadId = $(this).data('thread-refresh'),
		afterId  = $("div[data-type=thread-"+threadId+"] .post:last").attr('id');
	// Догружаем посты в тред threadId начиная от afterId
	$.cryApi.refreshThread({ threadId: threadId, afterId: afterId  });
	return false;
});

$(document).on('click', 'a[data-thread-open]', function() {
	return true;
});
/* Images
========================================================= */
$(document).on('click', 'a[data-file-expand]', function() {
	var fileType = $(this).data('file-expand'),
		fileHref = $(this).attr('href');
	// Разворачиваем файл
	$.cryApi.expandFile({ fileHref: fileHref, fileType: fileType  });
	return false;
});
/* Send form by Ctrl + Enter
========================================================= */
$('#shampoo').keydown(function(e) {
	if (e.keyCode == 13 && ctrl)
		$('#submit').click();
});
/*
	Dropdown
	=========================================================
*/
$(document).on('click', '.dropdown .dropdown_toggler', function(){
	var e = $(this).closest('.dropdown');
	return e.toggleClass('open'),
	$(document).one("click",function(){
		e.removeClass("open")
	}),!1
});
