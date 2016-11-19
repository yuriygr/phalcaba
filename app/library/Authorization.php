<?php

namespace Phalcon;

use Phalcon\Mvc\User\Component;

class Authorization extends Component
{
	/*
	* Log(in|out)
	*/
	public function login($user)
	{	
        $this->session->set('auth-identity', [
            'id' => $user->id,
        ]);
	}
	public function logout()
	{	
		$this->session->remove('auth-identity');
	}


	public function isLogin()
	{	
		return $this->session->get('auth-identity');
	}


	/*
	* Getter's
	*/
	public function getId()
	{	
		$identity = $this->session->get('auth-identity');
		return $identity['id'];
	}
}