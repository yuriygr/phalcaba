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
		<div class="board-add">
			{{ partial('common/chan_form', ['thread_id': thread_id]) }}
		</div>
		<hr>
		{{ content() }}

		<div class="board-panel">
			{{ link_to('#top', 'Scroll up','local': false, 'data-scroll': 'up', 'class': 'btn') }}
			{{ link_to('#top', 'Scroll down','local': false, 'data-scroll': 'down', 'class': 'btn') }}
		</div>
	</div>
</section>