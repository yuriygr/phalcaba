<header>
	<div class="warp">
		<ul class="header__links">
			<li>{{ link_to(['for': 'home-link'], config.site.title) }}</li>
			{% for board in boards %}
				<li>{{ link_to(['for': 'board-link', 'board': board.slug], board.slug, 'title': board.name) }}</li>
			{% endfor %}
		</ul>
		<ul class="header__links right">
			{% for page in pages %}
				<li>{{ link_to(['for': 'page-link', 'slug': page.slug], page.name, 'title': page.name) }}</li>
			{% endfor %}
			<li>{{ link_to(['for': 'news-link'], 'Новости', 'title': 'Новости') }}</li>
		</ul>
		<div class="clear"></div>
	</div>
</header>