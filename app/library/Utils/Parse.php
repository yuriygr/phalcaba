<?php
namespace Phalcon\Utils;

use Phalcon\Mvc\User\Component;

class Parse extends Component {

	var $board_slug;
	var $thread_id;

	/**
	 * Hastag
	 */
	function MakeHashTag($buffer)
	{
		$buffer = preg_replace_callback('/(\#)([0-9a-zA-ZА-Я-а-я\_]+)/', array(&$this, 'MakeHashTagCallback'), $buffer);

		return $buffer;
	}
	function MakeHashTagCallback($matches) {
		global $board_slug;

		$link = \Phalcon\Tag::linkTo([
			$this->url->get([ 'for' => 'chan-search-hashtag', 'board' => $this->board_slug, 'hashtag' => $matches[2] ]),
			'#' . $matches[2]
		]);

		return $link;
	}
	/* Ссылки
	 ================================== */
	function MakeLink($buffer)
	{
		$buffer = preg_replace('#(http://|https://|ftp://)([^(\s<|\[)]*)#', '<a href="\\1\\2">\\1\\2</a>', $buffer);
		
		return $buffer;
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
		// Ссылка на пост в пределе раздела
		$buffer = preg_replace_callback('/&gt;&gt;([r]?[l]?[f]?[q]?[0-9,\-,\,]+)/', array(&$this, 'PostLinkCallback'), $buffer);
		// Ссылка на пост в другом разделе
		$buffer = preg_replace_callback('/&gt;&gt;\/([a-z]+)\/([0-9]+)/', array(&$this, 'InterPostLinkCallback'), $buffer);

		return $buffer;
	}
	// Ссылка на пост в пределе раздела
	function PostLinkCallback($matches) {
		global $thread_id, $board_slug;

		$lastchar = '';
		// If the quote ends with a , or -, cut it off.
		if(substr($matches[0], -1) == "," || substr($matches[0], -1) == "-") {
			$lastchar = substr($matches[0], -1);
			$matches[1] = substr($matches[1], 0, -1);
			$matches[0] = substr($matches[0], 0, -1);
		}
		
		$post = \Post::findFirst(
			[ 'id = :id: and board = :board:',
				'bind' => [ 'id' => $matches[1], 'board' => $this->board_slug]
			]
		);

		if ( $post )
			$link = \Phalcon\Tag::linkTo([
				$this->url->get([ 'for' => 'chan-thread-link', 'board' => $post->board, 'id' => ($post->parent == 0 ? $post->id : $post->parent) ]).'#'.$post->id,
				'&gt;&gt;' . $post->id,
				'class' => ($post->parent == 0 ? 'op_post' : '')
			]);
		else
			$link = '&gt;&gt;' . $matches[1];
			
		return $link.$lastchar;
	}
	// Ссылка на пост в другом разделе
	function InterPostLinkCallback($matches) {
		$lastchar = '';
		// If the quote ends with a , or -, cut it off.
		if(substr($matches[0], -1) == "," || substr($matches[0], -1) == "-") {
			$lastchar = substr($matches[0], -1);
			$matches[1] = substr($matches[1], 0, -1);
			$matches[0] = substr($matches[0], 0, -1);
		}
		
		$post = \Post::findFirst(
			[ 'id = :id: and board = :board:',
				'bind' => [ 'id' => $matches[2], 'board' => $matches[1]]
			]
		);

		if ( $post )
			$link = \Phalcon\Tag::linkTo([
				$this->url->get([ 'for' => 'chan-thread-link', 'board' => $post->board, 'id' => ($post->parent == 0 ? $post->id : $post->parent) ]).'#'.$post->id,
				'&gt;&gt;' . '/' . $post->board . '/' . $post->id,
				'class' => ($post->parent == 0 ? 'op_post' : '')
			]);
		else
			$link = '&gt;&gt;' . '/' . $matches[1] . '/' . $matches[2];
			
		return $link.$lastchar;
	}
	// Ссылка на другой раздел
	function BoardLinkCallback($matches) {
		$lastchar = '';
		// If the quote ends with a , or -, cut it off.
		if(substr($matches[0], -1) == "," || substr($matches[0], -1) == "-") {
			$lastchar = substr($matches[0], -1);
			$matches[1] = substr($matches[1], 0, -1);
			$matches[0] = substr($matches[0], 0, -1);
		}
		
		$board = \Chan::findFirst(
			[ 'slug = :slug:',
				'bind' => [ 'slug' => $matches[1]]
			]
		);

		if ( $board )
			$link = \Phalcon\Tag::linkTo([
				$this->url->get([ 'for' => 'chan-board-link', 'board' => $board->slug]),
				'&gt;&gt;' . '/' . $board->slug . '/'
			]);
		else
			$link = '&gt;&gt;' . '/' . $matches[1] . '/';
			
		return $link.$lastchar;
	}
	
	/* ББ коды
	 ================================== */
	function MakeBBCode($buffer){
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
		$buffer = preg_replace($patterns, $replaces, $buffer);
		$buffer = preg_replace_callback('`\[code\](.+?)\[/code\]`is', array(&$this, 'CodeCallback'), $buffer);
		
		return $buffer;
	}
	function CodeCallback($matches) {
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

	/**
	 * General function
	 */
	function Make($message, $board_slug, $thread_id) {
		// Пока что не знаю зачем
		$this->board_slug = $board_slug;
		$this->thread_id = $thread_id;

		// Чистим вилкой
		$message = trim($message);
		$message = htmlspecialchars($message, ENT_QUOTES);
		// Делаем хештеги
		$message = $this->MakeHashTag($message);
		// Ссылка на пост
		$message = $this->MakePostLink($message);
		// Цитата
		$message = $this->MakeQuote($message);
		// Замена переносов
		$message = str_replace("\n", '<br />', $message);
		// ББ коды
		$message = $this->MakeBBCode($message);
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