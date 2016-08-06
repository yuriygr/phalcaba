(function($) {
	$.cryApi = {

		/* THREAD*/
		expandThread: function( params ) {
			$.ambiance({ message: 'Загружаем в тред №' + params['threadId'] + ' все ответы.'});
		},
		hideThread: function( params ) {
			$.ambiance({ message: 'Тред №' + params['threadId'] + ' скрыт' });
			$('div[data-type=thread-' + params['threadId'] + ']').fadeOut();
		},
		refreshThread: function( params ) {
			var html = '<div class="clear"></div><div class="post reply"><div class="post-text">Хули ты нажал?</div></div>';

			$('.post#' + params['afterId']).after(html);
			$.ambiance({ message: 'Нет новых постов'});
		},
		
		/* POST */
		getPost: function( params ) {},
		sendPost: function( params ) {
			$.ambiance({ message: 'Пост №' +  params['postId']  + ' отправлен в тред №' +  params['threadId'] + '. Последний пост в этом треде №'+ params['afterId']});
		},
		scrollToPost: function( postId ) {
			$('html, body').animate({ 'scrollTop': $('.post#' + id).offset().top });
		},


		expandFile: function( params ) {
			$.ambiance({ message: 'Файл типа ' +  params['fileType'] + ' и ссылкой ' +  params['fileHref'] + ' не развёрнут, так как я ничего не умею', type: 'error' });
		},


		insertText: function( text ) {
			var form = $('#shampoo');
			form.val(form.val() + text);
			form.focus();
			return false;
		},



	};

})(jQuery);