-- --------------------------------------------------------
-- 主机:                           192.168.2.101
-- 服务器版本:                        5.7.19-0ubuntu0.17.04.1 - (Ubuntu)
-- 服务器操作系统:                      Linux
-- HeidiSQL 版本:                  9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- 导出 nowqs 的数据库结构
CREATE DATABASE IF NOT EXISTS `nowqs` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `nowqs`;

-- 导出  表 nowqs.now_auth_group 结构
CREATE TABLE IF NOT EXISTS `now_auth_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户组表',
  `title` varchar(80) NOT NULL COMMENT '用户组名称',
  `rule` text COMMENT '用户组规则组',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：1正常、0关闭',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- 数据导出被取消选择。
-- 导出  表 nowqs.now_auth_group_access 结构
CREATE TABLE IF NOT EXISTS `now_auth_group_access` (
  `uid` bigint(16) NOT NULL COMMENT '用户id',
  `group_id` int(10) NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 nowqs.now_auth_rule 结构
CREATE TABLE IF NOT EXISTS `now_auth_rule` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'AUTH规则表',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `name` varchar(80) NOT NULL COMMENT '规则名',
  `title` varchar(30) DEFAULT NULL COMMENT '规则中文名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '规则状态：1正常、0关闭',
  `type` tinyint(1) DEFAULT NULL,
  `condition` varchar(100) DEFAULT NULL COMMENT '规则验证方式，默认为空',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='AUTH规则表';

-- 数据导出被取消选择。
-- 导出  表 nowqs.now_users 结构
CREATE TABLE IF NOT EXISTS `now_users` (
  `id` bigint(16) NOT NULL AUTO_INCREMENT COMMENT '用户表',
  `username` varchar(80) NOT NULL COMMENT '用户登录名',
  `password` varchar(80) NOT NULL COMMENT '用户密码',
  `name` varchar(50) DEFAULT NULL COMMENT '用户姓名',
  `last_time` int(10) DEFAULT NULL COMMENT '登录时间',
  `last_ip` varchar(20) DEFAULT NULL COMMENT '登录IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- 数据导出被取消选择。
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
