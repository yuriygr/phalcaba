{% for news in newss %}
	<h4>{{ news.getTitle() }}</h4>
	{{ news.getContent() }}
	{{ news.getDate() }}
	<hr>
{% endfor %}