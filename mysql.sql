SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `chan`;
CREATE TABLE `chan` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `hide` int(1) unsigned NOT NULL DEFAULT '0',
  `isLocked` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `chan` (`id`, `slug`, `name`, `description`, `category`, `hide`, `isLocked`) VALUES
(1,	'b',	'/Б/ред',	'',	'Разное',	0,	0),
(2,	's',	'Софт',	'',	'Технологии',	0,	0),
(3,	'vg',	'Видеоигры',	'',	'Развлечение',	0,	0),
(4,	'tv',	'Фильмы и сериалы',	'',	'Развлечение',	0,	0),
(5,	'c',	'О чане',	'Обсуждение работы чана',	'Разное',	0,	0),
(6,	'x',	'Тесты',	'Раздел для тестирования',	'Разное',	0,	0);

DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `board` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `owner` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `file` (`id`, `slug`, `board`, `type`, `owner`) VALUES
(1,	'D9gfFdg',	'b',	'jpg',	'1');

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `news` (`id`, `text`, `timestamp`) VALUES
(1,	'<p>Привет, Мир!</p>',	1441870014);

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL DEFAULT 'thread',
  `parent` varchar(100) NOT NULL DEFAULT '0',
  `board` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `timestamp` int(100) unsigned NOT NULL,
  `text` text NOT NULL,
  `owner` varchar(100) NOT NULL,
  `bump` int(10) unsigned NOT NULL DEFAULT '0',
  `sage` int(1) unsigned NOT NULL DEFAULT '0',
  `isLocked` int(1) unsigned NOT NULL DEFAULT '0',
  `isSticky` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`board`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
