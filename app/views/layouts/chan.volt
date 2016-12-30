<section class="board">
	<div class="warp">
		<script type="text/javascript">
			var boardSlug = '{{ board.slug }}';
			var	threadId = '{{ thread_id }}';
			var	isThread = {{ thread_id ? 'true' : 'false' }};
		</script>
		<div class="board-name">
			{{ link_to(['for': 'chan.board', 'board': board.slug], board.name) }}
			{% if (board.description) %}
				<span class="board-desc"> — {{ board.description }}</span>
			{% endif %}
		</div>
		<hr>

		{{ content() }}
	
	</div>
</section>