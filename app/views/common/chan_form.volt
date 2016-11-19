{{ form(
	url(['for': 'chan.thread.add', 'board': board.slug]),
	'class': 'form',
	'data-ajax': 'true',
	'method': 'post',
	'id': 'form',
	'enctype': 'multipart/form-data'
) }}

	{{ hidden_field('parent', 'value': thread_id) }}

	<div class="form-group">
		<input class="input" type="text" id="kasumi" name="kasumi" placeholder="Subject" tabindex="1" />
		{{ submit_button('Send','class': 'btn', 'id': 'submit', 'name': 'submit', 'tabindex': 3) }}
	</div>

	<div class="form-group">
		{{ text_area('shampoo', 'id': 'shampoo', 'rows': 6, 'placeholder': 'Message', 'tabindex': 2) }}
	</div>

	<div class="form-group">
		<input id="attach-input" type="file" name="file" multiple hidden accept="{{ allowedFiles|join(',') }}">
		<div id="attach-zone" class="attach" data-upload-file="true" data-upload-file-max="{{ maxFiles }}">
			Attach files
		</div>
		<div id="attach-thumbs"></div>
	</div>

	<div class="form-group">
		<span class="checkbox">
			<label><input type="checkbox" name="sage" id="sage" tabindex="4"> Sage</label>
		</span>
		<span class="form-name right">{{ thread_id ? 'Reply' : 'Create thread' }}</span>
	</div>

{{ end_form() }}
