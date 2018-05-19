/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : lazylearn_com

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-11-25 23:52:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cards
-- ----------------------------
DROP TABLE IF EXISTS `cards`;
CREATE TABLE `cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) CHARACTER SET ascii DEFAULT NULL,
  `set_id` int(10) unsigned DEFAULT NULL,
  `front` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `back` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `step` tinyint(3) unsigned DEFAULT '0',
  `weakup` timestamp NULL DEFAULT NULL,
  `learned` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `card_username` (`username`),
  KEY `card_set_id` (`set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=105219 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for forgets
-- ----------------------------
DROP TABLE IF EXISTS `forgets`;
CREATE TABLE `forgets` (
  `username` varchar(30) DEFAULT NULL,
  `id` varchar(32) NOT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- ----------------------------
-- Table structure for posts
-- ----------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `body` longtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `related` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for remembers
-- ----------------------------
DROP TABLE IF EXISTS `remembers`;
CREATE TABLE `remembers` (
  `username` varchar(30) NOT NULL,
  `series` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL,
  `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`series`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- ----------------------------
-- Table structure for searches
-- ----------------------------
DROP TABLE IF EXISTS `searches`;
CREATE TABLE `searches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `rtfghh` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for sets
-- ----------------------------
DROP TABLE IF EXISTS `sets`;
CREATE TABLE `sets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `username` varchar(30) CHARACTER SET ascii DEFAULT NULL,
  `last_used` timestamp NULL DEFAULT NULL,
  `cards` int(11) unsigned DEFAULT '0',
  `public` tinyint(3) unsigned DEFAULT '1',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(251) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `set_username` (`username`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `email` varchar(254) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `username` varchar(30) NOT NULL DEFAULT '',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(15) DEFAULT NULL,
  `lang` varchar(8) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`username`),
  UNIQUE KEY `user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- ----------------------------
-- Table structure for ev
-- ----------------------------
CREATE TABLE `ev` (
  `en` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `vi` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`en`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
