<div class="board-content">

{% for file in files %}
	<a href="{{ file.getLink('origin') }}" target="_blank" data-file-expand="{{ file.type }}">
		<img src="{{ file.getLink('thumb') }}" alt="file-{{ file.type }}-{{ file.id }}">
	</a>
{% endfor %}

</div>
