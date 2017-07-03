-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июн 09 2016 г., 20:18
-- Версия сервера: 5.6.27-0ubuntu0.14.04.1
-- Версия PHP: 5.6.22-1+donate.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `infex`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '0 - article, 1 - course',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `pub_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `recovers`
--

CREATE TABLE IF NOT EXISTS `recovers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `unique_hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pub_date` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_recovers_user` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `recovers`
--

INSERT INTO `recovers` (`id`, `user_id`, `unique_hash`, `pub_date`) VALUES
(1, 1, '$2y$10$ATpPA2yX9JbZb6gKG328k..zWBQ8jMVSbUh2bmK2vWZNuTxGIhB7a', 1465400518);

-- --------------------------------------------------------

--
-- Структура таблицы `timeblocks`
--

CREATE TABLE IF NOT EXISTS `timeblocks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ancor` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starttime` int(11) unsigned DEFAULT NULL,
  `endtime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `timeblocks`
--

INSERT INTO `timeblocks` (`id`, `ancor`, `type`, `starttime`, `endtime`) VALUES
(1, 'restore', 'block', 1465400516, 1465486916);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_link` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profession` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int(11) unsigned DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `money` double DEFAULT NULL,
  `socials` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_visit` datetime DEFAULT CURRENT_TIMESTAMP,
  `profilesettings` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_activated` int(11) unsigned DEFAULT NULL,
  `is_admin` int(11) unsigned DEFAULT NULL,
  `is_banned` int(11) unsigned DEFAULT NULL,
  `account_type` int(11) unsigned DEFAULT NULL COMMENT '0 - learner, 1 - mentor',
  `likes` int(11) NOT NULL DEFAULT '0',
  `dislikes` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `profile_link`, `name`, `profession`, `avatar`, `gender`, `age`, `city`, `email`, `money`, `socials`, `join_date`, `last_visit`, `profilesettings`, `is_activated`, `is_admin`, `is_banned`, `account_type`, `likes`, `dislikes`, `hash`) VALUES
(1, 'priler96@gmail.com', '$2y$10$cBrB7kgrb2NLZOVctFWEteGi.utVr3YfFgePM/QGvoqLca1vmaq/m', 'priler', 'Абрахам Тугалов', 'Веб Дизайнер', 'c64a3ff41edd493285e898d4c46b7408.png', 'male', 20, 'Ташкент', 'priler96@gmail.com', 0, '[]', '2016-06-22 00:00:00', '2016-06-09 20:16:45', '[]', 1, 1, 0, 1, 0, 0, '9d041da59e851c6e6268e09141daa2718c2d6c45'),
(12, 'admin@infex.ru', '$2y$10$e7jTKA8XKhIO0xhuip6iLOBd4aeADbyOhBOuKq4bQ1HMVSwIlMQ/q', 'admin', 'Павел Свинцов', 'Администратор', '', 'male', 30, 'Новосибирск', 'admin@infex.ru', 0, '[]', '2016-06-09 20:10:24', '2016-06-09 20:16:45', '[]', 1, 1, 0, 1, 0, 0, '15e6a0273043f4ea1a31f93a91829ce0eb419708');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
