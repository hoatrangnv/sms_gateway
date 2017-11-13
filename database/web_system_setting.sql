/*
Navicat MySQL Data Transfer

Source Server         : sms gateways
Source Server Version : 50638
Source Host           : 27.118.26.157:3306
Source Database       : sms_gateways

Target Server Type    : MYSQL
Target Server Version : 50638
File Encoding         : 65001

Date: 2017-11-13 14:43:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for web_system_setting
-- ----------------------------
DROP TABLE IF EXISTS `web_system_setting`;
CREATE TABLE `web_system_setting` (
  `system_setting_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `time_check_connect` bigint(8) NOT NULL COMMENT '''Thiết lập thời gian hệ thống tự động kiểm tra kết nối tới các trạm',
  `concatenation_strings` text COMMENT 'Chuỗi ký tự cần ghép, ngăn cách nhau bởi dấu phẩy',
  `concatenation_rule` bigint(8) DEFAULT NULL COMMENT '1- Đầu; 2 - Cuối; 3 - Vị trí giữa radom bất kỳ trong chuỗi',
  `api_manager` text COMMENT 'Nội dung mô tả kết nội API của admin',
  `api_manager_en` text COMMENT 'Nội dung mô tả kết nội API của admin  (Tiếng anh)',
  `api_customer` text COMMENT 'Nội dung mô tả kết nội API của khách hàng',
  `api_customer_en` text COMMENT 'Nội dung mô tả kết nội API của khách hàng  (Tiếng anh)',
  `system_content` text COMMENT 'Thông báo từ hệ thống',
  `system_content_en` text COMMENT 'Thông báo từ hệ thống (Tiếng anh)',
  `created_date` datetime DEFAULT NULL COMMENT 'Thời gian tạo lần đầu',
  `updated_date` datetime DEFAULT NULL COMMENT 'Thời gian cập nhật gần nhất',
  PRIMARY KEY (`system_setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of web_system_setting
-- ----------------------------
INSERT INTO `web_system_setting` VALUES ('1', '1510373669', 'dfdfdf', null, 'nam da sua', null, null, null, null, null, null, null);
INSERT INTO `web_system_setting` VALUES ('2', '1510373102', null, null, 'nam dep trai', null, null, null, null, null, null, null);
INSERT INTO `web_system_setting` VALUES ('3', '1510450170', null, null, 'ádasdasdsad', null, null, null, null, null, null, null);
