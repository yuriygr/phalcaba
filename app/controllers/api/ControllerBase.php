<?php

namespace Chan\Controllers\Api;

class ControllerBase extends \Phalcon\Mvc\Controller
{
	public function _returnJson($array)
	{
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setJsonContent($array);
		return false;
	}
}