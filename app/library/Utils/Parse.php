<?php
namespace Phalcon\Utils;

class Parse {

	/* Ссылки
	 ================================== */
	function MakeLink($txt) {
		/* Make http:// urls in posts clickable */
		$txt = preg_replace('#(http://|https://|ftp://)([^(\s<|\[)]*)#', '<a href="\\1\\2">\\1\\2</a>', $txt);
		
		return $txt;
	} 
	
	/* Цитаты
	 ================================== */
	function MakeQuote($buffer) {
		/* Add a \n to keep regular expressions happy */
		if (substr($buffer, -1, 1)!="\n")
			$buffer .= "\n";

		$buffer = preg_replace('/^(&gt;[^>](.*))\n/m', '<span class="quote">\\1</span>'."\n", $buffer);
		return $buffer;
	}
	
	/* Ссылка на пост
	 ================================== */	
	function MakePostLink($buffer) {
		/* Ссылка в пределе раздела */
		$buffer = preg_replace_callback('/&gt;&gt;([r]?[l]?[f]?[q]?[0-9,\-,\,]+)/', array(&$this, 'InterthreadQuoteCheck'), $buffer);

		return $buffer;
	}
	function InterthreadQuoteCheck($matches) {
		$lastchar = '';
		// If the quote ends with a , or -, cut it off.
		if(substr($matches[0], -1) == "," || substr($matches[0], -1) == "-") {
			$lastchar = substr($matches[0], -1);
			$matches[1] = substr($matches[1], 0, -1);
			$matches[0] = substr($matches[0], 0, -1);
		}
		
		$url =  \Phalcon\DI\FactoryDefault::getDefault()->getShared('url');
		$post = \Post::findFirstById($matches[1]);

		if ( $post )
		    $link = \Phalcon\Tag::linkTo([
		    	$url->get([ 'for' => 'thread-link', 'board' => $post->board, 'id' => ($post->parent == 0 ? $post->id : $post->parent) ]).'#'.$post->id,
		    	'&gt;&gt;' . $post->id,
		    	'class' => ($post->parent == 0 ? 'op_post' : '')
		    ]);
		else
			$link = "&gt;&gt;".$matches[1];
			
		return $link.$lastchar;
	}
	
	/* ББ коды
	 ================================== */
	function BBCode($string){
		$patterns = array(
			'`\*\*(.+?)\*\*`is',
			'`\*(.+?)\*`is',
			'`\_\_(.+?)\_\_`is', 
			'`\%\%(.+?)\%\%`is',
			
			'`\[b\](.+?)\[/b\]`is', 
			'`\[i\](.+?)\[/i\]`is', 
			'`\[u\](.+?)\[/u\]`is', 
			'`\[s\](.+?)\[/s\]`is', 
			'`\[spoiler\](.+?)\[/spoiler\]`is', 
			);
		$replaces =  array(
			'<b>\\1</b>', 
			'<i>\\1</i>',
			'<span class="underline">\\1</span>',
			'<span class="spoiler">\\1</span>', 
			
			'<b>\\1</b>', 
			'<i>\\1</i>', 
			'<span class="underline">\\1</span>', 
			'<strike>\\1</strike>', 
			'<span class="spoiler">\\1</span>', 
			);
		$string = preg_replace($patterns, $replaces, $string);
		$string = preg_replace_callback('`\[code\](.+?)\[/code\]`is', array(&$this, 'code_callback'), $string);
		
		return $string;
	}
	function code_callback($matches) {
		$return = '<pre><code>'	. str_replace('<br />', '', $matches[1]) . '</code></pre>';
		
		return $return;
	}
	
	/* Проверка на наличие
	 ================================== */
	function CheckNotEmpty($buffer) {
		$buffer_temp = str_replace("\n", "", $buffer);
		$buffer_temp = str_replace("<br>", "", $buffer_temp);
		$buffer_temp = str_replace("<br/>", "", $buffer_temp);
		$buffer_temp = str_replace("<br />", "", $buffer_temp);

		$buffer_temp = str_replace(" ", "", $buffer_temp);
		
		if ($buffer_temp == "")
			return "";
		else
			return $buffer;
	}

	function Make($message) {
		// Чистим вилкой
		$message = trim($message);
		$message = htmlspecialchars($message, ENT_QUOTES);
		// Ссылка на пост
		$message = $this->MakePostLink($message);
		// Цитата
		$message = $this->MakeQuote($message);
		// Замена переносов
		$message = str_replace("\n", '<br />', $message);
		// ББ коды
		$message = $this->BBCode($message);
		// Ссылки
		$message = $this->MakeLink($message);
		// Убираем лишние переносы
		$message = preg_replace('#(<br(?: \/)?>\s*){3,}#i', '<br /><br />', $message);
		// Проверка на наличие
		$message = $this->CheckNotEmpty($message);
		
		return $message;
	}
}
?>