/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : sms_gateways

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-11-10 21:57:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for web_menu_system
-- ----------------------------
DROP TABLE IF EXISTS `web_menu_system`;
CREATE TABLE `web_menu_system` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `menu_url` varchar(100) DEFAULT NULL,
  `menu_name` varchar(100) DEFAULT NULL,
  `menu_name_en` varchar(100) DEFAULT NULL,
  `menu_icons` varchar(30) DEFAULT NULL,
  `menu_type` char(10) DEFAULT NULL,
  `role_id` varchar(100) DEFAULT NULL,
  `showcontent` smallint(2) DEFAULT '0',
  `show_permission` smallint(2) DEFAULT '0',
  `show_menu` smallint(2) DEFAULT '1',
  `ordering` int(6) DEFAULT '1',
  `position` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `access_data` text,
  `allow_guest` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_menu_system
-- ----------------------------
INSERT INTO `web_menu_system` VALUES ('1', '0', 'admin.user_view', 'Quản lý Admin', 'Manager Admin', 'fa fa-user icon-4x', null, null, '1', '0', '1', '2', null, '1', null, '1');
INSERT INTO `web_menu_system` VALUES ('2', '1', 'admin.user_view', 'QL người dùng', 'Manager User', 'fa fa-user icon-4x', null, null, '1', '1', '1', '0', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('3', '0', 'admin.user_view', 'QL sản phẩm', 'Manager Product', 'fa fa-gift', null, null, '1', '0', '0', '2', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('4', '1', 'admin.logout', 'Đăng xuất', 'Logout', 'fa fa-user icon-4x', null, null, '0', '1', '1', '10', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('5', '3', 'admin.user_view', 'Sản phẩm', 'Manager Product', 'fa fa-gift', null, null, '1', '1', '1', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('6', '3', 'admin.user_view', 'Danh mục sản phẩm', 'Product Category', 'fa fa-gift', null, null, '1', '1', '1', '2', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('8', '11', 'admin.menuView', 'MenuSystem', 'Menu Site', 'fa fa-sitemap', null, null, '0', '0', '0', '3', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('9', '11', 'admin.permission_view', 'Phân quyền', 'Permission', 'fa fa-user icon-4x', null, null, '0', '0', '0', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('10', '11', 'admin.groupUser_view', 'Nhóm quyền', 'Group Permission', 'fa fa-user icon-4x', null, null, '0', '0', '0', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('11', '0', '#', 'Quản trị Site', 'Manager Site', 'fa fa-cogs icon-4x', null, null, '0', '0', '0', '1', null, '1', null, '0');
