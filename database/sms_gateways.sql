/*
Navicat MySQL Data Transfer

Source Server         : sms gateways
Source Server Version : 50638
Source Host           : 27.118.26.157:3306
Source Database       : sms_gateways

Target Server Type    : MYSQL
Target Server Version : 50638
File Encoding         : 65001

Date: 2017-11-20 17:09:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for web_carrier_setting
-- ----------------------------
DROP TABLE IF EXISTS `web_carrier_setting`;
CREATE TABLE `web_carrier_setting` (
  `carrier_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_name` varchar(255) DEFAULT NULL,
  `slipt_number` int(11) DEFAULT '0' COMMENT 'Số ký tự cần tách',
  `first_number` varchar(255) DEFAULT NULL COMMENT 'Danh sách các đầu số cho phép, nhập cách nhau bởi dấu phẩy',
  `min_number` int(11) DEFAULT '0' COMMENT 'Độ dài tối thiểu của 1 số hợp lệ',
  `max_number` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`carrier_setting_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_carrier_setting
-- ----------------------------
INSERT INTO `web_carrier_setting` VALUES ('1', 'viet', '156', '094', '10', '11', '1', null, null);

-- ----------------------------
-- Table structure for web_customer_setting
-- ----------------------------
DROP TABLE IF EXISTS `web_customer_setting`;
CREATE TABLE `web_customer_setting` (
  `customer_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `priority` tinyint(2) DEFAULT NULL COMMENT 'Độ ưu tiên của khách hàng',
  `payment_type` tinyint(2) DEFAULT '0' COMMENT 'Hình thức thanh toán: 1 - Thanh toán trước; 2 - Thanh toán sau',
  `account_balance` float(10,2) DEFAULT '0.00' COMMENT 'Số dư trong tài khoản',
  `sms_send_auto` tinyint(2) DEFAULT '1' COMMENT 'Tin sẽ được gửi tự động hoặc qua bước kiểm duyệt (1 - tự động; 0 - Qua kiểm duyêt)',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_setting_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_customer_setting
-- ----------------------------

-- ----------------------------
-- Table structure for web_device_token
-- ----------------------------
DROP TABLE IF EXISTS `web_device_token`;
CREATE TABLE `web_device_token` (
  `device_token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Liên kết với id trong table "Manager"',
  `device_code` varchar(255) DEFAULT '0',
  `token` varchar(255) DEFAULT NULL,
  `messeger_center` varchar(255) DEFAULT NULL COMMENT 'Trung tâm tin nhắn:',
  `status` tinyint(2) DEFAULT '1',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`device_token_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_device_token
-- ----------------------------
INSERT INTO `web_device_token` VALUES ('1', '6', '0123', 'maxathuc', 'sdadasd', '1', '2017-11-19 15:47:00', '2017-11-19 15:47:00');

-- ----------------------------
-- Table structure for web_group_user
-- ----------------------------
DROP TABLE IF EXISTS `web_group_user`;
CREATE TABLE `web_group_user` (
  `group_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id nhom nguoi dung',
  `group_user_name` varchar(50) NOT NULL COMMENT 'Ten nhom nguoi dung',
  `group_user_status` int(1) NOT NULL DEFAULT '1' COMMENT '1 : hiá»‡n , 0 : áº©n',
  `group_user_type` int(1) NOT NULL DEFAULT '1' COMMENT '1:admin;2:shop',
  `group_user_order` tinyint(5) DEFAULT '1',
  `group_user_view` tinyint(2) DEFAULT '1' COMMENT '1:view quyền: 0 là ko view',
  PRIMARY KEY (`group_user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_group_user
-- ----------------------------
INSERT INTO `web_group_user` VALUES ('1', 'Super Admin', '1', '1', '1', '1');
INSERT INTO `web_group_user` VALUES ('2', 'Tech code', '1', '1', '1', '1');
INSERT INTO `web_group_user` VALUES ('3', 'Quyền CTV share link', '1', '1', '1', '1');
INSERT INTO `web_group_user` VALUES ('4', 'Boss', '1', '1', '1', '0');

-- ----------------------------
-- Table structure for web_group_user_permission
-- ----------------------------
DROP TABLE IF EXISTS `web_group_user_permission`;
CREATE TABLE `web_group_user_permission` (
  `group_user_id` int(11) NOT NULL COMMENT 'id nhÃ³m',
  `permission_id` int(11) NOT NULL COMMENT 'id quyÃ¨n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_group_user_permission
-- ----------------------------
INSERT INTO `web_group_user_permission` VALUES ('2', '13');
INSERT INTO `web_group_user_permission` VALUES ('2', '18');
INSERT INTO `web_group_user_permission` VALUES ('2', '23');
INSERT INTO `web_group_user_permission` VALUES ('2', '28');
INSERT INTO `web_group_user_permission` VALUES ('2', '33');
INSERT INTO `web_group_user_permission` VALUES ('2', '38');
INSERT INTO `web_group_user_permission` VALUES ('2', '41');
INSERT INTO `web_group_user_permission` VALUES ('2', '42');
INSERT INTO `web_group_user_permission` VALUES ('3', '42');
INSERT INTO `web_group_user_permission` VALUES ('3', '43');
INSERT INTO `web_group_user_permission` VALUES ('1', '1');
INSERT INTO `web_group_user_permission` VALUES ('4', '44');
INSERT INTO `web_group_user_permission` VALUES ('5', '2');
INSERT INTO `web_group_user_permission` VALUES ('5', '3');

-- ----------------------------
-- Table structure for web_manager_setting
-- ----------------------------
DROP TABLE IF EXISTS `web_manager_setting`;
CREATE TABLE `web_manager_setting` (
  `manager_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `priority` tinyint(2) DEFAULT '0' COMMENT 'Độ ưu tiên của admin',
  `payment_type` tinyint(2) DEFAULT '0' COMMENT 'Hình thức thanh toán: 1 - Thanh toán trực tiếp; 2 - Thanh toán sau',
  `account_balance` float(10,2) DEFAULT NULL COMMENT 'Số dư trong tài khoản',
  `scan_auto` tinyint(2) DEFAULT '0' COMMENT 'Máy chủ tự động quét gửi SMS (1 có; 0 không)',
  `count_sms_number` int(11) DEFAULT '0' COMMENT 'Đếm số lượng tin đã gửi trong ngày, tự tăng khi truyền thành công gói tin về Trạm (Cuối ngày sẽ reset về 0)',
  `sms_max` int(11) DEFAULT '0' COMMENT '''Số lượng tin max có thể gửi trong ngày theo COM',
  `sms_error_max` int(11) DEFAULT '0' COMMENT 'Số lần gửi lỗi tối đa trong 1 lần kết nối tới COM',
  `time_delay_from` int(11) DEFAULT '0' COMMENT 'Thời gian  trễ giữa 2 lần gửi trên 1 COM từ',
  `time_delay_to` int(11) DEFAULT '0' COMMENT 'Thời gian  trễ giữa 2 lần gửi trên 1 COM từ',
  `concatenation_strings` text COMMENT 'Chuỗi ký tự cần ghép, ngăn cách nhau bởi dấu phẩy',
  `concatenation_rule` int(11) DEFAULT NULL COMMENT '1- Đầu; 2 - Cuối; 3 - Vị trí thứ n trong chuỗi (n cho nhập); 4 - Vị trí giữa bất kỳ trong chuỗi',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`manager_setting_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_manager_setting
-- ----------------------------

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
  PRIMARY KEY (`menu_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_menu_system
-- ----------------------------
INSERT INTO `web_menu_system` VALUES ('1', '0', 'admin.user_view', 'Quản lý Hệ thống', 'System management', 'fa fa-home icon-4x', null, null, '0', '1', '1', '2', null, '1', null, '1');
INSERT INTO `web_menu_system` VALUES ('2', '1', 'admin.user_view', 'Quản lý Người dùng', 'User Management', 'fa fa-user icon-4x', null, null, '1', '1', '1', '0', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('3', '0', 'admin.user_view', 'Quản lý Trạm', 'Station Management', 'fa fa-bars icon-4x', null, null, '0', '1', '1', '3', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('4', '1', 'admin.systemSettingView', 'Cài đặt Hệ thống', 'System Setting', 'fa fa-cog icon-4x', null, null, '0', '1', '1', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('5', '3', 'admin.user_view', 'Danh sách Trạm', 'Station List', 'fa fa-list-alt icon-4x', null, null, '1', '1', '1', '3', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('6', '3', 'admin.user_view', 'Thống kê Trạm', 'Station Report', 'fa fa-bar-chart icon-4x', null, null, '1', '1', '1', '2', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('8', '11', 'admin.menuView', 'Danh mục', 'Menu Site', 'fa fa-sitemap', null, null, '0', '0', '0', '3', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('9', '11', 'admin.permission_view', 'Phân quyền', 'Permission', 'fa fa-user icon-4x', null, null, '0', '0', '0', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('10', '11', 'admin.groupUser_view', 'Nhóm quyền', 'Group Permission', 'fa fa-users icon-4x', null, null, '0', '0', '0', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('11', '0', '#', 'Quản trị Site', 'Manager Site', 'fa fa-cogs icon-4x', null, null, '0', '0', '0', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('15', '1', 'admin.carrierSettingView', 'Quản lý Nhà mạng', 'Carrier Management', 'fa fa-credit-card icon-4x', null, null, '0', '1', '1', '2', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('16', '0', 'admin.user_view', 'Biểu đồ Tin nhắn', 'SMS Chart', 'fa fa-area-chart icon-4x', null, null, '1', '1', '1', '5', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('17', '16', 'admin.user_view', 'Biểu đồ Tỷ lệ thành công', ' Billing graph of successful', 'fa fa-line-chart icon-4x', null, null, '1', '1', '1', '6', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('18', '0', 'admin.user_view', 'Quản lý Gửi tin', 'SMS Management', 'fa fa-envelope icon-4x', null, null, '0', '1', '1', '4', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('19', '18', 'admin.user_view', 'SMS chờ gửi', 'SMS wating send', 'fa fa-pause icon-4x', null, null, '0', '1', '1', '2', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('20', '18', 'admin.user_view', 'Gửi SMS', 'Send SMS', 'fa fa-paper-plane icon-4x', null, null, '1', '1', '1', '3', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('21', '18', 'admin.user_view', 'Lịch sử Gửi tin', 'Sent SMS History', 'fa fa-history icon-4x', null, null, '1', '1', '1', '5', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('22', '16', 'admin.user_view', 'Sản lượng SMS theo giờ', 'SMS Quality by hour', 'fa fa-bar-chart icon-4x', null, null, '0', '1', '1', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('23', '16', 'admin.user_view', 'Sản lượng SMS theo ngày', 'SMS Quality by day', 'fa fa-bar-chart icon-4x', null, null, '0', '1', '1', '2', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('24', '16', 'admin.user_view', 'Sản lượng SMS theo tháng', 'SMS Quality by month', 'fa fa-bar-chart icon-4x', null, null, '0', '1', '1', '3', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('25', '16', 'admin.user_view', 'Sản lượng SMS theo năm', 'SMS Quality by year', 'fa fa-bar-chart icon-4x', null, null, '0', '1', '1', '4', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('26', '28', 'admin.user_view', 'API kết nối trạm', 'Client API', 'fa fa-life-ring icon-4x', null, null, '1', '1', '1', '7', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('27', '28', 'admin.user_view', 'API Khách hàng', 'Customer API', 'fa fa-recycle icon-4x', null, null, '1', '1', '1', '8', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('28', '0', '#', 'Tài liệu API', 'API Document', 'fa fa-file-text icon-4x', null, null, '0', '1', '1', '7', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('29', '3', 'admin.user_view', 'Cài đặt Trạm', 'Station Setting', 'fa fa-cog icon-4x', null, null, '0', '1', '1', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('30', '18', 'admin.user_view', 'SMS chờ xử lý', 'SMS waiting process', 'fa fa-pause icon-4x', null, null, '0', '1', '1', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('31', '3', 'admin.deviceTokenView', 'Cài đặt thiết bị', 'Setting device', 'fa fa-cog icon-4x', null, null, '1', '0', '1', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('32', '3', 'admin.modemView', 'Modem', 'Modem', 'fa fa-cog icon-4x', null, null, '1', '0', '1', '1', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('33', '18', 'admin.user_view', 'Gửi SMS thông minh', 'Send Smart SMS', 'fa fa-share-square-o icon-4x', null, null, '0', '1', '1', '4', null, '1', null, '0');
INSERT INTO `web_menu_system` VALUES ('34', '18', 'admin.user_view', 'Biểu đồ gửi tin', 'Send SMS chart', 'fa fa-pie-chart 4x-icon', null, null, '1', '1', '1', '6', null, '1', null, '0');

-- ----------------------------
-- Table structure for web_modem
-- ----------------------------
DROP TABLE IF EXISTS `web_modem`;
CREATE TABLE `web_modem` (
  `modem_id` int(11) NOT NULL AUTO_INCREMENT,
  `modem_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `device_id` int(11) DEFAULT '0',
  `digital` varchar(255) DEFAULT NULL COMMENT 'Thông số kỹ thuật của modem',
  `is_active` tinyint(2) DEFAULT '1' COMMENT 'Tình trạng kích hoạt của modem  (1- on; 0 off)',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`modem_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_modem
-- ----------------------------
INSERT INTO `web_modem` VALUES ('1', 'máy tính 1', '3', '1', 'thông số kỹ thuật chưa có gì', '1', '2017-11-19 15:48:00', '2017-11-19 15:48:00');

-- ----------------------------
-- Table structure for web_modem_com
-- ----------------------------
DROP TABLE IF EXISTS `web_modem_com`;
CREATE TABLE `web_modem_com` (
  `modem_com_id` int(11) NOT NULL AUTO_INCREMENT,
  `modem_com_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `modem_id` int(11) DEFAULT '0',
  `carrier_id` int(11) DEFAULT '0' COMMENT 'Mã nhà mạng (0: Không xác định)',
  `carrier_name` varchar(255) DEFAULT NULL,
  `mei_com` varchar(255) DEFAULT NULL COMMENT 'Thông số kỹ thuật của Com',
  `content` varchar(255) DEFAULT NULL COMMENT 'Nội dung cảnh báo',
  `sms_max_com_day` tinyint(5) DEFAULT '0' COMMENT 'Đếm số tin gửi thành công trong ngày để so sánh với số tối đa cho phép gửi  (Cuối ngày sẽ reset về 0)',
  `success_number` tinyint(2) DEFAULT '0' COMMENT 'Số lần gửi thành công trong 1 lần kết nối',
  `error_number` tinyint(5) DEFAULT '0' COMMENT 'Số lần gửi lỗi trong 1 lần kết nối => Khi Com disconnect thì reset về 0',
  `is_active` tinyint(2) DEFAULT '1' COMMENT 'Tình trạng kích hoạt của Com (1- on; 0 off; 2 - error)note: error_number = sms_error_max (theo Manager_Settings) => update is_active = 2',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`modem_com_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_modem_com
-- ----------------------------

-- ----------------------------
-- Table structure for web_permission
-- ----------------------------
DROP TABLE IF EXISTS `web_permission`;
CREATE TABLE `web_permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_code` varchar(50) NOT NULL COMMENT 'MÃ£ quyá»n',
  `permission_name` varchar(50) NOT NULL COMMENT 'TÃªn quyá»n',
  `permission_status` int(1) NOT NULL DEFAULT '1' COMMENT '1:hiá»‡n , 0:áº©n',
  `permission_group_name` varchar(255) DEFAULT NULL COMMENT 'group ten controller',
  PRIMARY KEY (`permission_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_permission
-- ----------------------------
INSERT INTO `web_permission` VALUES ('1', 'root', 'Root', '1', 'Root');
INSERT INTO `web_permission` VALUES ('2', 'user_view', 'Xem danh sách user Admin', '1', 'Tài khoản Admin');
INSERT INTO `web_permission` VALUES ('3', 'user_create', 'Tạo user Admin', '1', 'Tài khoản Admin');
INSERT INTO `web_permission` VALUES ('4', 'user_edit', 'Sửa user Admin', '1', 'Tài khoản Admin');
INSERT INTO `web_permission` VALUES ('5', 'user_change_pass', 'Thay đổi user Admin', '1', 'Tài khoản Admin');
INSERT INTO `web_permission` VALUES ('6', 'user_remove', 'Xóa user Admin', '1', 'Tài khoản Admin');
INSERT INTO `web_permission` VALUES ('7', 'group_user_view', 'Xem nhóm quyền', '1', 'Nhóm quyền');
INSERT INTO `web_permission` VALUES ('8', 'group_user_create', 'Tạo nhóm quyền', '1', 'Nhóm quyền');
INSERT INTO `web_permission` VALUES ('9', 'group_user_edit', 'Sửa nhóm quyền', '1', 'Nhóm quyền');
INSERT INTO `web_permission` VALUES ('10', 'permission_full', 'Full tạo quyền', '1', 'Tạo quyền');
INSERT INTO `web_permission` VALUES ('11', 'permission_create', 'Tạo tạo quyền', '1', 'Tạo quyền');
INSERT INTO `web_permission` VALUES ('12', 'permission_edit', 'Sửa tạo quyền', '1', 'Tạo quyền');
INSERT INTO `web_permission` VALUES ('13', 'banner_full', 'Full quảng cáo', '1', 'Quyền quảng cáo');
INSERT INTO `web_permission` VALUES ('14', 'banner_view', 'Xem quảng cáo', '1', 'Quyền quảng cáo');
INSERT INTO `web_permission` VALUES ('15', 'banner_delete', 'Xóa quảng cáo', '1', 'Quyền quảng cáo');
INSERT INTO `web_permission` VALUES ('16', 'banner_create', 'Tạo quảng cáo', '1', 'Quyền quảng cáo');
INSERT INTO `web_permission` VALUES ('17', 'banner_edit', 'Sửa quảng cáo', '1', 'Quyền quảng cáo');
INSERT INTO `web_permission` VALUES ('18', 'category_full', 'Full danh mục', '1', 'Quyền danh mục');
INSERT INTO `web_permission` VALUES ('19', 'category_view', 'Xem danh mục', '1', 'Quyền danh mục');
INSERT INTO `web_permission` VALUES ('20', 'category_delete', 'Xóa danh mục', '1', 'Quyền danh mục');
INSERT INTO `web_permission` VALUES ('21', 'category_create', 'Tạo danh mục', '1', 'Quyền danh mục');
INSERT INTO `web_permission` VALUES ('22', 'category_edit', 'Sửa danh mục', '1', 'Quyền danh mục');
INSERT INTO `web_permission` VALUES ('23', 'items_full', 'Full tin rao', '1', 'Quyền tin rao');
INSERT INTO `web_permission` VALUES ('24', 'items_view', 'Xem tin rao', '1', 'Quyền tin rao');
INSERT INTO `web_permission` VALUES ('25', 'items_delete', 'Xóa tin rao', '1', 'Quyền tin rao');
INSERT INTO `web_permission` VALUES ('26', 'items_create', 'Tạo tin rao', '1', 'Quyền tin rao');
INSERT INTO `web_permission` VALUES ('27', 'items_edit', 'Sửa tin rao', '1', 'Quyền tin rao');
INSERT INTO `web_permission` VALUES ('28', 'news_full', 'Full tin tức', '1', 'Quyền tin tức');
INSERT INTO `web_permission` VALUES ('29', 'news_view', 'Xem tin tức', '1', 'Quyền tin tức');
INSERT INTO `web_permission` VALUES ('30', 'news_delete', 'Xóa tin tức', '1', 'Quyền tin tức');
INSERT INTO `web_permission` VALUES ('31', 'news_create', 'Tạo tin tức', '1', 'Quyền tin tức');
INSERT INTO `web_permission` VALUES ('32', 'news_edit', 'Sửa tin tức', '1', 'Quyền tin tức');
INSERT INTO `web_permission` VALUES ('33', 'province_full', 'Full tỉnh thành', '1', 'Quyền tỉnh thành');
INSERT INTO `web_permission` VALUES ('34', 'province_view', 'Xem tỉnh thành', '1', 'Quyền tỉnh thành');
INSERT INTO `web_permission` VALUES ('35', 'province_delete', 'Xóa tỉnh thành', '1', 'Quyền tỉnh thành');
INSERT INTO `web_permission` VALUES ('36', 'province_create', 'Tạo tỉnh thành', '1', 'Quyền tỉnh thành');
INSERT INTO `web_permission` VALUES ('37', 'province_edit', 'Sửa tỉnh thành', '1', 'Quyền tỉnh thành');
INSERT INTO `web_permission` VALUES ('38', 'user_customer_full', 'Full khách hàng', '1', 'Quyền khách hàng');
INSERT INTO `web_permission` VALUES ('39', 'user_customer_view', 'Xem khách hàng', '1', 'Quyền khách hàng');
INSERT INTO `web_permission` VALUES ('40', 'user_customer_delete', 'Xóa khách hàng', '1', 'Quyền khách hàng');
INSERT INTO `web_permission` VALUES ('41', 'user_customer_create', 'Tạo khách hàng', '1', 'Quyền khách hàng');
INSERT INTO `web_permission` VALUES ('42', 'user_customer_edit', 'Sửa khách hàng', '1', 'Quyền khách hàng');
INSERT INTO `web_permission` VALUES ('43', 'toolsCommon_full', 'Full view share', '1', 'Full Quyền share');
INSERT INTO `web_permission` VALUES ('44', 'is_boss', 'Boss', '1', 'Boss');

-- ----------------------------
-- Table structure for web_sms_customer
-- ----------------------------
DROP TABLE IF EXISTS `web_sms_customer`;
CREATE TABLE `web_sms_customer` (
  `sms_customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `correct_number` int(11) DEFAULT '0' COMMENT 'Tổng số lượng số hợp lệ',
  `incorrect_number` int(11) DEFAULT '0' COMMENT 'tổng không hợp lệ',
  `incorrect_number_list` text COMMENT 'Chuỗi số không hợp lệ, ngăn cách bởi dấu phẩy',
  `status` tinyint(2) DEFAULT '1',
  `status_name` varchar(255) DEFAULT NULL COMMENT 'Tương ứng status: Pending, Successful',
  `send_date` int(12) DEFAULT NULL COMMENT 'yyyymmdd',
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`sms_customer_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_sms_customer
-- ----------------------------

-- ----------------------------
-- Table structure for web_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `web_sms_log`;
CREATE TABLE `web_sms_log` (
  `sms_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_customer_id` int(11) DEFAULT '0',
  `sms_customer_id` int(11) DEFAULT '0' COMMENT 'id gói tin gốc, liên kết với id bảng "Sms_Customer"',
  `carrier_id` int(11) DEFAULT NULL,
  `carrier_name` varchar(255) DEFAULT NULL,
  `user_manager_id` int(11) DEFAULT '0',
  `total_sms` int(11) DEFAULT '0' COMMENT 'Tổng số SMS cần gửi sau khi được tách theo từng nhà mạng',
  `send_sussesssful` int(11) DEFAULT '0' COMMENT 'Đếm số lần gửi thành công',
  `send_fail` int(11) DEFAULT '0' COMMENT 'Số lần thất bại',
  `sms_max` int(11) DEFAULT '0' COMMENT 'Tổng số sms gửi để check số lương max trong lần kế nối, trong ngày',
  `status` tinyint(2) DEFAULT '1',
  `status_name` varchar(255) DEFAULT NULL,
  `send_date` int(12) DEFAULT '0' COMMENT 'yyyymmdd',
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`sms_log_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_sms_log
-- ----------------------------

-- ----------------------------
-- Table structure for web_sms_report
-- ----------------------------
DROP TABLE IF EXISTS `web_sms_report`;
CREATE TABLE `web_sms_report` (
  `sms_report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_customer_id` int(11) DEFAULT '0',
  `user_manager_id` int(11) DEFAULT '0',
  `carrier_id` int(11) DEFAULT '0',
  `success_number` int(10) DEFAULT '0',
  `fail_number` int(10) DEFAULT '0',
  `hour` int(5) DEFAULT '0',
  `day` int(5) DEFAULT '0',
  `month` int(5) DEFAULT '0',
  `year` int(5) DEFAULT '0',
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`sms_report_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_sms_report
-- ----------------------------

-- ----------------------------
-- Table structure for web_sms_sendTo
-- ----------------------------
DROP TABLE IF EXISTS `web_sms_sendTo`;
CREATE TABLE `web_sms_sendTo` (
  `sms_sendTo_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_log_id` int(11) DEFAULT '0' COMMENT 'id gói tin sau khi được tách, liên kết với id trong table "Sms_Log"',
  `sms_customer_id` int(11) DEFAULT '0' COMMENT 'id gói tin gốc, liên kết với id bảng "Sms_Customer"',
  `user_customer_id` int(11) DEFAULT '0',
  `carrier_id` int(11) DEFAULT NULL,
  `user_manager_id` int(11) DEFAULT '0',
  `modem_id` int(11) DEFAULT NULL,
  `com_id` int(11) DEFAULT NULL,
  `phone_receive` varchar(255) DEFAULT NULL COMMENT 'Số điện thoại nhận',
  `phone_send` varchar(255) DEFAULT NULL COMMENT 'Số điện thoại gửi',
  `status` tinyint(2) DEFAULT '1',
  `status_name` varchar(255) DEFAULT NULL COMMENT 'Tương ứng status: Pending, Success, Fail',
  `content` varchar(255) DEFAULT NULL,
  `hour` int(5) DEFAULT NULL,
  `day` int(5) DEFAULT NULL,
  `month` int(5) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `send_date` int(12) DEFAULT NULL COMMENT 'yyyymmdd',
  `send_date_at` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`sms_sendTo_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_sms_sendTo
-- ----------------------------

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
  PRIMARY KEY (`system_setting_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of web_system_setting
-- ----------------------------
INSERT INTO `web_system_setting` VALUES ('1', '1510373669', 'dfdfdf', null, 'nam da sua', null, null, null, null, null, null, null);
INSERT INTO `web_system_setting` VALUES ('2', '1510373102', null, null, 'nam dep trai', null, null, null, null, null, null, null);
INSERT INTO `web_system_setting` VALUES ('3', '1510450170', 'sdsdfsd', '1', 'ádasdasdsad', null, null, null, null, null, null, '2017-11-19 15:44:00');

-- ----------------------------
-- Table structure for web_user
-- ----------------------------
DROP TABLE IF EXISTS `web_user`;
CREATE TABLE `web_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_parent` int(11) DEFAULT '0',
  `user_name` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_full_name` varchar(255) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_phone` varchar(11) DEFAULT NULL,
  `user_status` int(2) NOT NULL DEFAULT '1' COMMENT '-1: xÃ³a , 1: active',
  `user_sex` tinyint(2) DEFAULT '1',
  `user_group` varchar(255) DEFAULT NULL,
  `user_group_menu` varchar(255) DEFAULT NULL COMMENT 'view menu admin',
  `user_view` tinyint(2) DEFAULT '1',
  `user_last_login` int(11) DEFAULT NULL,
  `user_last_ip` varchar(15) DEFAULT NULL,
  `user_create_id` int(11) DEFAULT NULL,
  `user_create_name` varchar(255) DEFAULT NULL,
  `user_edit_id` int(11) DEFAULT NULL,
  `user_edit_name` varchar(255) DEFAULT NULL,
  `user_created` int(11) DEFAULT NULL,
  `user_updated` int(11) DEFAULT NULL,
  `role_type` tinyint(2) DEFAULT '3' COMMENT '1:SuperAdmin, 2:Admin, 3:Customer',
  `role_name` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `number_code` varchar(250) DEFAULT NULL,
  `address_register` varchar(255) DEFAULT NULL COMMENT 'địa chỉ kinh doanh',
  `telephone` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_user
-- ----------------------------
INSERT INTO `web_user` VALUES ('2', '0', 'admin', 'eef828faf0754495136af05c051766cb', 'Root', 'quynh@gmail.com', '', '1', '1', '4', '8,4,5', '1', '1510040205', '127.0.0.1', null, null, '2', 'admin', null, '1508643546', '3', null, null, null, null, null);
INSERT INTO `web_user` VALUES ('3', '0', 'quynhtm', '62c6e06921a2188643e58935b9e6c880', 'Trương Mạnh Quynh', 'quynh@gmail.com', '09838413368', '1', '1', '1,4', '2,4,5', '1', '1511105955', '117.5.222.197', '2', 'admin', '2', 'admin', '1499654993', '1510040229', '3', null, null, null, null, null);
INSERT INTO `web_user` VALUES ('4', '0', 'hienlt', '08390aa6f4dbc50eeeda7d7bec630034', 'Lê Thị Hiến', 'hien46k2@gmail.com', '', '1', '1', '1,4', '3,2,4,5,6', '1', '1511166795', '113.176.7.67', '2', 'admin', '4', 'hienlt', '1510040284', '1510182613', '3', null, null, null, null, null);
INSERT INTO `web_user` VALUES ('5', '0', 'dienbt', 'e7a19d3aa1bcac298816c5a23e92947b', 'Bùi Tiến Diện', 'dienbt@hanelsoft.vn', '', '1', '1', '1,4', '2,4,5', '1', '1511115986', '42.112.91.117', '2', 'admin', '2', 'admin', '1510040323', '1510040323', '3', null, null, null, null, null);
INSERT INTO `web_user` VALUES ('6', '0', 'namnv', 'ac66700a2642519819379dcc151fcc62', 'Nguyễn Văn Nam', 'namnv@hanelsofn.vn', '', '1', '1', '4', '2,4,5', '1', '1510974462', '113.190.161.95', '2', 'admin', '2', 'admin', '1510040354', '1510040354', '3', null, null, null, null, null);
INSERT INTO `web_user` VALUES ('7', '0', 'quantriAdmin', 'b7997c3d2f3125784f715992b55e10c1', 'Quản trị Admin SMS', 'leeduxng@gmail.com', '', '1', '1', '1', '1,3,18,16,28,2,4,15,29,6,5,22,23,24,25,17,30,19,20,21,26,27', '1', '1510546803', '14.166.203.210', '2', 'admin', '3', 'quynhtm', '1510040642', '1511106152', '1', 'SuperAdmin', null, null, null, null);

-- ----------------------------
-- Table structure for web_user_carrier_setting
-- ----------------------------
DROP TABLE IF EXISTS `web_user_carrier_setting`;
CREATE TABLE `web_user_carrier_setting` (
  `user_carrier_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT '0' COMMENT 'Liên kết với id của bảng Carrier_Setting',
  `user_id` int(11) DEFAULT NULL,
  `cost` float(10,2) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_carrier_setting_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_user_carrier_setting
-- ----------------------------
INSERT INTO `web_user_carrier_setting` VALUES ('1', '1', '7', '345.00', '2017-11-19 03:45:01', '2017-11-19 03:45:01');

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
  PRIMARY KEY (`user_setting_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_user_setting
-- ----------------------------
INSERT INTO `web_user_setting` VALUES ('1', '7', '1', 'SuperAdmin', '1', '1', '1500000.00', '0', '0', '0', '0', null, '0', '0', null, null, '2017-11-19 03:45:01', '2017-11-19 03:45:01');
