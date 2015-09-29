<?php

class ModelBase extends \Phalcon\Mvc\Model
{
	public function formatDate($timestamp)
	{
		return date( "H:i d.m.Y", $timestamp );
	}
}