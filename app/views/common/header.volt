<header>
	<div class="warp">
		<ul class="header__links">
			<li>{{ link_to(['for': 'chan.home'], chanName) }}</li>
			{% for board in boardsList %}
				<li>{{ link_to(['for': 'chan.board', 'board': board.slug], board.slug, 'title': board.name) }}</li>
			{% endfor %}
		</ul>
		<ul class="header__links right">
			<li>{{ link_to(['for': 'chan.settings'], 'Settings', 'title': 'Settings') }}</li>

			{% for page in pagesList %}
				<li>{{ link_to(['for': 'chan.page', 'slug': page.slug], page.name, 'title': page.name) }}</li>
			{% endfor %}

			<li>{{ link_to(['for': 'chan.news'], 'News', 'title': 'News') }}</li>
		</ul>
		<div class="clear"></div>
	</div>
</header>