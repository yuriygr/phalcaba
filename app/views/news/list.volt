{% for news in news.items %}
	<h3>{{ news.getTitle() }}</h3>
	<span>{{ news.getDate() }}</span>
	{{ news.getContent() }}
	
	<hr>
{% endfor %}