(function($) {
	$.core = {

		api: 'https://crychan.com/api/',

		boardSlug: false,
		threadId: false,

		isThread: false,
		isCtrl: false,
		isMobile: false,

		// Init core
		init: function() {

			// Определяем телефон
			$.core.isMobile  = (/android|webos|i(phone|pod|pad)|blackberry|bb10|rim|rim9|opera m(ob|in)i|symbian|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));

			// Не знаю зачем
			$.core.boardSlug = (typeof window.boardSlug !== 'undefined') ? boardSlug : false;
			$.core.threadId  = (typeof window.threadId !== 'undefined')  ? threadId  : false;
			$.core.isThread  = (typeof window.isThread !== 'undefined')  ? isThread  : false;

			// Ну вообще юмор
			console.info('Что ты тут делаешь?');
		},

		// Work With Thread
		expandThread: function( params ) {
			var link = $.core.api + 'expandThread';
			var params = {
				'boardSlug': params['boardSlug'],
				'threadId': params['threadId']
			};
			$.get(link, params)
			.done(function( data ) {
				if (data.result == 'success') {
					var posts = data.posts;

					var html = '';

					posts.map(function(post) {
						$.core.buildPost(post, function(returns) {
							html += returns;
						});
					});

					console.log(data);
					$('div[data-type="thread-'+params['threadId']+'"] .omitted').remove();
					$('div[data-type="thread-'+params['threadId']+'"] .thread-replys').html(html);

					$.ambiance({ message: 'Thread #' + params['threadId'] + ' expanded'});

				}
				if (data.result == 'error') {
					$.ambiance({ message: 'Some error', type: 'error' });
				}
			})
			.fail(function() {
				$.ambiance({ message: 'Some unknown error', type: 'error' });
			});
		},
		refreshThread: function( params ) {
			var link = $.core.api + 'refreshThread';
			var params = {
				'boardSlug': params['boardSlug'],
				'threadId': params['threadId'],
				'afterId': params['afterId']
			};

			$.get(link, params)
			.done(function( data ) {
				if (data.result == 'success') {
					var posts = data.posts;

					var html = '';
					var loaded_posts = 0;

					posts.map(function(post) {
						$.core.buildPost(post, function(returns) {
							html += returns;
						});
						loaded_posts++;
					});
					
					if (loaded_posts != 0) {
						$('.post#' + params['afterId']).after(html);
						$.ambiance({ message: 'New posts: ' + loaded_posts });
					} else {
						$.ambiance({ message: 'No new posts' });
					}

				}
				if (data.result == 'error') {
					$.ambiance({ message: 'Some error', type: 'error' });
				}
			})
			.fail(function() {
				$.ambiance({ message: 'Some unknown error', type: 'error' });
			});
		},
		
		// Work With Posts
		getPost: function( params ) {
			$.ambiance({ message: 'Пост успешно получен', type: 'success' });
		},

		// Work With File
		expandFile: function( params ) {
			// Если телефон, то открываем в новой вкладке
			// if ( $.core.isMobile )
				return window.open(params['fileHref'],'_blank');

			$.ambiance({ message: 'Файл типа ' +  params['fileType'] + ' и ссылкой ' +  params['fileHref'] + ' не развёрнут, так как я ничего не умею', type: 'error' });
		},


		// Helpers
		scrollTo: function( element ) {
			var to = $(element).data('scroll');
			var postId = $(element).data('num');

			if (to == 'up')
				$('html, body').animate({ 'scrollTop': 0 });

			if (to == 'down')
				$('html, body').animate({ 'scrollTop': $(document).height() });

			if (postId)
				$('html, body').animate({ 'scrollTop': $('.post#' + postId).offset().top });
		},

		insertText: function( text ) {
			var form = $('#shampoo');
			form.val(form.val() + text);
			form.focus();
			return false;
		},

		buildPost: function( post, callback ) {
			var html = '';
			html += '<div class="clear"></div>';
			html += '<div class="post reply" id="' + post.id + '">';
				html += '<div class="post-info">';
				if (post.isSage)
					html += '<span class="nyan nyan-sage" title="Sage"></span>\r';
				if (post.subject)
					html += '<span class="subject">' + post.subject + '</span>\r';
					html += '<span class="name">' + post.name + '</span>\r';
					html += '<span class="time">' + post.time + '</span>\r';
					html += '<span class="link">';
						html += post.link;
					html += '</span>';
				html += '</div>';
				html += '<div class="post-text">' + post.text + '</div>';
			html += '</div>';

			if (typeof callback === "function") {
				callback.call(this, html);
			}
		}
	};

})(jQuery);