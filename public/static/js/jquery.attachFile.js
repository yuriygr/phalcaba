(function($) {
	$.attachFile = {

		files: [],
		maxFiles: 1,

		attachZone: false,
		attachInput: false,
		attachThumbs: false,

		init: function() {
			// disabled by incompatible browser.
			if (!(window.URL.createObjectURL && window.File))
				return;

			// Nu ti ponyal
			$.attachFile.attachZone = $('#attach-zone');
			$.attachFile.attachInput = $('#attach-input');
			$.attachFile.attachThumbs = $('#attach-thumbs');
			$.attachFile.maxFiles = $.attachFile.attachZone.data('upload-file-max') ?
									$.attachFile.attachZone.data('upload-file-max') :
									1;
		},
		// Add file to array
		addFile: function(file) {
			if ($.attachFile.files.length == $.attachFile.maxFiles) {
				$.ambiance({ message: 'Достигнут лимит аттача файлов', title: 'Ошибка', type: 'error' });
				return;
			}

			// Добавляем файл к массиву файлов
			$.attachFile.files.push(file);
			// Создаём превью
			$.attachFile.addThumb(file);
		},
		// Remove file from array
		removeFile: function(file) {
			$.attachFile.files.splice($.attachFile.files.indexOf(file), 1);
		},
		// Don't know for what this code
		getThumbElement: function(file) {
			return $('.tmb-container').filter(function() {
				return ($(this).data('file-ref') == file);
			});
		},
		// Create thumb. TODO
		addThumb: function(file) {
			var fileName = (file.name.length < 24) ? file.name : file.name.substr(0, 22) + '…';
			var fileType = file.type.split('/')[0];
			var fileExt = file.type.split('/')[1];

			var $container = $('<div>')
				.addClass('tmb-container')
				.data('file-ref', file)
				.append(
					$('<div>').addClass('tmb-file'),
					$('<div>').addClass('tmb-filename').html(fileName),
					$('<div>').addClass('tmb-remove').html('✖')
				)
				.appendTo($.attachFile.attachThumbs);

			var fileThumb = $container.find('.tmb-file');
			if (fileType == 'image') {
				// if image file, generate thumbnail
				var objURL = window.URL.createObjectURL(file);
				fileThumb.css('background-image', 'url('+ objURL +')');
			} else {
				fileThumb.html('<span>' + fileExt.toUpperCase() + '</span>');
			}
		}
	};

	var dragCounter = 0;
	var dropHandlers = {
		dragenter: function (e) {
			e.stopPropagation();
			e.preventDefault();

			if (dragCounter === 0) $.attachFile.attachZone.addClass('dragover');
			dragCounter++;
		},
		dragover: function (e) {
			// needed for webkit to work
			e.stopPropagation();
			e.preventDefault();
		},
		dragleave: function (e) {
			e.stopPropagation();
			e.preventDefault();

			dragCounter--;
			if (dragCounter === 0) $.attachFile.attachZone.removeClass('dragover');
		},
		drop: function (e) {
			e.stopPropagation();
			e.preventDefault();

			$.attachFile.attachZone.removeClass('dragover');
			dragCounter = 0;

			var fileList = e.originalEvent.dataTransfer.files;
			for (var i = 0; i < fileList.length; i++) {
				$.attachFile.addFile(fileList[i]);
			}
		}
	};

	$(document).on('before_post', function (e, formData) {
		for (var i=0; i < $.attachFile.maxFiles; i++) {
			var key = 'file';
			if (i > 0) key += i + 1;
			if (typeof $.attachFile.files[i] === 'undefined') break;
			formData.append(key, $.attachFile.files[i]);
		}
	});

	// clear file queue and UI on success
	$(document).on('after_post', function () {
		$.attachFile.files = [];
		$.attachFile.attachThumbs.empty();
	});

	// attach handlers
	$(document).on(dropHandlers);

	$(document).on('click', '#attach-thumbs .tmb-remove', function (e) {
		e.stopPropagation();

		var file = $(e.target).parent().data('file-ref');

		$.attachFile.getThumbElement(file).remove();
		$.attachFile.removeFile(file);
	});

	$(document).on('keypress click', $.attachFile.attachZone, function (e) {
		e.stopPropagation();

		// accept mouse click or Enter
		if (
			(e.which != 1 || e.target.className != 'attach') &&
			(e.which != 13 || e.target.className != 'attach')
		) return;

		var fileSelector = $.attachFile.attachInput;

		fileSelector.on('change', function (e) {
			if (this.files.length > 0) {
				for (var i = 0; i < this.files.length; i++) {
					$.attachFile.addFile(this.files[i]);
				}
			}
			$(this).val(''); // Clear input
		});

		fileSelector.click();
	});

	$(document).on('paste', function (e) {
		var clipboard = e.originalEvent.clipboardData;
		if (typeof clipboard.items != 'undefined' && clipboard.items.length != 0) {
			
			//Webkit
			for (var i=0; i<clipboard.items.length; i++) {
				if (clipboard.items[i].kind != 'file')
					continue;

				//convert blob to file
				var file = new File([clipboard.items[i].getAsFile()], 'ClipboardImage.png', {type: 'image/png'});
				$.attachFile.addFile(file);
			}
		}
	});

})(jQuery);