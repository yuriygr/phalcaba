<section class="board">
	<div class="warp">
		<script type="text/javascript">
			var is_thread = {{ thread_id ? 'true' : 'false' }};
		</script>
		<div class="board-name">
			{{ link_to(['for': 'board-link', 'board': board.slug], board.name) }}
			{% if (board.description) %}
				<span class="board-desc"> - {{ board.description }}</span>
			{% endif %}
		</div>
		<hr>
		<div class="board-add">
			{{ partial('common/postform', ['thread_id': thread_id]) }}
		</div>
		<hr>
		<div class="board-content">
			{{ content() }}
		</div>
	</div>
</section>