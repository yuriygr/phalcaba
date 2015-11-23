<?php

class ModelBase extends \Phalcon\Mvc\Model
{
	public function formatDate($timestamp)
	{
		return date( "d.m.Y H:i", $timestamp );
	}
}