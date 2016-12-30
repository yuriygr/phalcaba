<?php

namespace Phalcon\Utils;

class Timeformat
{
	/**
	 * Генерация красивого времени из timestamp
	 * @var $datatime Переменная содержащая Сегодня/Вчера/Полную дату
	 * @var $postMinute Тупо дописывается после всего дерьма
	 *
	 * @param  int $timestamp
	 * @return string
	 */
	public static function generate($timestamp)
	{	
		$postDate = date( "d.m.Y", $timestamp );
		$postMinute = date( "H:i", $timestamp );
		
		if ($postDate == date('d.m.Y')) {
			// Если сегодня
			$datetime = 'Cегодня в ';
		} else if ($postDate == date('d.m.Y', strtotime('-1 day'))) {
			// Если вчера
			$datetime = 'Вчера в ';
		} else {
			// Иначе
			$fulldate = date( "j # Y в ", $timestamp );
			$mon = date("m", $timestamp );
			switch( $mon ) {
				case  1: { $mon='Января'; } break;
				case  2: { $mon='Февраля'; } break;
				case  3: { $mon='Марта'; } break;
				case  4: { $mon='Апреля'; } break;
				case  5: { $mon='Мая'; } break;
				case  6: { $mon='Июня'; } break;
				case  7: { $mon='Июля'; } break;
				case  8: { $mon='Августа'; } break;
				case  9: { $mon='Сентября'; } break;
				case 10: { $mon='Октября'; } break;
				case 11: { $mon='Ноября'; } break;
				case 12: { $mon='Декабря'; } break;
			}
			$datetime = str_replace( '#', $mon, $fulldate );
		}
		return $datetime.$postMinute;
	}

	/**
	 * Создание даты в формате человкочитаемом из timestamp
	 *
	 * @param  int $timestamp
	 * @return string
	 */
	public static function normal($timestamp)
	{
		return date("d.m.Y H:i", $timestamp);
	}
	
	/**
	 * Создание даты в формате ATOM из timestamp
	 *
	 * @param  int $timestamp
	 * @return string
	 */
	public static function atom($timestamp)
	{
		return date(DATE_ATOM, $timestamp);
	}
}