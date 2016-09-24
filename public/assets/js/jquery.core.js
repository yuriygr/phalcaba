(function($) {
	$.core = {

		api: '/api/',

		boardSlug: false,
		threadId: false,

		isThread: false,
		isCtrl: false,
		isMobile: false,

		// Init core
		init: function() {

			// Определяем телефон
			$.core.isMobile = (/android|webos|i(phone|pod|pad)|blackberry|bb10|rim|rim9|opera m(ob|in)i|symbian|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));

			// Не знаю зачем
			$.core.boardSlug = boardSlug;
			$.core.threadId = threadId;
			$.core.isThread = isThread;

			// Ну вообще юмор
			console.info('Что ты тут делаешь?');
		},

		// Word With Thread
		expandThread: function( params ) {
			$.ambiance({ message: 'Загружаем в тред №' + params['threadId'] + ' все ответы.'});
		},
		hideThread: function( params ) {
			$('div[data-type=thread-' + params['threadId'] + ']').fadeOut();
			$.ambiance({ message: 'Тред №' + params['threadId'] + ' скрыт' });
		},
		refreshThread: function( params ) {
			var html = '<div class="clear"></div><div class="post reply"><div class="post-text">Хули ты нажал? Зачем? Тебя кто-то просил? Пиздец ты тварь.</div></div>';

			$('.post#' + params['afterId']).after(html);
			$.ambiance({ message: 'Новых постов: 1'});
		},
		
		// Work With Posts
		getPost: function( params ) {
			$.ambiance({ message: 'Пост успешно получен'});
		},
		sendPost: function( params ) {
			$.ambiance({ message: 'Пост успешно отправлен'});
		},

		// Work With File
		expandFile: function( params ) {
			// Если телефон, то нет смысла поп-апа
			//if ( $.core.isMobile )
				return window.open(params['fileHref'],'_blank');

			$.ambiance({ message: 'Файл типа ' +  params['fileType'] + ' и ссылкой ' +  params['fileHref'] + ' не развёрнут, так как я ничего не умею', type: 'error' });
		},


		// Helpers
		scrollTo: function( element ) {
			var to = $(element).data('scroll');
			if ( to == 'up')
				$('html, body').animate({ 'scrollTop': 0 });
			if ( to == 'down')
				$('html, body').animate({ 'scrollTop': $(document).height() });

			var postId = $(element).data('num');
			if (postId)
				$('html, body').animate({ 'scrollTop': $('.post#' + postId).offset().top });
		},

		insertText: function( text ) {
			var form = $('#shampoo');
			form.val(form.val() + text);
			form.focus();
			return false;
		}


	};

})(jQuery);