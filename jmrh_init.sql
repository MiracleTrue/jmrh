/*
Navicat MySQL Data Transfer

Source Server         : 博采服务器_002
Source Server Version : 50720
Source Host           : 47.104.96.181:3306
Source Database       : jmrh

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2017-11-24 11:32:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `order_offer`
-- ----------------------------
DROP TABLE IF EXISTS `order_offer`;
CREATE TABLE `order_offer` (
  `offer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '报价状态:  -1.已超期   0.待报价   1.等待通过    2.未通过   3.已通过   4.已发货',
  `price` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '单价',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '确认时间',
  `warning_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '到货预警时间',
  `warning_is_sms` tinyint(4) NOT NULL DEFAULT '0' COMMENT '预警订单是否发送过短信  1.已发送   0.未发送',
  `allocation_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分配人的id',
  PRIMARY KEY (`offer_id`),
  KEY `fk_order_offer_order1_idx` (`order_id`),
  KEY `fk_order_offer_users1_idx` (`user_id`),
  CONSTRAINT `fk_order_offer_order1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_offer_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=661 DEFAULT CHARSET=utf8 COMMENT='订单报价表';

-- ----------------------------
-- Records of order_offer
-- ----------------------------

-- ----------------------------
-- Table structure for `orders`
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `army_id` int(10) unsigned DEFAULT NULL COMMENT '如果是军方订单,对应军方用户id',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单类型:  1.军方发布   2.平台发布   0.无效',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '订单状态:    \n0.待分配   1.重新分配   \n100.已分配供应商    110.已选择供应商    120.供应商已发货   130.供应商已到货\n200.库存供应    \n1000.已发货到军方    \n9000.军方已收货(交易成功)',
  `order_sn` varchar(45) NOT NULL DEFAULT '' COMMENT '订单号',
  `product_name` varchar(45) NOT NULL DEFAULT '' COMMENT '产品名称',
  `product_number` int(11) NOT NULL DEFAULT '1' COMMENT '产品数量',
  `product_unit` varchar(10) NOT NULL DEFAULT '' COMMENT '产品计量单位',
  `platform_receive_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '平台接收时间',
  `army_receive_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '军方接收时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除状态:  1.已删除  0.正常',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_sn_UNIQUE` (`order_sn`),
  KEY `fk_orders_users1_idx` (`army_id`),
  CONSTRAINT `fk_orders_users1` FOREIGN KEY (`army_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=258 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of orders
-- ----------------------------

-- ----------------------------
-- Table structure for `product_category`
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(45) NOT NULL DEFAULT '' COMMENT '分类名称',
  `unit` varchar(10) NOT NULL DEFAULT '' COMMENT '计量单位',
  `labels` varchar(255) NOT NULL DEFAULT '' COMMENT '标签列表',
  `sort` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_index` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否首页显示',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除状态:  1.已删除  0.正常',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='商品分类表';

-- ----------------------------
-- Records of product_category
-- ----------------------------

-- ----------------------------
-- Table structure for `products`
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `product_name` varchar(45) NOT NULL DEFAULT '' COMMENT '商品名称',
  `product_thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `product_original` varchar(255) NOT NULL DEFAULT '' COMMENT '商品原图',
  `product_content` varchar(2000) NOT NULL DEFAULT '' COMMENT '商品详情',
  `sort` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除状态:  1.已删除  0.正常',
  PRIMARY KEY (`product_id`),
  KEY `fk_products_product_category1_idx` (`category_id`),
  CONSTRAINT `fk_products_product_category1` FOREIGN KEY (`category_id`) REFERENCES `product_category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COMMENT='商品表';

-- ----------------------------
-- Records of products
-- ----------------------------

-- ----------------------------
-- Table structure for `user_log`
-- ----------------------------
DROP TABLE IF EXISTS `user_log`;
CREATE TABLE `user_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `log_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '日志描述',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ip_address` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP地址',
  PRIMARY KEY (`log_id`),
  KEY `fk_user_log_users1_idx` (`user_id`),
  CONSTRAINT `fk_user_log_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1058 DEFAULT CHARSET=utf8 COMMENT='用户操作日志表';

-- ----------------------------
-- Records of user_log
-- ----------------------------

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identity` tinyint(4) NOT NULL DEFAULT '0' COMMENT '身份标识: 1.超级管理员  2.平台运营员 3.供货商  4.军方  0.无效',
  `user_name` varchar(45) NOT NULL DEFAULT '' COMMENT '用户名',
  `nick_name` varchar(45) NOT NULL DEFAULT '' COMMENT '姓名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `phone` varchar(15) NOT NULL DEFAULT '' COMMENT '电话',
  `is_disable` tinyint(4) NOT NULL DEFAULT '0' COMMENT '禁用状态:  1.禁用  0.启用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name_UNIQUE` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '1', 'admin', '超级管理员', '$2y$10$TI.hXTC7sQqvwK9ogIta3uGgzjfkdV1qTYsXPWlvX6gKsKcGefJlW', '-', '0', '1507789254');
