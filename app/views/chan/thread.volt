<div class="board-content">

	<div class="thread-box" data-thread="{{ thread.board }}-{{ thread.id }}">
		{{ partial('common/chan_thread', ['thread': thread, 'open': true]) }}
		<div class="thread-replys">
			{% for post in thread.getReply() %}
				{{ partial('common/chan_post', ['post': post]) }}
			{% endfor %}
		</div>
	</div>
	<hr>

</div>

<div class="board-add">
	{{ partial('common/chan_form', ['thread_id': thread_id]) }}
</div>

<hr>

<div class="board-nav">
	<span>
		{{ link_to(['for': 'chan.board', 'board': board.slug], 'Return', 'class': 'btn') }}
	</span>
	<span>
		{{ link_to('#top', 'Scroll up', 'data-scroll': 'up', 'local': false, 'class': 'btn') }}
	</span>
	<span>
		{{ link_to('#', 'Refresh thread', 'data-thread-refresh': thread.id, 'local': false, 'class': 'btn') }}
	</span>
</div>