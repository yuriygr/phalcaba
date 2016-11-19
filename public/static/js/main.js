/* Init core
========================================================= */
$(function() {
	$.core.init();
	$.attachFile.init();
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
	if (e.keyCode == 13 && $.core.isCtrl) $('#submit').trigger('click');
});

/* Ajax forms
========================================================= */
$(document).on('submit', '.form[data-ajax]', function(e) {
	e.preventDefault();
	
	var formAction = $(this).attr('action'),
		formMethod = $(this).attr('method'),
		formData   = new FormData(),
		formSerialize = $(this).serializeArray();
		
	var updateProgress = function(e) {
		var percentage;
		if(e.position===undefined){
			percentage=Math.round(e.loaded*100/e.total);
		} else{
			percentage=Math.round(e.position*100/e.total);
		}
		$('input#submit').val('Posting... (#%)'.replace('#', percentage));
	};

	// Make form data great again
	$.each( formSerialize, function( i, field ) {
		formData.append(field.name, field.value);
	});

	// Attach file's
	$(document).trigger('before_post', formData);

	$.ajax({
		type: formMethod,
		url: formAction,
		data: formData,
		cache: false,
		contentType: false,
		processData: false,

		beforeSend: function () {
			// Make button deactive
			$('input#submit').attr('disabled', true);
		},

		xhr: function() {
			var xhr = $.ajaxSettings.xhr();
			if (xhr.upload) {
				xhr.upload.addEventListener('progress', updateProgress, false);
			}
			return xhr;
		},

		success: function(data, status, xhr) {
			// Если все хорошо
			if (status == "success") {
				if (data.success) {
					$('.form[data-ajax]').trigger('reset');
					$(document).trigger('after_post');
					$.ambiance({ message: data.success, type: 'success' });
				}
				if (data.error) {
					$.ambiance({ message: data.error, type: 'error' });
				}
				if (data.refreshThread) {
					var boardSlug = $.core.boardSlug,
						threadId = $('input#parent').val(),
						afterId  = $("div[data-type=thread-"+threadId+"] .post:last").attr('id');
					$.core.refreshThread({ boardSlug: boardSlug, threadId: threadId, afterId: afterId  });
				}
				if (data.redirect) {
					$(location).attr('href', data.redirect);
				}
			} else {
				$.ambiance({ message: 'Some unknown error', type: 'error' });
			}
			// Make button active
			$('input#submit').attr('disabled', false);
			// Make default text in button
			$('input#submit').val('Send');
		},

		error: function(xhr, status, error) {
			$.ambiance({ message: 'Some unknown error', type: 'error' });
		}
	},'json');
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
		$('#parent').val(threadId);
		$('.form-name').html('Reply to #' + threadId + ' <a href="#" data-reply-remove="true">Cancel</a>');
	}

	// Вставляем в поле ввода текст
	$.core.insertText( '>>' + postId + "\r\n" );
	return false;
});
$(document).on('click', 'a[data-reply-remove]', function() {
	$('#parent').val('0');
	$('.form-name').html('Create thread');
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
$(document).on('click', 'a[data-thread-refresh]', function() {
	var boardSlug = $.core.boardSlug,
		threadId = $(this).data('thread-refresh'),
		afterId  = $("div[data-type=thread-"+threadId+"] .post:last").attr('id');
	// Догружаем посты в тред threadId начиная от afterId
	$.core.refreshThread({ boardSlug: boardSlug, threadId: threadId, afterId: afterId  });
	return false;
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