<?php

namespace Phalcon\Utils;

class Tripcode
{
	/**
	 * Kusaba X code
	 *
	 * @param  string $post_name Имя с ключём трипкода
	 * @return string            Трипкод
	 */
	public static function generate($post_name)
	{
		if (preg_match("/(#|!)(.*)/", $post_name, $regs)) {
			$cap = $regs[2];
			$cap_full = '#' . $regs[2];

			if (function_exists('mb_convert_encoding')) {
				$recoded_cap = mb_convert_encoding($cap, 'SJIS', 'UTF-8');
				if ($recoded_cap != '') {
					$cap = $recoded_cap;
				}
			}
			
			if (strpos($post_name, '#') === false) {
				$cap_delimiter = '!';
			} elseif (strpos($post_name, '!') === false) {
				$cap_delimiter = '#';
			} else {
				$cap_delimiter = (strpos($post_name, '#') < strpos($post_name, '!')) ? '#' : '!';
			}

			if (preg_match("/(.*)(" . $cap_delimiter . ")(.*)/", $cap, $regs_secure)) {
				$cap = $regs_secure[1];
				$cap_secure = $regs_secure[3];
				$is_secure_trip = true;
			} else {
				$is_secure_trip = false;
			}

			$tripcode = '';
			if ($cap != '') {
				/* From Futabally */
				$cap = strtr($cap, "&amp;", "&");
				$cap = strtr($cap, "&#44;", ", ");
				$salt = substr($cap."H.", 1, 2);
				$salt = preg_replace("/[^\.-z]/", ".", $salt);
				$salt = strtr($salt, ":;<=>?@[\\]^_`", "ABCDEFGabcdef");
				$tripcode = substr(crypt($cap, $salt), -10);
			}
			if ($is_secure_trip) {
				if ($cap != '') {
					$tripcode .= '!';
				}
				$secure_tripcode = md5($cap_secure);
				if (function_exists('base64_encode')) {
					$secure_tripcode = base64_encode($secure_tripcode);
				}
				if (function_exists('str_rot13')) {
					$secure_tripcode = str_rot13($secure_tripcode);
				}
				$secure_tripcode = substr($secure_tripcode, 2, 10);
				$tripcode .= '!' . $secure_tripcode;
			}
			$name = preg_replace("/(" . $cap_delimiter . ")(.*)/", "", $post_name);
			return $tripcode;
		}
	}
}