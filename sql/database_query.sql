--
-- Database: `chatting_room`
--

CREATE DATABASE IF NOT EXISTS `chatting_room` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
use chatting_room;


CREATE TABLE IF NOT EXISTS `admin` (
	`idadmin` int(2) NOT NULL auto_increment,
	`user_id` int(8) NOT NULL default 0,
	`nickname` varchar(80) NOT NULL default '',
	`password` varchar(80) NOT NULL default '',	
	PRIMARY KEY (`idadmin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `users` (
	`id` int(8) NOT NULL auto_increment,
	`nickname` varchar(80) NOT NULL default '',
	`email` varchar(180) NOT NULL default '',
	`password` varchar(80) NOT NULL default '',
	`sex` tinyint(1) NOT NULL default '0',
	`age` tinyint(100) NOT NULL default '0',
	`position` varchar(180) NOT NULL default '',
	`active` tinyint(1) NOT NULL default '0',
	`create_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`last_signin` datetime NOT NULL default '0000-00-00 00:00:00',
	`last_activation_request` datetime NOT NULL default '0000-00-00 00:00:00',
	`lost_password_request` tinyint(1) NOT NULL DEFAULT '0',
	`room_id` int(8) NOT NULL default 0,
	`chat_permission` tinyint(1) NOT NULL default '0',
	`ban_last_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`ban_period` int(2) NOT NULL default -1,
	`is_online` tinyint(1) NOT NULL default '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `friends` (
	`id` int(8) NOT NULL auto_increment,
	`user_id` int(8) NOT NULL default '0',
	`user_id2` int(8) NOT NULL default '0',
	`approved` tinyint(1) NOT NULL default '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `chatrooms` (
	`id` int(8) NOT NULL auto_increment,
	`room_id` int(8) NOT NULL default 0,
	`name` varchar(80) NOT NULL default '',
	`online_users` int(8) NOT NULL default '0',
	`registered_users` int(8) NOT NULL default '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `chatrooms` VALUES (1, 1000, 'The Lobby', 0, 0);
INSERT INTO `chatrooms` VALUES (2, 2000, 'Love Shack', 0, 0);
INSERT INTO `chatrooms` VALUES (3, 3000, 'Bored Room', 0, 0);
INSERT INTO `chatrooms` VALUES (4, 4000, 'Game Room', 0, 0);
INSERT INTO `chatrooms` VALUES (5, 5000, 'Music Lovers', 0, 0);

CREATE TABLE IF NOT EXISTS `messages` (
	`id` int(8) NOT NULL auto_increment,
	`sender_id` int(8) NOT NULL default 0,
	`receiver_id` int(8) NOT NULL default 0,
	`sender_name` varchar(80) NOT NULL default '', 
	`receiver_name` varchar(80) NOT NULL default '',
	`send_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`msg` varchar(255) NOT NULL default '',
	`read_status` tinyint(1) NOT NULL default '0', 
	`room_id` int(8) NOT NULL default 0,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;