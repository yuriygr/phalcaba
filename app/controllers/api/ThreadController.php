<?php

namespace Chan\Controllers\Api;

use \Chan\Models\Board;
use \Chan\Models\Post;

class ThreadController extends ControllerBase
{
	public function expandAction()
	{	
		$json = [];
		$boardSlug = $this->request->get('boardSlug', 'string');
		$threadId = $this->request->get('threadId', 'int');

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
		$replys = Post::find(
			[ 'parent = :id: and type = "reply" and board = :board:', 'order' => 'timestamp', 'bind' => [
				'id' => $threadId,
				'board' => $boardSlug
			]]
		);

		$posts = [];

		foreach ($replys as $reply) {
			$posts[] = [
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
		}


		$json['result'] = 'success';
		$json['posts'] = $posts;

		return $this->_returnJson($json);
	}

	public function refreshAction()
	{	
		$json = [];
		$boardSlug = $this->request->get('boardSlug', 'string');
		$threadId = $this->request->get('threadId', 'int');
		$afterId = $this->request->get('afterId', 'int');

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
		$replys = Post::find(
			[ 'parent = :id: and type = "reply" and board = :board: and id > :after:', 'order' => 'timestamp', 'bind' => [
				'id' => $threadId,
				'board' => $boardSlug,
				'after' => $afterId
			]]
		);

		$posts = [];
		$files[] = [];

		foreach ($replys as $reply) {

			/*foreach ($reply->getFiles() as $file) {
				$files[] = [
					'id' => $reply->id,
					'parent' => $reply->parent,
					'board' => $reply->board,
					'subject' => $reply->subject,
					'time' => $reply->getTime()
				];
			}*/

			$posts[] = [
				'id' => $reply->id,
				'parent' => $reply->parent,
				'board' => $reply->board,
				'subject' => $reply->subject,
				'time' => $reply->getTime(),
				'name' => $reply->getName(),
				'text' => $reply->text,
				'isSage' => $reply->isSage ? true : false,
				'link' => $reply->getNuberLink(),
				'files' => $files
			];
		}

		$json['result'] = 'success';
		$json['posts'] = $posts;

		return $this->_returnJson($json);
	}
}