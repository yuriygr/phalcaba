{{ tag.getDocType() }}
<html lang="ru">
<head>
	{{ tag.getCharset() }}
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta name="HandheldFriendly" content="true">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	{{ tag.getGenerator() }}

	{{ tag.getFavicon() }}
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/img/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/static/img/apple-touch-icon-152x152.png" />
	<link rel="apple-touch-icon" sizes="180x180" href="static/img/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/static/img/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/static/img/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="/static/img/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/static/img/safari-pinned-tab.svg" color="#e86b09">
	<meta name="theme-color" content="#e86b09">

	<!-- Meta Tags -->
	{{ tag.getTitle() }}
	{{ tag.getDescription() }}
	{{ tag.getKeywords() }}
	
	<!-- CSS -->
	{{ assets.outputCss('app-fonts') }}
	{{ assets.outputCss('app-css') }}

</head>
<body>

{{ partial('common/header') }}

{{ content() }}

{{ partial('common/footer') }}

<!-- JavaScript  -->
{{ assets.outputJs('app-js') }}

</body>
</html>