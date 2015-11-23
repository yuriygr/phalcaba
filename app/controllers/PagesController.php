<?php
use \Phalcon\Mvc\View as View;

class PagesController extends ControllerBase
{
	public function indexAction()
	{
		$this->tag->prependTitle("home");
		$this->view->disableLevel(View::LEVEL_LAYOUT);
	}
	public function faqAction()
	{
		$this->tag->prependTitle("faq");
	}
	public function rulesAction()
	{
		$this->tag->prependTitle("rules");
	}
	public function show404Action()
	{
		$this->response->setStatusCode(404, "Not Found");
		$this->tag->prependTitle("Ошибка 404");
	}
}