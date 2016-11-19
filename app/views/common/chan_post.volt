<div class="post {{ post.type }}" id="{{ post.id }}">
	<div class="post-info">
	{% if (post.isSage) %}
		<span class="nyan nyan-sage" title="Sage"></span>
	{% endif %}
	{% if (post.subject) %}
		<span class="subject">{{ post.subject }}</span>
	{% endif %}
		<span class="name">{{ post.getName() }}</span>
		<span class="time">{{ post.getTime() }}</span>
		<span class="link">{{ post.getNuberLink() }}</span>
	</div>

	{% if (post.getFiles()) %}
		<div class="post-file left">
		{% for file in post.getFiles() %}
			<a href="{{ file.getLink('origin') }}" target="_blank" data-file-expand="{{ file.type }}">
				<img src="{{ file.getLink('thumb') }}" alt="file-{{ file.type }}-{{ file.id }}">
				<span class="file-info">({{ file.getResolution() }}) {{ file.type }}</span>
			</a>
		{% endfor %}
		</div>
	{% endif %}

	<div class="post-text">
		{{ post.text }}
	</div>

</div>
<div class="clear"></div>
