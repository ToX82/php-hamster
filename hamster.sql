-- Adminer 4.7.8 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `autologins`;
CREATE TABLE `autologins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `expires` datetime NOT NULL,
  `browser` varchar(255) NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 99,
  `hidden` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`, `description`, `position`, `hidden`) VALUES
(1,	'admin',	'Amministratore',	1,	0),
(2,	'user',	'Utente',	2,	0);

DROP TABLE IF EXISTS `roles_users`;
CREATE TABLE `roles_users` (
  `role_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  KEY `index_roles_users_on_role_id` (`role_id`),
  KEY `index_roles_users_on_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(50) DEFAULT NULL,
  `login_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2020-12-25 21:38:06
