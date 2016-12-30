<?php

namespace Phalcon;

/**
 * Class TokenManager
 *
 * @package Phalcon
 */
class TokenManager extends \Phalcon\Mvc\User\Component
{
    /**
     * Generates token per session
     */
    public function generateToken($type)
    {
        $this->session->set('sessionToken' . $type, [
            'tokenKey' => $this->security->getTokenKey(),
            'tokenValue' => $this->security->getToken()
        ]);
    }

    /**
     * Checks token given values against session values
     *
     * @param $tokenKey
     * @param $tokenValue
     * @return bool
     */
    public function checkToken($type, $tokenKey, $tokenValue)
    {
        if ($this->session->has('sessionToken' . $type)) {
            $token = $this->session->get('sessionToken' . $type);
            if ($token['tokenKey'] == $tokenKey && $token['tokenValue'] == $tokenValue) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Checks if user have token or not
     *
     * @return bool
     */
    public function doesUserHaveToken($type)
    {
        if ($this->session->has('sessionToken' . $type)) {
            return true;
        }
        return false;
    }
    /**
     * Gets token values from session
     *
     * @return array|bool
     */
    public function getToken($type)
    {
        if ($this->session->has('sessionToken' . $type)) {
            $token = $this->session->get('sessionToken' . $type);
            return [
                'tokenKey' => $token['tokenKey'],
                'tokenValue' => $token['tokenValue']
            ];
        }
        return false;
    }
}