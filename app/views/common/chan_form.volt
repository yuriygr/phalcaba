{{ form( url(['for': 'chan-add-link', 'board': board.slug]), 'class': 'form', 'data-ajax': 'true', 'method': 'post', 'id': 'form') }}

	{{ hidden_field('yarn', 'value': thread_id) }}

	<div class="form-control">
		<input class="input" type="text" id="kasumi" name="kasumi" placeholder="Subject" tabindex="1" />
		{{ submit_button('Send','class': 'btn', 'id': 'submit', 'name': 'submit', 'tabindex': 3) }}
	</div>

	<div class="form-control">
		{{ text_area('shampoo', 'id': 'shampoo', 'rows': 6, 'placeholder': 'Message', 'tabindex': 2) }}
	</div>

	<div class="form-control">
		<span class="checkbox">
			<label><input type="checkbox" name="sage" tabindex="4"> Sage</label>
		</span>
		<span class="form-name right">{{ thread_id ? 'Reply' : 'Create thread' }}</span>
	</div>

{{ end_form() }}
