<div class="post {{ thread.type}}" id="{{ thread.id}}">
	<div class="post-info">
	{% if (thread.isLocked or thread.isSticky or thread.isHide) %}
		{% if (thread.isLocked) %}
			<span class="nyan nyan-locked" title="Locked"></span>
		{% endif %}
		{% if (thread.isSticky) %}
			<span class="nyan nyan-sticky" title="Sticky"></span>
		{% endif %}
		{% if (thread.isHide) %}
			<span class="nyan nyan-hide" title="Hide"></span>
		{% endif %}
	{% endif %}
	{% if (thread.subject) %}
		<span class="subject">{{ thread.subject }}</span>
	{% endif %}
		<span class="name">{{ thread.name }}</span>
		<span class="time">{{ thread.time }}</span>
		<span class="link">{{ thread.link }}</span>
	{% if (open == false) %}
		<span class="open">{{ thread.open }}</span>
	{% endif %}
	</div>
	
	{% if (thread.getFiles()) %}
		<div class="post-file left">
		{% for file in thread.getFiles() %}
			<a href="{{ file.getLink('origin') }}" target="_blank" data-file-expand="{{ file.type }}">
				<img src="{{ file.getLink('thumb') }}" alt="file-{{ file.type }}-{{ file.id }}">
				<span class="file-info">({{ file.getResolution() }}) {{ file.type }}</span>
			</a>
		{% endfor %}
		</div>
	{% endif %}
	
	<div class="post-text">
		{{ thread.text }}
	</div>
</div>
{% if (thread.countReply() > config.site.replyLimit and open == false) %}
	<div class="omitted">{{ tag.getOmitted(thread.countReply() - config.site.replyLimit) }}</div>
{% endif %}
<div class="clear"></div>