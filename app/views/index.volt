{{ tag.getDocType() }}
<html lang="ru">
<head>
	{{ tag.getCharset() }}
	{{ tag.getFavicon() }}
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta name="HandheldFriendly" content="true">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	{{ tag.getGenerator() }}
	
	<!-- Meta Tags -->
	{{ tag.getTitle() }}
	{{ tag.getDescription() }}
	{{ tag.getKeywords() }}
	
	<!-- CSS -->
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