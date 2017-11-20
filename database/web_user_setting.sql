/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : sms_gateways

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-11-14 23:37:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for web_user_setting
-- ----------------------------
DROP TABLE IF EXISTS `web_user_setting`;
CREATE TABLE `web_user_setting` (
  `user_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `role_type` tinyint(2) DEFAULT '3',
  `role_name` varchar(255) DEFAULT NULL,
  `priority` tinyint(2) DEFAULT '0' COMMENT 'Độ ưu tiên của admin',
  `payment_type` tinyint(2) DEFAULT '0' COMMENT 'Hình thức thanh toán: 1 - Thanh toán trực tiếp; 2 - Thanh toán sau',
  `account_balance` float(10,2) DEFAULT NULL COMMENT 'Số dư trong tài khoản',
  `scan_auto` tinyint(2) DEFAULT '0' COMMENT 'Máy chủ tự động quét gửi SMS (1 có; 0 không)role type= 1',
  `count_sms_number` int(11) DEFAULT '0' COMMENT 'Đếm số lượng tin đã gửi trong ngày, tự tăng khi truyền thành công gói tin về Trạm (Cuối ngày sẽ reset về 0)',
  `sms_max` int(11) DEFAULT '0' COMMENT '''Số lượng tin max có thể gửi trong ngày theo COM',
  `sms_error_max` int(11) DEFAULT '0' COMMENT 'Số lần gửi lỗi tối đa trong 1 lần kết nối tới COM',
  `sms_send_auto` tinyint(2) DEFAULT NULL COMMENT 'Tin sẽ được gửi tự động hoặc qua bước kiểm duyệt (1 - tự động; 0 - Qua kiểm duyêt) role=2',
  `time_delay_from` int(11) DEFAULT '0' COMMENT 'Thời gian  trễ giữa 2 lần gửi trên 1 COM từ',
  `time_delay_to` int(11) DEFAULT '0' COMMENT 'Thời gian  trễ giữa 2 lần gửi trên 1 COM từ',
  `concatenation_strings` text COMMENT 'Chuỗi ký tự cần ghép, ngăn cách nhau bởi dấu phẩy',
  `concatenation_rule` int(11) DEFAULT NULL COMMENT '1- Đầu; 2 - Cuối; 3 - Vị trí thứ n trong chuỗi (n cho nhập); 4 - Vị trí giữa bất kỳ trong chuỗi',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_user_setting
-- ----------------------------
