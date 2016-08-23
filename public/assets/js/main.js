/* Init core
========================================================= */
$(function() {
	$.core.init();
});

/* Is Ctrl pressed
========================================================= */
$(window).keydown(function(e) {
	if (e.keyCode == 17) $.core.isCtrl = true;
})
.keyup(function(e) {
	if (e.keyCode == 17) $.core.isCtrl = false;
})
.blur(function() {
	$.core.isCtrl = false;
});

/* Send form by Ctrl + Enter
========================================================= */
$('#shampoo').keydown(function(e) {
	if (e.keyCode == 13 && $.core.isCtrl) $('#submit').click();
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
				var boardSlug = $.core.boardSlug,
					threadId = data.sendPost.threadId,
					afterId  = $("div[data-type=thread-"+threadId+"] .post:last").attr('id'),
					postId   = data.sendPost.postId;
				// Догружаем посты в тред threadId начиная от afterId и делаем скролл до поста postId
				$.core.sendPost({ boardSlug: boardSlug,  threadId: threadId, afterId: afterId , postId: postId  });
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
	// Скроллим до Чего-то
	$.core.scrollTo( this );
	return false;
});
$(document).on('click', 'a[data-num]', function() {
	// Скролл до поста
	$.core.scrollTo( this );
	return false;
});

/* Reply
========================================================= */
$(document).on('click', 'a[data-reply]', function() {
	var threadId = $(this).data('reply-thread'),
		postId	 = $(this).data('reply');

	if ($.core.isThread == false) {
		$('#yarn').val(threadId);
		$('.form-name').html('Ответ в тред #' + threadId + ' <a href="#" data-reply-remove="true">Отмена</a>');
	}

	// Вставляем в поле ввода текст
	$.core.insertText( '>>' + postId + '\r\n' );
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
	var boardSlug = $.core.boardSlug,
		threadId = $(this).data('thread-expand');
	// Загружаем все посты треда в тред
	$.core.expandThread({ boardSlug: boardSlug, threadId: threadId  });
	return false;
});
$(document).on('click', 'a[data-thread-hide]', function() {
	var boardSlug = $.core.boardSlug,
		threadId = $(this).data('thread-hide');
	// Скрываем тред
	$.core.hideThread({ boardSlug: boardSlug, threadId: threadId  });
	return false;
});
$(document).on('click', 'a[data-thread-refresh]', function() {
	var boardSlug = $.core.boardSlug,
		threadId = $(this).data('thread-refresh'),
		afterId  = $("div[data-type=thread-"+threadId+"] .post:last").attr('id');
	// Догружаем посты в тред threadId начиная от afterId
	$.core.refreshThread({ boardSlug: boardSlug, threadId: threadId, afterId: afterId  });
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
	$.core.expandFile({ fileHref: fileHref, fileType: fileType  });
	return false;
});