<div class="board-add">
	{{ partial('common/chan_form', ['thread_id': thread_id]) }}
</div>

<hr>

<div class="board-content">

	{% for thread in threads.items %}
		<div class="thread-hide" data-thread="{{ thread.board }}-{{ thread.id }}" style="display: none;">
			<span>Thread #{{ thread.id }} hidden.</span>
			<span>{{ thread.open }}</span>
			<span><a href="#" data-thread-unhide="{{ thread.id }}">Unhide</a></span>
		</div>
		<div class="thread-box" data-thread="{{ thread.board }}-{{ thread.id }}">
			{{ partial('common/chan_thread', ['thread': thread, 'open': false]) }}
			<div class="thread-replys">
				{% for post in thread.getReply(config.site.replyLimit) %}
					{{ partial('common/chan_post', ['post': post]) }}
				{% endfor %}
			</div>
		</div>
		<hr>
	{% endfor %}
	
	{% if !threads.items %}
		<h3>В этом разделе нет тредов</h3>
		<p>Удивительно, но такое возможно. Ты можешь исравить ситуацию создав первый тред. Слови GET 1.</p>
		<hr>
	{% endif %}

</div>

<div class="board-nav">
{% if (threads.first != threads.total_pages and threads.total_pages > 0) %}
	<span class="pagination">
	{% if (threads.current != threads.first) %}
		{% if (threads.before != threads.first) %}
			{{ link_to(['for': 'chan.board.page', 'board': board.slug, 'page': threads.before], 'Previous', 'class': 'btn', 'rel': 'prev') }}		
		{% endif %}
		{% if (threads.before == threads.first) %}
			{{ link_to(['for': 'chan.board', 'board': board.slug], 'Previous', 'class': 'btn', 'rel': 'prev') }}		
		{% endif %}
	{% endif %}
	{% if (threads.current != threads.last) %}
		{{ link_to(['for': 'chan.board.page', 'board': board.slug, 'page': threads.next], 'Next', 'class': 'btn', 'rel': 'next') }}
	{% endif %}
	</span>
{% endif %}
	<span>
		{{ link_to('#top', 'Scroll up', 'data-scroll': 'up', 'local': false, 'class': 'btn') }}
	</span>
	<span>
		{{ link_to(['for': 'chan.board.catalog', 'board': board.slug], 'Catalog', 'class': 'btn') }}
	</span>
</div>