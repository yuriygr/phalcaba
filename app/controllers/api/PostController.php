<?php

namespace Chan\Controllers\Api;

use \Chan\Models\Board;
use \Chan\Models\Post;

class PostController extends ControllerBase
{
	public function getAction()
	{	
		$json = [];
		$boardSlug = $this->request->get('boardSlug', 'string');
		$threadId = $this->request->get('threadId', 'int');
		$postId = $this->request->get('postId', 'int');

		// Поиск раздела
		$chan = Board::findFirst(
			[ 'slug = :slug:', 'bind' => [
				'slug' => $boardSlug
			]]
		);
		if (!$chan) {
			$json['result'] = 'error';
			return $this->_returnJson($json);
		}

		// Поиск треда
		$reply = Post::findFirst(
			[ 'id = :id: and board = :board:', 'bind' => [
				'id' => $postId,
				'board' => $boardSlug
			]]
		);

		$post = [
			'id' => $reply->id,
			'parent' => $reply->parent,
			'board' => $reply->board,
			'subject' => $reply->subject,
			'time' => $reply->getTime(),
			'name' => $reply->getName(),
			'text' => $reply->text,
			'isSage' => $reply->isSage ? true : false,
			'link' => $reply->getNuberLink()
		];



		$json['result'] = 'success';
		$json['post'] = $post;

		return $this->_returnJson($json);
	}
}