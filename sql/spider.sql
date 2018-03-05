/*
Navicat MySQL Data Transfer

Source Server         : aly
Source Server Version : 50552
Source Host           : localhost:3306
Source Database       : spider

Target Server Type    : MYSQL
Target Server Version : 50552
File Encoding         : 65001

Date: 2018-03-05 09:54:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gushizixun
-- ----------------------------
DROP TABLE IF EXISTS `gushizixun`;
CREATE TABLE `gushizixun` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `sourceName` varchar(50) DEFAULT NULL,
  `sourceUrl` varchar(255) DEFAULT NULL,
  `addtime` char(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gushizixun
-- ----------------------------
