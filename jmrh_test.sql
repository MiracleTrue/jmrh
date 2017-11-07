/*
Navicat MySQL Data Transfer

Source Server         : 本机Windows
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : jmrh

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-11-07 11:55:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `orders`
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  UNIQUE KEY `order_sn_UNIQUE` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of orders
-- ----------------------------
INSERT INTO `orders` VALUES ('46', '2', '100', '20171026140463013482', '橘子', '202', '个', '1509897600', '0', '1508997651', '0');
INSERT INTO `orders` VALUES ('47', '2', '100', '20171026140784033116', '苹果', '691', '个', '1509897600', '0', '1508997657', '0');
INSERT INTO `orders` VALUES ('48', '2', '100', '20171026140220019302', '梨子', '535', '个', '1509897600', '0', '1508997663', '0');
INSERT INTO `orders` VALUES ('49', '2', '100', '20171026140812099799', '可乐', '666', '个', '1509897600', '0', '1508997674', '0');
INSERT INTO `orders` VALUES ('50', '2', '100', '20171026140178047822', '商品167', '732', '个', '1509897600', '0', '1508997697', '0');
INSERT INTO `orders` VALUES ('51', '2', '100', '20171026140196063208', '商品262', '758', '个', '1509897600', '0', '1508997697', '0');
INSERT INTO `orders` VALUES ('52', '2', '100', '20171026140213041154', '商品695', '102', '个', '1509897600', '0', '1508997698', '0');
INSERT INTO `orders` VALUES ('53', '2', '100', '20171026140226031924', '商品913', '143', '个', '1509897600', '0', '1508997698', '0');
INSERT INTO `orders` VALUES ('54', '2', '100', '20171026141019098525', '蔬菜670', '660', '个', '1509897600', '0', '1508997712', '0');
INSERT INTO `orders` VALUES ('55', '2', '100', '20171026141033002997', '蔬菜785', '22', '个', '1509897600', '0', '1508997712', '0');
INSERT INTO `orders` VALUES ('56', '1', '110', '20171026141048002848', '蔬菜43', '774', '个', '1509897600', '0', '1508997712', '0');
INSERT INTO `orders` VALUES ('57', '1', '1', '20171026140681016511', '军方需求73', '203', '个', '0', '1509897600', '1508997926', '0');
INSERT INTO `orders` VALUES ('58', '1', '1000', '20171026140766058642', '军方需求62', '206', '个', '0', '1509897600', '1508997928', '0');
INSERT INTO `orders` VALUES ('59', '1', '1000', '20171026140557057849', '军方需求45', '149', '个', '1509897600', '1509897600', '1508997929', '0');
INSERT INTO `orders` VALUES ('60', '1', '130', '20171026141034025906', '军方需求12', '447', '个', '1509897600', '1509897600', '1508997932', '0');
INSERT INTO `orders` VALUES ('61', '1', '1000', '20171026141047075185', '军方需求9', '170', '个', '0', '1509897600', '1508997933', '0');
INSERT INTO `orders` VALUES ('62', '1', '100', '20171031095833018785', 'sad', '1', '瓶', '1510816203', '1510243200', '1509415110', '0');
INSERT INTO `orders` VALUES ('63', '1', '0', '20171031100847085359', '12', '12', '瓶', '0', '1509580800', '1509415374', '1');
INSERT INTO `orders` VALUES ('64', '1', '0', '20171031101230073137', '12', '122121', '瓶', '0', '1509667200', '1509415448', '1');
INSERT INTO `orders` VALUES ('65', '1', '0', '20171031100720001560', '12', '123', '瓶', '0', '1509667200', '1509415579', '1');
INSERT INTO `orders` VALUES ('66', '1', '0', '20171031101177049976', '21', '12', '瓶', '0', '1510272000', '1509415684', '1');
INSERT INTO `orders` VALUES ('67', '1', '0', '20171031101548089430', '12', '21', '瓶', '0', '1510156800', '1509415953', '1');
INSERT INTO `orders` VALUES ('68', '1', '0', '20171031140590030912', '啊啊啊', '131', '斤', '0', '1509638400', '1509429911', '1');
INSERT INTO `orders` VALUES ('69', '1', '0', '20171101094625016011', '韦尔奇无', '1', '瓶', '0', '1510761600', '1509500312', '1');
INSERT INTO `orders` VALUES ('70', '1', '1000', '20171101094447030002', '韦尔奇无', '1', '瓶', '0', '1510761600', '1509500313', '0');
INSERT INTO `orders` VALUES ('71', '1', '1000', '20171101094608050546', '12', '12', '瓶', '0', '1510243200', '1509500326', '0');
INSERT INTO `orders` VALUES ('72', '1', '1000', '20171101094333094370', '12', '12', '瓶', '0', '1510848000', '1509500334', '0');
INSERT INTO `orders` VALUES ('73', '1', '100', '20171101094442011690', '12', '12', '瓶', '1510280954', '1510848000', '1509500341', '0');
INSERT INTO `orders` VALUES ('74', '1', '1000', '20171101093915060010', '12', '12', '瓶', '0', '1511539200', '1509500350', '0');
INSERT INTO `orders` VALUES ('75', '1', '1000', '20171101094214094428', '12', '12', '瓶', '0', '1510934400', '1509500361', '0');
INSERT INTO `orders` VALUES ('76', '1', '100', '20171101094034089549', '12', '12', '瓶', '1510761600', '1510934400', '1509500361', '0');
INSERT INTO `orders` VALUES ('77', '2', '100', '20171101174496038158', '12', '12', '斤', '1510156800', '0', '1509528933', '0');
INSERT INTO `orders` VALUES ('78', '1', '0', '20171103152897002004', '全文', '12', '瓶', '0', '1510848000', '1509693643', '0');
INSERT INTO `orders` VALUES ('79', '1', '100', '20171103152241076416', '1人2', '12', '瓶', '1510848000', '1510156800', '1509693666', '0');
INSERT INTO `orders` VALUES ('80', '1', '110', '20171103152529015001', '212121', '2121', '瓶', '1511366400', '1511452800', '1509693756', '0');
INSERT INTO `orders` VALUES ('81', '2', '100', '20171103152425071948', '12', '12', '瓶', '1511366400', '0', '1509693822', '0');
INSERT INTO `orders` VALUES ('82', '2', '100', '20171103152987058919', '全文21', '21', '瓶', '1510588800', '0', '1509693916', '0');

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
  PRIMARY KEY (`offer_id`),
  KEY `fk_order_offer_order1_idx` (`order_id`),
  KEY `fk_order_offer_users1_idx` (`user_id`),
  CONSTRAINT `fk_order_offer_order1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_offer_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8 COMMENT='订单报价表';

-- ----------------------------
-- Records of order_offer
-- ----------------------------
INSERT INTO `order_offer` VALUES ('83', '46', '42', '0', '0.0000', '0.00', '1508997651', '1513465600', '0');
INSERT INTO `order_offer` VALUES ('84', '46', '40', '-1', '0.0000', '0.00', '1508997651', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('85', '46', '41', '-1', '0.0000', '0.00', '1508997651', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('86', '47', '42', '-1', '0.0000', '0.00', '1508997657', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('87', '47', '40', '-1', '0.0000', '0.00', '1508997657', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('88', '47', '41', '-1', '0.0000', '0.00', '1508997657', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('89', '48', '42', '-1', '0.0000', '0.00', '1508997663', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('90', '48', '40', '-1', '0.0000', '0.00', '1508997663', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('91', '48', '41', '-1', '0.0000', '0.00', '1508997663', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('92', '49', '42', '-1', '0.0000', '0.00', '1508997674', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('93', '49', '40', '-1', '0.0000', '0.00', '1508997674', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('94', '49', '41', '-1', '0.0000', '0.00', '1508997674', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('95', '50', '42', '-1', '0.0000', '0.00', '1508997697', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('96', '50', '40', '-1', '0.0000', '0.00', '1508997697', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('97', '50', '41', '-1', '0.0000', '0.00', '1508997697', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('98', '51', '42', '-1', '0.0000', '0.00', '1508997697', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('99', '51', '40', '-1', '0.0000', '0.00', '1508997697', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('100', '51', '41', '-1', '0.0000', '0.00', '1508997697', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('101', '52', '42', '-1', '0.0000', '0.00', '1508997698', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('102', '52', '40', '-1', '0.0000', '0.00', '1508997698', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('103', '52', '41', '-1', '0.0000', '0.00', '1508997698', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('104', '53', '42', '-1', '0.0000', '0.00', '1508997698', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('105', '53', '40', '-1', '0.0000', '0.00', '1508997698', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('106', '53', '41', '-1', '0.0000', '0.00', '1508997698', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('107', '54', '42', '-1', '0.0000', '0.00', '1508997712', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('108', '54', '40', '-1', '0.0000', '0.00', '1508997712', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('109', '54', '41', '-1', '0.0000', '0.00', '1508997712', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('110', '55', '42', '-1', '0.0000', '0.00', '1508997712', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('111', '55', '40', '-1', '0.0000', '0.00', '1508997712', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('112', '55', '41', '-1', '0.0000', '0.00', '1508997712', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('113', '56', '42', '2', '0.0000', '0.00', '1508997712', '1509465600', '0');
INSERT INTO `order_offer` VALUES ('114', '56', '40', '2', '11.3695', '8800.00', '1508997712', '1520552000', '0');
INSERT INTO `order_offer` VALUES ('115', '56', '41', '3', '10.0000', '100.00', '1508997712', '1519465600', '0');
INSERT INTO `order_offer` VALUES ('118', '59', '41', '-1', '0.0000', '0.00', '1509355120', '1509465600', '7200');
INSERT INTO `order_offer` VALUES ('121', '60', '41', '-1', '0.0000', '0.00', '1509355223', '1509465600', '7200');
INSERT INTO `order_offer` VALUES ('122', '77', '39', '0', '0.0000', '0.00', '1509528933', '1511516127', '0');
INSERT INTO `order_offer` VALUES ('123', '76', '39', '0', '0.0000', '0.00', '1509589735', '1511490531', '14400');
INSERT INTO `order_offer` VALUES ('124', '73', '39', '0', '0.0000', '0.00', '1509589757', '1512008953', '0');
INSERT INTO `order_offer` VALUES ('125', '62', '39', '0', '0.0000', '0.00', '1509693009', '1512457806', '0');
INSERT INTO `order_offer` VALUES ('126', '79', '40', '0', '0.0000', '0.00', '1509693705', '1512458502', '0');
INSERT INTO `order_offer` VALUES ('127', '79', '39', '0', '0.0000', '0.00', '1509693705', '1512458502', '0');
INSERT INTO `order_offer` VALUES ('128', '80', '39', '2', '0.0000', '0.00', '1509693789', '1511335386', '0');
INSERT INTO `order_offer` VALUES ('129', '80', '40', '4', '58.0495', '123123.00', '1509693789', '1511335386', '0');
INSERT INTO `order_offer` VALUES ('130', '81', '39', '0', '0.0000', '0.00', '1509693822', '1511508219', '0');
INSERT INTO `order_offer` VALUES ('131', '82', '40', '1', '1.0000', '21.00', '1509693916', '1510730714', '0');
INSERT INTO `order_offer` VALUES ('132', '82', '41', '0', '0.0000', '0.00', '1509693916', '1510730714', '0');
INSERT INTO `order_offer` VALUES ('133', '82', '39', '0', '0.0000', '0.00', '1509693916', '1510730714', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COMMENT='商品表';

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES ('30', '22', '上海来伊份 菲律宾香蕉片 500克 果干休闲零食 江浙沪皖满百包邮', 'thumb/201711/1/WtUdn0epwpRV7zfl0THqNjLGRHyj0xXHfmFO0cKh.jpeg', 'original/201711/1/WtUdn0epwpRV7zfl0THqNjLGRHyj0xXHfmFO0cKh.jpeg', '<hr/><hr/><hr/><hr/><hr/><p><img src=\"/uploads/ueditor/image/20171107/1510017183.jpg\" title=\"1510017183.jpg\" alt=\"5cf6cb3fd523e674d9ba886b12e94813.jpg\" width=\"561\" height=\"297\"/></p>', '100', '1509958380', '0');
INSERT INTO `products` VALUES ('31', '22', '俄罗斯巧克力威化榛仁夹心饼干 进口休闲零食 200g/袋', 'thumb/201711/1/Z5p6EtfIIO3P0FLooXbuUi5OfKYPRLWS2KMGJv4Q.jpeg', 'original/201711/1/Z5p6EtfIIO3P0FLooXbuUi5OfKYPRLWS2KMGJv4Q.jpeg', '', '100', '1509958415', '0');
INSERT INTO `products` VALUES ('32', '22', '飘零大叔原味紫薯脆 地瓜干果干紫薯条地瓜条办公室休闲零食128g', 'thumb/201711/1/d5BQafezvMgY7CAXWc4Yfjb4xcP2u6lPNhkJwSjm.jpeg', 'original/201711/1/d5BQafezvMgY7CAXWc4Yfjb4xcP2u6lPNhkJwSjm.jpeg', '', '100', '1509958432', '0');
INSERT INTO `products` VALUES ('33', '22', '喜之郎蜜桔果肉果冻450g中袋装 休闲怀旧零食小吃大礼包婚庆', 'thumb/201711/1/gnZ56teV9K3f0uKz9u4KIbLLG11Uqe3mDWoV6loI.jpeg', 'original/201711/1/gnZ56teV9K3f0uKz9u4KIbLLG11Uqe3mDWoV6loI.jpeg', '', '100', '1509958460', '0');
INSERT INTO `products` VALUES ('34', '24', '爱尔兰进口洋酒 奥妙10年单一麦芽威士忌Bushmills布什米尔斯10年', 'thumb/201711/1/ceUvyECpLzsJ3DJIuGINXr2PXvGFks49FoSU1GUB.jpeg', 'original/201711/1/ceUvyECpLzsJ3DJIuGINXr2PXvGFks49FoSU1GUB.jpeg', '', '100', '1509958567', '0');
INSERT INTO `products` VALUES ('35', '24', '52度泸州老窖精品头曲光瓶品鉴酒500ml浓香型', 'thumb/201711/1/oOfsXsFI2r146unZsF04C4m89b5niX2iVh0z9LHn.jpeg', 'original/201711/1/oOfsXsFI2r146unZsF04C4m89b5niX2iVh0z9LHn.jpeg', '', '100', '1509958598', '0');
INSERT INTO `products` VALUES ('36', '24', '日本进口 现货 麒麟冰结果酒 KIRIN Chu-Hi 氷結 青乌梅 350ml', 'thumb/201711/1/1teAvNi3dtYVPY1JI5ljr8m4PRmcVN2AB5yz3QSJ.jpeg', 'original/201711/1/1teAvNi3dtYVPY1JI5ljr8m4PRmcVN2AB5yz3QSJ.jpeg', '', '100', '1509958617', '0');
INSERT INTO `products` VALUES ('37', '25', '奥迪', 'thumb/201711/1/xcjYgGjDiffC2T02VRBsdObICFE3stFxh1NOm03h.jpeg', 'original/201711/1/xcjYgGjDiffC2T02VRBsdObICFE3stFxh1NOm03h.jpeg', '', '1', '1509959984', '0');
INSERT INTO `products` VALUES ('38', '25', '宝马', 'thumb/201711/1/Wtx7JvGnYbSrIhCQ4uLDf2MVHodYhWQ0XLBXkt08.jpeg', 'original/201711/1/Wtx7JvGnYbSrIhCQ4uLDf2MVHodYhWQ0XLBXkt08.jpeg', '', '1', '1509960002', '0');
INSERT INTO `products` VALUES ('39', '25', '奔驰', 'thumb/201711/1/VBb7efLPku5zIuLb0EQFE2mOPG73Mgib73dYSqK1.jpeg', 'original/201711/1/VBb7efLPku5zIuLb0EQFE2mOPG73Mgib73dYSqK1.jpeg', '', '1', '1509960012', '0');
INSERT INTO `products` VALUES ('40', '25', '卡宴', 'thumb/201711/1/uRQv2MyyOAKyVSzPFf81WVgPts06dKf4jVR4NMNX.jpeg', 'original/201711/1/uRQv2MyyOAKyVSzPFf81WVgPts06dKf4jVR4NMNX.jpeg', '', '100', '1509960031', '0');
INSERT INTO `products` VALUES ('41', '25', '爱玛电动车', 'thumb/201711/1/1SB08wAhwv15v802H23aVfQMRQmlayFFvdaTJ1KQ.jpeg', 'original/201711/1/1SB08wAhwv15v802H23aVfQMRQmlayFFvdaTJ1KQ.jpeg', '', '1', '1509960051', '0');
INSERT INTO `products` VALUES ('42', '25', '捷安特', 'thumb/201711/1/42u0U4NVChAIkOVOzNQZUiC6fFH4kIeUXimXgcoR.png', 'original/201711/1/42u0U4NVChAIkOVOzNQZUiC6fFH4kIeUXimXgcoR.png', '', '1', '1509960066', '0');
INSERT INTO `products` VALUES ('43', '25', 'AAA', 'thumb/201711/1/3YaNkoOi96AsCths3PE2pLebY71rmgVT8QSh5tDc.jpeg', 'original/201711/1/3YaNkoOi96AsCths3PE2pLebY71rmgVT8QSh5tDc.jpeg', '', '1', '1509960079', '0');
INSERT INTO `products` VALUES ('44', '25', 'BBB', 'thumb/201711/1/jqwYEw1fV90RWZxueo0rM6H184gwsIbvKjE2MV5X.jpeg', 'original/201711/1/jqwYEw1fV90RWZxueo0rM6H184gwsIbvKjE2MV5X.jpeg', '', '1', '1509960086', '0');
INSERT INTO `products` VALUES ('45', '25', 'CCC', 'thumb/201711/1/wCwffjL0xTMoLPvAiMFFcQLKPnCoP5kR6cg6RWDz.jpeg', 'original/201711/1/wCwffjL0xTMoLPvAiMFFcQLKPnCoP5kR6cg6RWDz.jpeg', '', '1', '1509960092', '0');
INSERT INTO `products` VALUES ('46', '25', 'DDD', 'thumb/201711/1/fTBwtVa9IXPxJ0ZxzAJtbQCwfQLES9c8xwQrDIrU.jpeg', 'original/201711/1/fTBwtVa9IXPxJ0ZxzAJtbQCwfQLES9c8xwQrDIrU.jpeg', '', '1', '1509960099', '0');
INSERT INTO `products` VALUES ('47', '25', 'EEE', 'thumb/201711/1/BC9F0WeYsu1R7A31FQwsMGRFu4WEL1aBr9Qt9O6y.jpeg', 'original/201711/1/BC9F0WeYsu1R7A31FQwsMGRFu4WEL1aBr9Qt9O6y.jpeg', '', '1', '1509960107', '0');
INSERT INTO `products` VALUES ('48', '25', 'FFF', 'thumb/201711/1/5Bd3jKqdDHXlp8qGPMpcaQqCeCPj4vl0jlwaRD3H.jpeg', 'original/201711/1/5Bd3jKqdDHXlp8qGPMpcaQqCeCPj4vl0jlwaRD3H.jpeg', '', '1', '1509960113', '0');
INSERT INTO `products` VALUES ('49', '25', 'GGG', 'thumb/201711/1/2L9gxCKhncqylOnWskqKKUSXmyZo7IoGnYHzQpdN.jpeg', 'original/201711/1/2L9gxCKhncqylOnWskqKKUSXmyZo7IoGnYHzQpdN.jpeg', '', '1', '1509960120', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='商品分类表';

-- ----------------------------
-- Records of product_category
-- ----------------------------
INSERT INTO `product_category` VALUES ('22', '休闲零食', '袋', '休闲,成品,即食', '10', '1', '0');
INSERT INTO `product_category` VALUES ('23', '全文', '斤', '全文亲戚', '1221', '0', '1');
INSERT INTO `product_category` VALUES ('24', '酒类', '瓶', '白酒,红酒,啤酒', '20', '1', '0');
INSERT INTO `product_category` VALUES ('25', '汽车', '辆', '车,跑车,自行车', '50', '1', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '1', 'admin', '超级管理员', '$2y$10$3UjNyh8U.xQFefewnO5lkeVg0U7RUtwrEL8QCiFix5Y7ZV7h6tV8C', '-', '0', '1507789254');
INSERT INTO `users` VALUES ('17', '2', 'sada', 'adhui', '$2y$10$5IAUyq87mQxq0TS8Vq55JusanxapzqQpBxpPR1NOkCkfqKYSJqCcu', '15648944561', '1', '1508397676');
INSERT INTO `users` VALUES ('18', '2', '123456', 'asdnjkn12j', '$2y$10$HjXvfAxWO5lP05sHUOX/y.jMliyOAqaNnnBOpV02iyz0pjhBgVe4C', '15648944561', '0', '1508397738');
INSERT INTO `users` VALUES ('19', '2', 'dsdsf', 'ewqweq', '$2y$10$Bm1auWuIqDr0jVOB0FVtK.JXAZjod8rC12OZtNfilD7DqvWDIxIG6', '17600082920', '0', '1508397877');
INSERT INTO `users` VALUES ('20', '2', '45165', 'ninjii', '$2y$10$rXp.JuGdDDrT8vIo5GIUve5e5tzTArfw0h2Mip1WJVaRVfgV7cIa2', '15648974897', '0', '1508397918');
INSERT INTO `users` VALUES ('21', '2', 'qweqwe', 'weqewewq', '$2y$10$XAe0lzYSwNhx/XT.BtswYexLSSE.3/D24RSK7IZVmO8V0GZxx2e9K', '17600082920', '0', '1508398181');
INSERT INTO `users` VALUES ('22', '2', 'saddsad', 'asd', '$2y$10$IhsUQwF2e.4bHddAowF/p.SoBacIxGUt5x4lsiYPPaiwU0WHVRLTu', '17600082920', '0', '1508398245');
INSERT INTO `users` VALUES ('23', '2', 'sdf', 'zxc', '$2y$10$PPQ4GyJIUfnidxsPK0WQuOsMLAl4JaoFAltYAwmoE69FNlc2kATYu', '17600082920', '1', '1508398375');
INSERT INTO `users` VALUES ('24', '2', 'asd', 'zxc', '$2y$10$YGfpcuqJ7RNcs6G5McS31e0CmMV2EWia7QBv8POd96t8oVGQ378qO', '17600082920', '0', '1508398497');
INSERT INTO `users` VALUES ('25', '2', 'qwe', 'asdqq', '$2y$10$Ur/SIp2VgPjfVx.jZylQyOliD3uAmoECpzcIkHezVS9qnVs1bD8Du', '17600082920', '0', '1508398893');
INSERT INTO `users` VALUES ('26', '2', '156489', 'asdfsdf', '$2y$10$.mGg3UaNVQQHvqlMW9Oqfevd3tiJPxSFzlNpRfvYpVvN9WRvs3wIW', '15648974897', '0', '1508400137');
INSERT INTO `users` VALUES ('27', '2', 'asdsad', 'fdsfddfdf', '$2y$10$UFjmDCZZXyUD8tNzWFI57eXTBC3UUthDmGBJS86mrLKa8StqBydom', '17600082920', '0', '1508401994');
INSERT INTO `users` VALUES ('38', '4', 'walker001', 'walker', '$2y$10$3UjNyh8U.xQFefewnO5lkeVg0U7RUtwrEL8QCiFix5Y7ZV7h6tV8C', '18600982820', '0', '1508823353');
INSERT INTO `users` VALUES ('39', '3', 'supplier_001', '供货商_001', '$2y$10$NXtrY/IkzUwRyqWbrMIKNeADp0G4500EhdbGCOSDGJGMtpmpZhgTG', '18600982820', '0', '1508901166');
INSERT INTO `users` VALUES ('40', '3', 'supplier_002', '供货商_002', '$2y$10$nifvN3P7szPpizFWdNQFt.tCPxpB3wdBVPlFjoWvzsjhEVNDT8hGO', '15648944561', '0', '1508901194');
INSERT INTO `users` VALUES ('41', '3', 'supplier_003', '供货商_003', '$2y$10$Ig4Yx2pg3S6aKF7dtj5q6eBK9zfPWluzS1t93SJrh1QaihLpDByLO', '15648944561', '0', '1508901219');
INSERT INTO `users` VALUES ('42', '3', 'supplier_004', '供货商_004', '$2y$10$hjeWkF8uEjpTMjX0JgGyP.PmjyG36l5XAhxulWUz56Wi.sjeidWQG', '15678947894', '0', '1508901242');
INSERT INTO `users` VALUES ('43', '3', 'supplier_005', '供货商_005', '$2y$10$oNe59AWQDF0QzNQcsLgtJOg.JMGxyOl1qqO6Hys1fdYolxmCZKzyS', '15678947894', '0', '1508901258');
INSERT INTO `users` VALUES ('44', '1', 'guest', '临时管理员', '$2y$10$3UjNyh8U.xQFefewnO5lkeVg0U7RUtwrEL8QCiFix5Y7ZV7h6tV8C', '', '0', '1507789254');

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
) ENGINE=InnoDB AUTO_INCREMENT=374 DEFAULT CHARSET=utf8 COMMENT='用户操作日志表';

-- ----------------------------
-- Records of user_log
-- ----------------------------
INSERT INTO `user_log` VALUES ('15', '1', '用户管理-用户修改 >> 平台运营员: E-2017-10-13 11:23:24(A-2017-10-12 17:00:45)', '1507865005', '127.0.0.1');
INSERT INTO `user_log` VALUES ('16', '1', '用户管理-用户修改 >> 平台运营员: E-2017-10-13 11:23:25(A-2017-10-12 17:00:45)', '1507865005', '127.0.0.1');
INSERT INTO `user_log` VALUES ('17', '1', '用户管理-用户修改 >> 平台运营员: E-2017-10-13 11:23:31(A-2017-10-12 17:00:45)', '1507865011', '127.0.0.1');
INSERT INTO `user_log` VALUES ('18', '1', '用户管理-用户修改 >> 平台运营员: E-2017-10-13 11:23:55(A-2017-10-12 17:00:45)', '1507865035', '127.0.0.1');
INSERT INTO `user_log` VALUES ('19', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:36:51(A-2017-10-12 17:00:45)', '1507865811', '127.0.0.1');
INSERT INTO `user_log` VALUES ('20', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:36:52(A-2017-10-12 17:00:45)', '1507865812', '127.0.0.1');
INSERT INTO `user_log` VALUES ('21', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:39:15(A-2017-10-12 17:00:45)', '1507865955', '127.0.0.1');
INSERT INTO `user_log` VALUES ('22', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:39:53(A-2017-10-12 17:00:45)', '1507865993', '127.0.0.1');
INSERT INTO `user_log` VALUES ('23', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:41:11(A-2017-10-12 17:00:45)', '1507866071', '127.0.0.1');
INSERT INTO `user_log` VALUES ('24', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:43:45(A-2017-10-12 17:00:45)', '1507866225', '127.0.0.1');
INSERT INTO `user_log` VALUES ('25', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:44:35(A-2017-10-12 17:00:45)', '1507866276', '127.0.0.1');
INSERT INTO `user_log` VALUES ('26', '1', '用户管理-用户修改 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507866276', '127.0.0.1');
INSERT INTO `user_log` VALUES ('27', '1', '用户管理-用户禁用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873919', '127.0.0.1');
INSERT INTO `user_log` VALUES ('28', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873922', '127.0.0.1');
INSERT INTO `user_log` VALUES ('29', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873923', '127.0.0.1');
INSERT INTO `user_log` VALUES ('30', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873923', '127.0.0.1');
INSERT INTO `user_log` VALUES ('31', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873924', '127.0.0.1');
INSERT INTO `user_log` VALUES ('32', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873924', '127.0.0.1');
INSERT INTO `user_log` VALUES ('33', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873924', '127.0.0.1');
INSERT INTO `user_log` VALUES ('34', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507873945', '127.0.0.1');
INSERT INTO `user_log` VALUES ('35', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507874083', '127.0.0.1');
INSERT INTO `user_log` VALUES ('36', '1', '用户管理-用户禁用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507874085', '127.0.0.1');
INSERT INTO `user_log` VALUES ('37', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507874091', '127.0.0.1');
INSERT INTO `user_log` VALUES ('38', '1', '用户管理-用户禁用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507874094', '127.0.0.1');
INSERT INTO `user_log` VALUES ('39', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507874095', '127.0.0.1');
INSERT INTO `user_log` VALUES ('40', '1', '用户管理-用户禁用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507874098', '127.0.0.1');
INSERT INTO `user_log` VALUES ('41', '1', '用户管理-用户启用 >> 平台运营员: N-2017-10-13 11:44:36(A-2017-10-12 17:00:45)', '1507874099', '127.0.0.1');
INSERT INTO `user_log` VALUES ('42', '1', '商品管理-新增商品分类 >> 蔬菜(计量单位:斤)', '1508121762', '127.0.0.1');
INSERT INTO `user_log` VALUES ('43', '1', '商品管理-新增商品分类 >> 水果(计量单位:个)', '1508121782', '127.0.0.1');
INSERT INTO `user_log` VALUES ('44', '1', '商品管理-新增商品分类 >> 水果(计量单位:个)', '1508121786', '127.0.0.1');
INSERT INTO `user_log` VALUES ('45', '1', '商品管理-新增商品分类 >> 水果(计量单位:个)', '1508121786', '127.0.0.1');
INSERT INTO `user_log` VALUES ('46', '1', '商品管理-新增商品分类 >> 蔬菜22(计量单位:斤)', '1508121797', '127.0.0.1');
INSERT INTO `user_log` VALUES ('47', '1', '商品管理-新增商品分类 >> 蔬菜22(计量单位:斤)', '1508121798', '127.0.0.1');
INSERT INTO `user_log` VALUES ('48', '1', '商品管理-新增商品分类 >> 饮料(计量单位:瓶)', '1508121823', '127.0.0.1');
INSERT INTO `user_log` VALUES ('49', '1', '商品管理-新增商品分类 >> 饮料(计量单位:瓶)', '1508121824', '127.0.0.1');
INSERT INTO `user_log` VALUES ('50', '1', '商品管理-新增商品分类 >> 饮料33(计量单位:瓶)', '1508121830', '127.0.0.1');
INSERT INTO `user_log` VALUES ('51', '1', '商品管理-新增商品分类 >> 饮料33(计量单位:瓶)', '1508121830', '127.0.0.1');
INSERT INTO `user_log` VALUES ('52', '1', '军方-修改军方需求 >> AA(20 个)', '1508287429', '127.0.0.1');
INSERT INTO `user_log` VALUES ('53', '1', '军方-修改军方需求 >> AA(20 个)', '1508288148', '127.0.0.1');
INSERT INTO `user_log` VALUES ('54', '1', '军方-修改军方需求 >> AA(20 个)', '1508288155', '127.0.0.1');
INSERT INTO `user_log` VALUES ('55', '1', '军方-修改军方需求 >> AA(20 个)', '1508288291', '127.0.0.1');
INSERT INTO `user_log` VALUES ('56', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:02(53 斤)', '1508380142', '127.0.0.1');
INSERT INTO `user_log` VALUES ('57', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:03(42 斤)', '1508380143', '127.0.0.1');
INSERT INTO `user_log` VALUES ('58', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:03(196 斤)', '1508380143', '127.0.0.1');
INSERT INTO `user_log` VALUES ('59', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:04(51 斤)', '1508380144', '127.0.0.1');
INSERT INTO `user_log` VALUES ('60', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:25(188 斤)', '1508380165', '127.0.0.1');
INSERT INTO `user_log` VALUES ('61', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:25(33 斤)', '1508380165', '127.0.0.1');
INSERT INTO `user_log` VALUES ('62', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:25(166 斤)', '1508380165', '127.0.0.1');
INSERT INTO `user_log` VALUES ('63', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:25(155 斤)', '1508380165', '127.0.0.1');
INSERT INTO `user_log` VALUES ('64', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:25(133 斤)', '1508380165', '127.0.0.1');
INSERT INTO `user_log` VALUES ('65', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:26(116 斤)', '1508380166', '127.0.0.1');
INSERT INTO `user_log` VALUES ('66', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:26(58 斤)', '1508380166', '127.0.0.1');
INSERT INTO `user_log` VALUES ('67', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:26(138 斤)', '1508380166', '127.0.0.1');
INSERT INTO `user_log` VALUES ('68', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:26(139 斤)', '1508380166', '127.0.0.1');
INSERT INTO `user_log` VALUES ('69', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:26(37 斤)', '1508380166', '127.0.0.1');
INSERT INTO `user_log` VALUES ('70', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:27(63 斤)', '1508380167', '127.0.0.1');
INSERT INTO `user_log` VALUES ('71', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:27(73 斤)', '1508380167', '127.0.0.1');
INSERT INTO `user_log` VALUES ('72', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:27(153 斤)', '1508380167', '127.0.0.1');
INSERT INTO `user_log` VALUES ('73', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:27(157 斤)', '1508380167', '127.0.0.1');
INSERT INTO `user_log` VALUES ('74', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:27(184 斤)', '1508380167', '127.0.0.1');
INSERT INTO `user_log` VALUES ('75', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:27(162 斤)', '1508380167', '127.0.0.1');
INSERT INTO `user_log` VALUES ('76', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:28(18 斤)', '1508380168', '127.0.0.1');
INSERT INTO `user_log` VALUES ('77', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:28(78 斤)', '1508380168', '127.0.0.1');
INSERT INTO `user_log` VALUES ('78', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:28(120 斤)', '1508380168', '127.0.0.1');
INSERT INTO `user_log` VALUES ('79', '1', '军方-发布军方需求 >> A-2017-10-19 10:29:29(125 斤)', '1508380169', '127.0.0.1');
INSERT INTO `user_log` VALUES ('80', '1', '用户管理-新增用户 >> 平台运营员: adhui(sada)', '1508397676', '127.0.0.1');
INSERT INTO `user_log` VALUES ('81', '1', '用户管理-新增用户 >> 平台运营员: asdnjkn12j(123456)', '1508397738', '127.0.0.1');
INSERT INTO `user_log` VALUES ('82', '1', '用户管理-新增用户 >> 平台运营员: ewqweq(dsdsf)', '1508397877', '127.0.0.1');
INSERT INTO `user_log` VALUES ('83', '1', '用户管理-新增用户 >> 平台运营员: ninjii(45165)', '1508397918', '127.0.0.1');
INSERT INTO `user_log` VALUES ('84', '1', '用户管理-新增用户 >> 平台运营员: weqewewq(qweqwe)', '1508398181', '127.0.0.1');
INSERT INTO `user_log` VALUES ('85', '1', '用户管理-新增用户 >> 平台运营员: asd(saddsad)', '1508398245', '127.0.0.1');
INSERT INTO `user_log` VALUES ('86', '1', '用户管理-新增用户 >> 平台运营员: zxc(sdf)', '1508398375', '127.0.0.1');
INSERT INTO `user_log` VALUES ('87', '1', '用户管理-新增用户 >> 平台运营员: zxc(asd)', '1508398497', '127.0.0.1');
INSERT INTO `user_log` VALUES ('88', '1', '用户管理-新增用户 >> 平台运营员: asdqq(qwe)', '1508398893', '127.0.0.1');
INSERT INTO `user_log` VALUES ('89', '1', '用户管理-新增用户 >> 平台运营员: asdfsdf(156489)', '1508400137', '127.0.0.1');
INSERT INTO `user_log` VALUES ('90', '1', '用户管理-新增用户 >> 平台运营员: fdsfddfdf(asdsad)', '1508401994', '127.0.0.1');
INSERT INTO `user_log` VALUES ('91', '1', '用户管理-新增用户 >> 平台运营员: asd(fg)', '1508402066', '127.0.0.1');
INSERT INTO `user_log` VALUES ('92', '1', '用户管理-新增用户 >> 平台运营员: qwe(dfg)', '1508402119', '127.0.0.1');
INSERT INTO `user_log` VALUES ('93', '1', '用户管理-新增用户 >> 平台运营员: asd(sasdasad)', '1508402935', '127.0.0.1');
INSERT INTO `user_log` VALUES ('94', '1', '用户管理-新增用户 >> 平台运营员: qwewqe(zcx)', '1508403023', '127.0.0.1');
INSERT INTO `user_log` VALUES ('95', '1', '用户管理-新增用户 >> 平台运营员: qwewqe(vvvb)', '1508403111', '127.0.0.1');
INSERT INTO `user_log` VALUES ('96', '1', '用户管理-新增用户 >> 平台运营员: admin(sdasdads)', '1508461476', '127.0.0.1');
INSERT INTO `user_log` VALUES ('97', '1', '用户管理-新增用户 >> 平台运营员: admin(ewqwqewqeewq)', '1508461506', '127.0.0.1');
INSERT INTO `user_log` VALUES ('98', '1', '用户管理-新增用户 >> 平台运营员: admin(sadds)', '1508461932', '127.0.0.1');
INSERT INTO `user_log` VALUES ('99', '1', '用户管理-新增用户 >> 平台运营员: admin(dsasaddsa)', '1508461963', '127.0.0.1');
INSERT INTO `user_log` VALUES ('100', '1', '用户管理-新增用户 >> 平台运营员: admin(dsdwd)', '1508461988', '127.0.0.1');
INSERT INTO `user_log` VALUES ('101', '1', '用户管理-禁用用户 >> 平台运营员: admin(ewqwqewqeewq)', '1508486711', '127.0.0.1');
INSERT INTO `user_log` VALUES ('102', '1', '用户管理-禁用用户 >> 平台运营员: qwewqe(vvvb)', '1508486854', '127.0.0.1');
INSERT INTO `user_log` VALUES ('103', '1', '用户管理-禁用用户 >> 平台运营员: admin(sdasdads)', '1508486861', '127.0.0.1');
INSERT INTO `user_log` VALUES ('104', '1', '用户管理-禁用用户 >> 平台运营员: asd(fg)', '1508486928', '127.0.0.1');
INSERT INTO `user_log` VALUES ('105', '1', '用户管理-禁用用户 >> 平台运营员: qwe(dfg)', '1508486942', '127.0.0.1');
INSERT INTO `user_log` VALUES ('106', '1', '用户管理-禁用用户 >> 平台运营员: asd(sasdasad)', '1508486982', '127.0.0.1');
INSERT INTO `user_log` VALUES ('107', '1', '用户管理-禁用用户 >> 平台运营员: qwewqe(zcx)', '1508488114', '127.0.0.1');
INSERT INTO `user_log` VALUES ('108', '1', '用户管理-禁用用户 >> 平台运营员: fdsfddfdf(asdsad)', '1508488157', '127.0.0.1');
INSERT INTO `user_log` VALUES ('109', '1', '用户管理-禁用用户 >> 平台运营员: asdfsdf(156489)', '1508488160', '127.0.0.1');
INSERT INTO `user_log` VALUES ('110', '1', '用户管理-禁用用户 >> 平台运营员: adhui(sada)', '1508488217', '127.0.0.1');
INSERT INTO `user_log` VALUES ('111', '1', '用户管理-禁用用户 >> 平台运营员: asdqq(qwe)', '1508489187', '127.0.0.1');
INSERT INTO `user_log` VALUES ('112', '1', '用户管理-禁用用户 >> 平台运营员: zxc(sdf)', '1508489191', '127.0.0.1');
INSERT INTO `user_log` VALUES ('113', '1', '用户管理-启用用户 >> 平台运营员: admin(dsdwd)', '1508489370', '127.0.0.1');
INSERT INTO `user_log` VALUES ('114', '1', '用户管理-启用用户 >> 平台运营员: admin(sadds)', '1508489385', '127.0.0.1');
INSERT INTO `user_log` VALUES ('115', '1', '用户管理-禁用用户 >> 平台运营员: admin(dsdwd)', '1508489386', '127.0.0.1');
INSERT INTO `user_log` VALUES ('116', '1', '用户管理-启用用户 >> 平台运营员: admin(dsasaddsa)', '1508489387', '127.0.0.1');
INSERT INTO `user_log` VALUES ('117', '1', '用户管理-启用用户 >> 平台运营员: admin(sdasdads)', '1508489394', '127.0.0.1');
INSERT INTO `user_log` VALUES ('118', '1', '用户管理-启用用户 >> 平台运营员: admin(ewqwqewqeewq)', '1508489395', '127.0.0.1');
INSERT INTO `user_log` VALUES ('119', '1', '用户管理-启用用户 >> 平台运营员: qwewqe(vvvb)', '1508489397', '127.0.0.1');
INSERT INTO `user_log` VALUES ('120', '1', '用户管理-禁用用户 >> 平台运营员: admin(sadds)', '1508717626', '127.0.0.1');
INSERT INTO `user_log` VALUES ('121', '1', '用户管理-禁用用户 >> 平台运营员: admin(dsasaddsa)', '1508717629', '127.0.0.1');
INSERT INTO `user_log` VALUES ('122', '1', '用户管理-启用用户 >> 平台运营员: admin(dsdwd)', '1508717632', '127.0.0.1');
INSERT INTO `user_log` VALUES ('123', '1', '用户管理-启用用户 >> 平台运营员: qwe(dfg)', '1508717635', '127.0.0.1');
INSERT INTO `user_log` VALUES ('124', '1', '用户管理-修改用户 >> 平台运营员: admin(sadds)', '1508726614', '127.0.0.1');
INSERT INTO `user_log` VALUES ('125', '1', '用户管理-修改用户 >> 平台运营员: admin(sadds)', '1508726663', '127.0.0.1');
INSERT INTO `user_log` VALUES ('126', '1', '用户管理-修改用户 >> 平台运营员: admin(dsdwd)', '1508738494', '127.0.0.1');
INSERT INTO `user_log` VALUES ('127', '1', '商品管理-新增商品分类 >> qwe(计量单位:qwe)', '1508743220', '127.0.0.1');
INSERT INTO `user_log` VALUES ('128', '1', '商品管理-新增商品分类 >> 84949(计量单位:7874)', '1508743580', '127.0.0.1');
INSERT INTO `user_log` VALUES ('129', '1', '商品管理-新增商品分类 >> 44(计量单位:545454)', '1508743613', '127.0.0.1');
INSERT INTO `user_log` VALUES ('130', '1', '商品管理-新增商品分类 >> asd(计量单位:qw)', '1508746809', '127.0.0.1');
INSERT INTO `user_log` VALUES ('131', '1', '商品管理-新增商品分类 >> qe(计量单位:23)', '1508746946', '127.0.0.1');
INSERT INTO `user_log` VALUES ('132', '1', '商品管理-新增商品分类 >> 啊啊(计量单位:阿)', '1508747219', '127.0.0.1');
INSERT INTO `user_log` VALUES ('133', '1', '用户管理-禁用用户 >> 平台运营员: admin(dsdwd)', '1508747449', '127.0.0.1');
INSERT INTO `user_log` VALUES ('134', '1', '用户管理-禁用用户 >> 平台运营员: admin(ewqwqewqeewq)', '1508747458', '127.0.0.1');
INSERT INTO `user_log` VALUES ('135', '1', '用户管理-启用用户 >> 平台运营员: admin(sadds)', '1508747462', '127.0.0.1');
INSERT INTO `user_log` VALUES ('136', '1', '用户管理-启用用户 >> 平台运营员: admin(dsasaddsa)', '1508747467', '127.0.0.1');
INSERT INTO `user_log` VALUES ('137', '1', '商品管理-修改商品分类 >> 蔬菜22(计量单位:斤)', '1508747866', '127.0.0.1');
INSERT INTO `user_log` VALUES ('138', '1', '商品管理-修改商品分类 >> 蔬菜22(计量单位:斤)', '1508747889', '127.0.0.1');
INSERT INTO `user_log` VALUES ('139', '1', '商品管理-删除商品分类 >> 饮料33(计量单位:瓶)', '1508749616', '127.0.0.1');
INSERT INTO `user_log` VALUES ('140', '1', '商品管理-删除商品分类 >> 蔬菜22(计量单位:斤)', '1508749627', '127.0.0.1');
INSERT INTO `user_log` VALUES ('141', '1', '商品管理-删除商品分类 >> 水果(计量单位:个)', '1508749860', '127.0.0.1');
INSERT INTO `user_log` VALUES ('142', '1', '用户管理-新增用户 >> 平台运营员: asd(walker001)', '1508823353', '127.0.0.1');
INSERT INTO `user_log` VALUES ('143', '1', '用户管理-启用用户 >> 平台运营员: asd(fg)', '1508824674', '127.0.0.1');
INSERT INTO `user_log` VALUES ('144', '1', '用户管理-启用用户 >> 平台运营员: fdsfddfdf(asdsad)', '1508824678', '127.0.0.1');
INSERT INTO `user_log` VALUES ('145', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508834589', '127.0.0.1');
INSERT INTO `user_log` VALUES ('146', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508834638', '127.0.0.1');
INSERT INTO `user_log` VALUES ('147', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508834730', '127.0.0.1');
INSERT INTO `user_log` VALUES ('148', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508834838', '127.0.0.1');
INSERT INTO `user_log` VALUES ('149', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508834984', '127.0.0.1');
INSERT INTO `user_log` VALUES ('150', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508835115', '127.0.0.1');
INSERT INTO `user_log` VALUES ('151', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508835206', '127.0.0.1');
INSERT INTO `user_log` VALUES ('152', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508835266', '127.0.0.1');
INSERT INTO `user_log` VALUES ('153', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508835501', '127.0.0.1');
INSERT INTO `user_log` VALUES ('154', '27', '用户中心-修改密码 >> 使用原密码方式修改密码', '1508835867', '127.0.0.1');
INSERT INTO `user_log` VALUES ('155', '1', '用户管理-新增用户 >> 供货商: 供货商_001(supplier_001)', '1508901166', '127.0.0.1');
INSERT INTO `user_log` VALUES ('156', '1', '用户管理-新增用户 >> 供货商: 供货商_002(supplier_002)', '1508901194', '127.0.0.1');
INSERT INTO `user_log` VALUES ('157', '1', '用户管理-新增用户 >> 供货商: 供货商_003(supplier_003)', '1508901219', '127.0.0.1');
INSERT INTO `user_log` VALUES ('158', '1', '用户管理-新增用户 >> 供货商: 供货商_004(supplier_004)', '1508901242', '127.0.0.1');
INSERT INTO `user_log` VALUES ('159', '1', '用户管理-新增用户 >> 供货商: 供货商_005(supplier_005)', '1508901258', '127.0.0.1');
INSERT INTO `user_log` VALUES ('160', '27', '商品管理-新增商品 >> qqwe(商品分类:饮料33)', '1508919591', '127.0.0.1');
INSERT INTO `user_log` VALUES ('161', '27', '商品管理-新增商品 >> qqqqqqqqqqqqq(商品分类:饮料)', '1508919758', '127.0.0.1');
INSERT INTO `user_log` VALUES ('162', '27', '商品管理-新增商品 >> wqewe(商品分类:饮料33)', '1508919832', '127.0.0.1');
INSERT INTO `user_log` VALUES ('163', '27', '商品管理-删除商品 >> aa(商品分类:水果)', '1508982062', '127.0.0.1');
INSERT INTO `user_log` VALUES ('164', '27', '商品管理-删除商品 >> bb(商品分类:水果)', '1508982195', '127.0.0.1');
INSERT INTO `user_log` VALUES ('165', '27', '商品管理-删除商品 >> dd(商品分类:蔬菜22)', '1508982198', '127.0.0.1');
INSERT INTO `user_log` VALUES ('166', '27', '商品管理-删除商品 >> cc(商品分类:蔬菜22)', '1508982209', '127.0.0.1');
INSERT INTO `user_log` VALUES ('167', '27', '商品管理-删除商品 >> tt(商品分类:水果)', '1508982212', '127.0.0.1');
INSERT INTO `user_log` VALUES ('168', '27', '商品管理-删除商品 >> qq(商品分类:水果)', '1508982279', '127.0.0.1');
INSERT INTO `user_log` VALUES ('169', '27', '商品管理-删除商品 >> ee(商品分类:蔬菜22)', '1508982297', '127.0.0.1');
INSERT INTO `user_log` VALUES ('170', '27', '商品管理-新增商品 >> iiiii(商品分类:饮料33)', '1508982324', '127.0.0.1');
INSERT INTO `user_log` VALUES ('171', '27', '商品管理-删除商品 >> wqewe(商品分类:饮料33)', '1508982341', '127.0.0.1');
INSERT INTO `user_log` VALUES ('172', '27', '商品管理-新增商品 >> eeeeee(商品分类:饮料33)', '1508982482', '127.0.0.1');
INSERT INTO `user_log` VALUES ('173', '27', '商品管理-新增商品分类 >> 全文(计量单位:qwewqe)', '1508982530', '127.0.0.1');
INSERT INTO `user_log` VALUES ('174', '27', '商品管理-新增商品分类 >> 全文(计量单位:qqwe)', '1508982612', '127.0.0.1');
INSERT INTO `user_log` VALUES ('175', '27', '商品管理-新增商品 >> 温热(商品分类:饮料33)', '1508984711', '127.0.0.1');
INSERT INTO `user_log` VALUES ('176', '27', '商品管理-新增商品 >> 企鹅王若(商品分类:饮料33)', '1508984725', '127.0.0.1');
INSERT INTO `user_log` VALUES ('177', '27', '商品管理-新增商品 >> 确认翁(商品分类:啊啊)', '1508984741', '127.0.0.1');
INSERT INTO `user_log` VALUES ('178', '27', '商品管理-新增商品 >> 企鹅王若(商品分类:饮料33)', '1508984752', '127.0.0.1');
INSERT INTO `user_log` VALUES ('179', '27', '商品管理-新增商品 >> 全文(商品分类:饮料33)', '1508984771', '127.0.0.1');
INSERT INTO `user_log` VALUES ('180', '27', '商品管理-新增商品 >> 全文(商品分类:饮料33)', '1508984783', '127.0.0.1');
INSERT INTO `user_log` VALUES ('181', '27', '商品管理-新增商品 >> 全文(商品分类:饮料33)', '1508984801', '127.0.0.1');
INSERT INTO `user_log` VALUES ('182', '27', '商品管理-新增商品 >> qeqw(商品分类:饮料33)', '1508985897', '127.0.0.1');
INSERT INTO `user_log` VALUES ('183', '27', '商品管理-新增商品 >> 王者(商品分类:饮料33)', '1508986046', '127.0.0.1');
INSERT INTO `user_log` VALUES ('184', '27', '商品管理-删除商品 >> qqwe(商品分类:饮料33)', '1508987832', '127.0.0.1');
INSERT INTO `user_log` VALUES ('185', '27', '商品管理-删除商品 >> qqqqqqqqqqqqq(商品分类:饮料)', '1508987835', '127.0.0.1');
INSERT INTO `user_log` VALUES ('186', '27', '商品管理-删除商品 >> iiiii(商品分类:饮料33)', '1508987841', '127.0.0.1');
INSERT INTO `user_log` VALUES ('187', '27', '商品管理-删除商品 >> eeeeee(商品分类:饮料33)', '1508987844', '127.0.0.1');
INSERT INTO `user_log` VALUES ('188', '1', '平台-平台发布需求 >> 橘子(783 个) 供应商:41,40', '1508987905', '127.0.0.1');
INSERT INTO `user_log` VALUES ('189', '27', '商品管理-删除商品 >> 温热(商品分类:饮料33)', '1508987894', '127.0.0.1');
INSERT INTO `user_log` VALUES ('190', '1', '平台-平台发布需求 >> 橘子(394 个) 供应商:41,40', '1508987931', '127.0.0.1');
INSERT INTO `user_log` VALUES ('191', '1', '平台-平台发布需求 >> 橘子(170 个) 供应商:41,40', '1508987969', '127.0.0.1');
INSERT INTO `user_log` VALUES ('192', '1', '平台-平台发布需求 >> 橘子(242 个) 供应商:41,40', '1508989136', '127.0.0.1');
INSERT INTO `user_log` VALUES ('193', '1', '平台-平台发布需求 >> 橘子(804 个) 供应商:41,40', '1508989277', '127.0.0.1');
INSERT INTO `user_log` VALUES ('194', '1', '平台-平台发布需求 >> 橘子(850 个) 供应商:42,40,41', '1508989305', '127.0.0.1');
INSERT INTO `user_log` VALUES ('195', '27', '商品管理-新增商品 >> qweewq(商品分类:饮料33)', '1508989371', '127.0.0.1');
INSERT INTO `user_log` VALUES ('196', '27', '商品管理-新增商品 >> wqeew(商品分类:全文)', '1508989428', '127.0.0.1');
INSERT INTO `user_log` VALUES ('197', '1', '平台-平台发布需求 >> 橘子(526 个) 供应商:供货商_002,供货商_003,供货商_004', '1508997337', '127.0.0.1');
INSERT INTO `user_log` VALUES ('198', '1', '平台-平台发布需求 >> 橘子(777个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997383', '127.0.0.1');
INSERT INTO `user_log` VALUES ('199', '1', '平台-平台发布需求 >> 橘子(414个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997542', '127.0.0.1');
INSERT INTO `user_log` VALUES ('200', '1', '平台-平台发布需求 >> 橘子(95个) 供应商: 供货商_002', '1508997550', '127.0.0.1');
INSERT INTO `user_log` VALUES ('201', '1', '平台-平台发布需求 >> 橘子(595个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997600', '127.0.0.1');
INSERT INTO `user_log` VALUES ('202', '1', '平台-平台发布需求 >> 橘子(366个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997600', '127.0.0.1');
INSERT INTO `user_log` VALUES ('203', '1', '平台-平台发布需求 >> 橘子(297个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997601', '127.0.0.1');
INSERT INTO `user_log` VALUES ('204', '1', '平台-平台发布需求 >> 橘子(430个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997601', '127.0.0.1');
INSERT INTO `user_log` VALUES ('205', '1', '平台-平台发布需求 >> 橘子(202个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997651', '127.0.0.1');
INSERT INTO `user_log` VALUES ('206', '1', '平台-平台发布需求 >> 苹果(691个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997657', '127.0.0.1');
INSERT INTO `user_log` VALUES ('207', '1', '平台-平台发布需求 >> 梨子(535个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997663', '127.0.0.1');
INSERT INTO `user_log` VALUES ('208', '1', '平台-平台发布需求 >> 可乐(666个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997674', '127.0.0.1');
INSERT INTO `user_log` VALUES ('209', '1', '商品管理-新增商品 >> qqqqq(商品分类:饮料33)', '1508997662', '127.0.0.1');
INSERT INTO `user_log` VALUES ('210', '1', '平台-平台发布需求 >> 商品167(732个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997697', '127.0.0.1');
INSERT INTO `user_log` VALUES ('211', '1', '平台-平台发布需求 >> 商品262(758个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997697', '127.0.0.1');
INSERT INTO `user_log` VALUES ('212', '1', '平台-平台发布需求 >> 商品695(102个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997698', '127.0.0.1');
INSERT INTO `user_log` VALUES ('213', '1', '平台-平台发布需求 >> 商品913(143个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997698', '127.0.0.1');
INSERT INTO `user_log` VALUES ('214', '1', '平台-平台发布需求 >> 蔬菜670(660个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997712', '127.0.0.1');
INSERT INTO `user_log` VALUES ('215', '1', '平台-平台发布需求 >> 蔬菜785(22个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997712', '127.0.0.1');
INSERT INTO `user_log` VALUES ('216', '1', '平台-平台发布需求 >> 蔬菜43(774个) 供应商: 供货商_002,供货商_003,供货商_004', '1508997712', '127.0.0.1');
INSERT INTO `user_log` VALUES ('217', '1', '商品管理-删除商品 >> wqeew(商品分类:全文)', '1508997848', '127.0.0.1');
INSERT INTO `user_log` VALUES ('218', '1', '军方-军方发布需求 >> 军方需求73(203 个)', '1508997926', '127.0.0.1');
INSERT INTO `user_log` VALUES ('219', '1', '军方-军方发布需求 >> 军方需求62(206 个)', '1508997928', '127.0.0.1');
INSERT INTO `user_log` VALUES ('220', '1', '军方-军方发布需求 >> 军方需求45(149 个)', '1508997929', '127.0.0.1');
INSERT INTO `user_log` VALUES ('221', '1', '军方-军方发布需求 >> 军方需求12(447 个)', '1508997932', '127.0.0.1');
INSERT INTO `user_log` VALUES ('222', '1', '军方-军方发布需求 >> 军方需求9(170 个)', '1508997933', '127.0.0.1');
INSERT INTO `user_log` VALUES ('223', '1', '商品管理-删除商品 >> qqqqq(商品分类:饮料33)', '1509002971', '127.0.0.1');
INSERT INTO `user_log` VALUES ('224', '1', '商品管理-删除商品 >> qweewq(商品分类:饮料33)', '1509002973', '127.0.0.1');
INSERT INTO `user_log` VALUES ('225', '1', '商品管理-新增商品 >> 企鹅王若(商品分类:饮料33)', '1509003047', '127.0.0.1');
INSERT INTO `user_log` VALUES ('226', '1', '商品管理-新增商品 >> 企鹅王若(商品分类:饮料33)', '1509003066', '127.0.0.1');
INSERT INTO `user_log` VALUES ('227', '1', '商品管理-新增商品 >> 企鹅王若(商品分类:饮料33)', '1509003090', '127.0.0.1');
INSERT INTO `user_log` VALUES ('228', '1', '商品管理-修改商品 >> 企鹅王若(商品分类:饮料33)', '1509003495', '127.0.0.1');
INSERT INTO `user_log` VALUES ('229', '1', '商品管理-修改商品 >> 企鹅王若(商品分类:饮料33)', '1509003546', '127.0.0.1');
INSERT INTO `user_log` VALUES ('230', '1', '商品管理-修改商品 >> 企鹅王若(商品分类:饮料33)', '1509003599', '127.0.0.1');
INSERT INTO `user_log` VALUES ('231', '1', '商品管理-新增商品 >> qweqw(商品分类:饮料33)', '1509006213', '127.0.0.1');
INSERT INTO `user_log` VALUES ('232', '1', '商品管理-新增商品 >> qweqw(商品分类:饮料33)', '1509006213', '127.0.0.1');
INSERT INTO `user_log` VALUES ('233', '1', '商品管理-修改商品 >> 企鹅王若(商品分类:饮料33)', '1509007030', '127.0.0.1');
INSERT INTO `user_log` VALUES ('234', '1', '平台-平台库存供应 >> 军方需求73(203个) 订单号: 20171026140681016511', '1509344068', '127.0.0.1');
INSERT INTO `user_log` VALUES ('235', '1', '平台-平台库存供应 >> 军方需求73(203个) 订单号: 20171026140681016511', '1509344118', '127.0.0.1');
INSERT INTO `user_log` VALUES ('236', '1', '平台-平台库存供应 >> 军方需求73(203个) 订单号: 20171026140681016511', '1509344124', '127.0.0.1');
INSERT INTO `user_log` VALUES ('237', '1', '平台-平台库存供应 >> 军方需求62(206个) 订单号: 20171026140766058642', '1509344152', '127.0.0.1');
INSERT INTO `user_log` VALUES ('238', '1', '平台-供应商分配 >> 军方需求45(149个) 供应商: 供货商_002,供货商_003,供货商_004', '1509355120', '127.0.0.1');
INSERT INTO `user_log` VALUES ('239', '1', '平台-供应商分配 >> 军方需求12(447个) 供应商: 供货商_002,供货商_003,供货商_004', '1509355223', '127.0.0.1');
INSERT INTO `user_log` VALUES ('240', '1', '平台-供应商选择 >> 军方需求45(149个) 选择供应商: 供货商_003', '1509357353', '127.0.0.1');
INSERT INTO `user_log` VALUES ('241', '1', '军方-发布需求 >> sad(1 瓶)', '1509415110', '127.0.0.1');
INSERT INTO `user_log` VALUES ('242', '1', '军方-发布需求 >> 12(12 瓶)', '1509415374', '127.0.0.1');
INSERT INTO `user_log` VALUES ('243', '1', '军方-发布需求 >> 12(122121 瓶)', '1509415448', '127.0.0.1');
INSERT INTO `user_log` VALUES ('244', '1', '军方-发布需求 >> 12(123 瓶)', '1509415579', '127.0.0.1');
INSERT INTO `user_log` VALUES ('245', '1', '军方-发布需求 >> 21(12 瓶)', '1509415684', '127.0.0.1');
INSERT INTO `user_log` VALUES ('246', '1', '军方-发布需求 >> 12(21 瓶)', '1509415953', '127.0.0.1');
INSERT INTO `user_log` VALUES ('247', '1', '军方-发布需求 >> 诶我去二二(12 斤)', '1509429912', '127.0.0.1');
INSERT INTO `user_log` VALUES ('248', '1', '军方-修改需求 >> 啊啊啊(13 斤)', '1509431439', '127.0.0.1');
INSERT INTO `user_log` VALUES ('249', '1', '军方-修改需求 >> 啊啊啊(131 斤)', '1509431517', '127.0.0.1');
INSERT INTO `user_log` VALUES ('250', '1', '军方-删除需求 >> 啊啊啊(131 斤)', '1509500097', '127.0.0.1');
INSERT INTO `user_log` VALUES ('251', '1', '军方-删除需求 >> 12(21 瓶)', '1509500134', '127.0.0.1');
INSERT INTO `user_log` VALUES ('252', '1', '军方-删除需求 >> 21(12 瓶)', '1509500179', '127.0.0.1');
INSERT INTO `user_log` VALUES ('253', '1', '军方-删除需求 >> 12(122121 瓶)', '1509500189', '127.0.0.1');
INSERT INTO `user_log` VALUES ('254', '1', '军方-删除需求 >> 12(123 瓶)', '1509500208', '127.0.0.1');
INSERT INTO `user_log` VALUES ('255', '1', '军方-删除需求 >> 12(12 瓶)', '1509500295', '127.0.0.1');
INSERT INTO `user_log` VALUES ('256', '1', '军方-发布需求 >> 韦尔奇无(1 瓶)', '1509500312', '127.0.0.1');
INSERT INTO `user_log` VALUES ('257', '1', '军方-发布需求 >> 韦尔奇无(1 瓶)', '1509500313', '127.0.0.1');
INSERT INTO `user_log` VALUES ('258', '1', '军方-发布需求 >> 12(12 瓶)', '1509500326', '127.0.0.1');
INSERT INTO `user_log` VALUES ('259', '1', '军方-发布需求 >> 12(12 瓶)', '1509500334', '127.0.0.1');
INSERT INTO `user_log` VALUES ('260', '1', '军方-发布需求 >> 12(12 瓶)', '1509500341', '127.0.0.1');
INSERT INTO `user_log` VALUES ('261', '1', '军方-发布需求 >> 12(12 瓶)', '1509500350', '127.0.0.1');
INSERT INTO `user_log` VALUES ('262', '1', '军方-发布需求 >> 12(12 瓶)', '1509500361', '127.0.0.1');
INSERT INTO `user_log` VALUES ('263', '1', '军方-发布需求 >> 12(12 瓶)', '1509500361', '127.0.0.1');
INSERT INTO `user_log` VALUES ('264', '1', '军方-修改需求 >> 12(12 瓶)', '1509508592', '127.0.0.1');
INSERT INTO `user_log` VALUES ('265', '1', '平台-发布需求 >> 12(12斤) 供应商: 供货商_001', '1509528933', '127.0.0.1');
INSERT INTO `user_log` VALUES ('266', '1', '平台-分配供应商 >> 12(12瓶) 供应商: 供货商_001', '1509589735', '127.0.0.1');
INSERT INTO `user_log` VALUES ('267', '1', '平台-分配供应商 >> 12(12瓶) 供应商: 供货商_001', '1509589757', '127.0.0.1');
INSERT INTO `user_log` VALUES ('268', '40', '供应商-报价提交 >> 订单:(20171026141048002848 蔬菜43 774个) 报价: 供货商_002   11.3695 元', '1509602005', '127.0.0.1');
INSERT INTO `user_log` VALUES ('269', '1', '平台-选择供应商 >> 蔬菜43(774个) 选择供应商: 供货商_003', '1509611057', '127.0.0.1');
INSERT INTO `user_log` VALUES ('270', '1', '平台-发货到军方 >> 军方需求62(206个) 订单号: 20171026140766058642', '1509613920', '127.0.0.1');
INSERT INTO `user_log` VALUES ('271', '1', '平台-发货到军方 >> 军方需求9(170个) 订单号: 20171026141047075185', '1509614447', '127.0.0.1');
INSERT INTO `user_log` VALUES ('272', '1', '平台-供应商确认收货 >> 军方需求45(149个) 订单号: 20171026140557057849', '1509615292', '127.0.0.1');
INSERT INTO `user_log` VALUES ('273', '1', '平台-库存供应 >> 12(12瓶) 订单号: 20171101094214094428', '1509615518', '127.0.0.1');
INSERT INTO `user_log` VALUES ('274', '1', '平台-库存供应 >> 12(12瓶) 订单号: 20171101093915060010', '1509615524', '127.0.0.1');
INSERT INTO `user_log` VALUES ('275', '1', '平台-发货到军方 >> 12(12瓶) 订单号: 20171101093915060010', '1509615530', '127.0.0.1');
INSERT INTO `user_log` VALUES ('276', '1', '平台-发货到军方 >> 12(12瓶) 订单号: 20171101094214094428', '1509692665', '127.0.0.1');
INSERT INTO `user_log` VALUES ('277', '1', '平台-库存供应 >> 12(12瓶) 订单号: 20171101094333094370', '1509692671', '127.0.0.1');
INSERT INTO `user_log` VALUES ('278', '1', '平台-发货到军方 >> 军方需求45(149个) 订单号: 20171026140557057849', '1509692677', '127.0.0.1');
INSERT INTO `user_log` VALUES ('279', '1', '军方-删除需求 >> 韦尔奇无(1 瓶)', '1509692687', '127.0.0.1');
INSERT INTO `user_log` VALUES ('280', '1', '平台-发货到军方 >> 12(12瓶) 订单号: 20171101094333094370', '1509692729', '127.0.0.1');
INSERT INTO `user_log` VALUES ('281', '1', '平台-库存供应 >> 12(12瓶) 订单号: 20171101094608050546', '1509692849', '127.0.0.1');
INSERT INTO `user_log` VALUES ('282', '1', '平台-发货到军方 >> 12(12瓶) 订单号: 20171101094608050546', '1509692855', '127.0.0.1');
INSERT INTO `user_log` VALUES ('283', '1', '平台-库存供应 >> 韦尔奇无(1瓶) 订单号: 20171101094447030002', '1509692985', '127.0.0.1');
INSERT INTO `user_log` VALUES ('284', '1', '平台-分配供应商 >> sad(1瓶) 供应商: 供货商_001', '1509693009', '127.0.0.1');
INSERT INTO `user_log` VALUES ('285', '1', '平台-发货到军方 >> 韦尔奇无(1瓶) 订单号: 20171101094447030002', '1509693014', '127.0.0.1');
INSERT INTO `user_log` VALUES ('286', '1', '军方-发布需求 >> 全文(12 瓶)', '1509693643', '127.0.0.1');
INSERT INTO `user_log` VALUES ('287', '1', '军方-发布需求 >> 1人2(12 瓶)', '1509693666', '127.0.0.1');
INSERT INTO `user_log` VALUES ('288', '1', '平台-分配供应商 >> 1人2(12瓶) 供应商: 供货商_001,供货商_002', '1509693705', '127.0.0.1');
INSERT INTO `user_log` VALUES ('289', '1', '军方-发布需求 >> 212121(2121 瓶)', '1509693756', '127.0.0.1');
INSERT INTO `user_log` VALUES ('290', '1', '平台-分配供应商 >> 212121(2121瓶) 供应商: 供货商_001,供货商_002', '1509693789', '127.0.0.1');
INSERT INTO `user_log` VALUES ('291', '1', '平台-发布需求 >> 12(12瓶) 供应商: 供货商_001', '1509693822', '127.0.0.1');
INSERT INTO `user_log` VALUES ('292', '1', '平台-发布需求 >> 全文21(21瓶) 供应商: 供货商_001,供货商_002,供货商_003', '1509693916', '127.0.0.1');
INSERT INTO `user_log` VALUES ('293', '40', '供应商-报价 >> 订单:(20171103152987058919 全文21 21瓶) 报价: 供货商_002   1.0000 元', '1509699885', '127.0.0.1');
INSERT INTO `user_log` VALUES ('294', '40', '供应商-报价 >> 订单:(20171103152529015001 212121 2121瓶) 报价: 供货商_002   58.0495 元', '1509701443', '127.0.0.1');
INSERT INTO `user_log` VALUES ('295', '1', '平台-选择供应商 >> 212121(2121瓶) 选择供应商: 供货商_002', '1509702637', '127.0.0.1');
INSERT INTO `user_log` VALUES ('296', '40', '供应商-配货 >> 订单:(20171103152529015001 212121 2121瓶) 配货', '1509702651', '127.0.0.1');
INSERT INTO `user_log` VALUES ('297', '1', '商品管理-新增商品分类 >> 范德萨sad(计量单位:12)', '1509955567', '127.0.0.1');
INSERT INTO `user_log` VALUES ('298', '1', '商品管理-新增商品分类 >> 去温泉无(计量单位:斤)', '1509955604', '127.0.0.1');
INSERT INTO `user_log` VALUES ('299', '1', '商品管理-新增商品分类 >> 去去去(计量单位:斤)', '1509955889', '127.0.0.1');
INSERT INTO `user_log` VALUES ('300', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957438', '127.0.0.1');
INSERT INTO `user_log` VALUES ('301', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957460', '127.0.0.1');
INSERT INTO `user_log` VALUES ('302', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957464', '127.0.0.1');
INSERT INTO `user_log` VALUES ('303', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957472', '127.0.0.1');
INSERT INTO `user_log` VALUES ('304', '1', '商品管理-分类开启首页显示 >> 饮料33', '1509957515', '127.0.0.1');
INSERT INTO `user_log` VALUES ('305', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957518', '127.0.0.1');
INSERT INTO `user_log` VALUES ('306', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957523', '127.0.0.1');
INSERT INTO `user_log` VALUES ('307', '1', '商品管理-分类取消首页显示 >> 饮料33', '1509957526', '127.0.0.1');
INSERT INTO `user_log` VALUES ('308', '1', '商品管理-分类开启首页显示 >> 饮料33', '1509957529', '127.0.0.1');
INSERT INTO `user_log` VALUES ('309', '1', '商品管理-分类取消首页显示 >> 饮料33', '1509957532', '127.0.0.1');
INSERT INTO `user_log` VALUES ('310', '1', '商品管理-分类开启首页显示 >> 饮料33', '1509957544', '127.0.0.1');
INSERT INTO `user_log` VALUES ('311', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957583', '127.0.0.1');
INSERT INTO `user_log` VALUES ('312', '1', '商品管理-分类开启首页显示 >> 水果', '1509957708', '127.0.0.1');
INSERT INTO `user_log` VALUES ('313', '1', '商品管理-分类取消首页显示 >> 水果', '1509957711', '127.0.0.1');
INSERT INTO `user_log` VALUES ('314', '1', '商品管理-分类开启首页显示 >> 水果', '1509957715', '127.0.0.1');
INSERT INTO `user_log` VALUES ('315', '1', '商品管理-分类开启首页显示 >> 饮料', '1509957720', '127.0.0.1');
INSERT INTO `user_log` VALUES ('316', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957723', '127.0.0.1');
INSERT INTO `user_log` VALUES ('317', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957968', '127.0.0.1');
INSERT INTO `user_log` VALUES ('318', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957968', '127.0.0.1');
INSERT INTO `user_log` VALUES ('319', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957969', '127.0.0.1');
INSERT INTO `user_log` VALUES ('320', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957970', '127.0.0.1');
INSERT INTO `user_log` VALUES ('321', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957978', '127.0.0.1');
INSERT INTO `user_log` VALUES ('322', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957979', '127.0.0.1');
INSERT INTO `user_log` VALUES ('323', '1', '商品管理-分类开启首页显示 >> 蔬菜22', '1509957981', '127.0.0.1');
INSERT INTO `user_log` VALUES ('324', '1', '商品管理-分类取消首页显示 >> 蔬菜22', '1509957982', '127.0.0.1');
INSERT INTO `user_log` VALUES ('325', '1', '商品管理-新增商品分类 >> 休闲零食(计量单位:袋)', '1509958230', '127.0.0.1');
INSERT INTO `user_log` VALUES ('326', '1', '商品管理-新增商品 >> 上海来伊份 菲律宾香蕉片 500克 果干休闲零食 江浙沪皖满百包邮(商品分类:休闲零食)', '1509958380', '127.0.0.1');
INSERT INTO `user_log` VALUES ('327', '1', '商品管理-新增商品 >> 俄罗斯巧克力威化榛仁夹心饼干 进口休闲零食 200g/袋(商品分类:休闲零食)', '1509958415', '127.0.0.1');
INSERT INTO `user_log` VALUES ('328', '1', '商品管理-新增商品 >> 飘零大叔原味紫薯脆 地瓜干果干紫薯条地瓜条办公室休闲零食128g(商品分类:休闲零食)', '1509958433', '127.0.0.1');
INSERT INTO `user_log` VALUES ('329', '1', '商品管理-新增商品 >> 喜之郎蜜桔果肉果冻450g中袋装 休闲怀旧零食小吃大礼包婚庆(商品分类:休闲零食)', '1509958460', '127.0.0.1');
INSERT INTO `user_log` VALUES ('330', '1', '商品管理-新增商品分类 >> 全文(计量单位:斤)', '1509958435', '127.0.0.1');
INSERT INTO `user_log` VALUES ('331', '1', '商品管理-分类开启首页显示 >> 休闲零食', '1509958475', '127.0.0.1');
INSERT INTO `user_log` VALUES ('332', '1', '商品管理-新增商品分类 >> 酒类(计量单位:瓶)', '1509958543', '127.0.0.1');
INSERT INTO `user_log` VALUES ('333', '1', '商品管理-新增商品 >> 爱尔兰进口洋酒 奥妙10年单一麦芽威士忌Bushmills布什米尔斯10年(商品分类:酒类)', '1509958567', '127.0.0.1');
INSERT INTO `user_log` VALUES ('334', '1', '商品管理-新增商品 >> 52度泸州老窖精品头曲光瓶品鉴酒500ml浓香型(商品分类:酒类)', '1509958598', '127.0.0.1');
INSERT INTO `user_log` VALUES ('335', '1', '商品管理-新增商品 >> 日本进口 现货 麒麟冰结果酒 KIRIN Chu-Hi 氷結 青乌梅 350ml(商品分类:酒类)', '1509958617', '127.0.0.1');
INSERT INTO `user_log` VALUES ('336', '1', '商品管理-新增商品分类 >> 汽车(计量单位:辆)', '1509959967', '127.0.0.1');
INSERT INTO `user_log` VALUES ('337', '1', '商品管理-新增商品 >> 奥迪(商品分类:汽车)', '1509959984', '127.0.0.1');
INSERT INTO `user_log` VALUES ('338', '1', '商品管理-删除商品分类 >> 全文(计量单位:斤)', '1509959989', '127.0.0.1');
INSERT INTO `user_log` VALUES ('339', '1', '商品管理-新增商品 >> 宝马(商品分类:汽车)', '1509960002', '127.0.0.1');
INSERT INTO `user_log` VALUES ('340', '1', '商品管理-新增商品 >> 奔驰(商品分类:汽车)', '1509960012', '127.0.0.1');
INSERT INTO `user_log` VALUES ('341', '1', '商品管理-新增商品 >> 卡宴(商品分类:汽车)', '1509960031', '127.0.0.1');
INSERT INTO `user_log` VALUES ('342', '1', '商品管理-新增商品 >> 爱玛电动车(商品分类:汽车)', '1509960051', '127.0.0.1');
INSERT INTO `user_log` VALUES ('343', '1', '商品管理-新增商品 >> 捷安特(商品分类:汽车)', '1509960066', '127.0.0.1');
INSERT INTO `user_log` VALUES ('344', '1', '商品管理-新增商品 >> AAA(商品分类:汽车)', '1509960079', '127.0.0.1');
INSERT INTO `user_log` VALUES ('345', '1', '商品管理-新增商品 >> BBB(商品分类:汽车)', '1509960086', '127.0.0.1');
INSERT INTO `user_log` VALUES ('346', '1', '商品管理-新增商品 >> CCC(商品分类:汽车)', '1509960092', '127.0.0.1');
INSERT INTO `user_log` VALUES ('347', '1', '商品管理-新增商品 >> DDD(商品分类:汽车)', '1509960099', '127.0.0.1');
INSERT INTO `user_log` VALUES ('348', '1', '商品管理-新增商品 >> EEE(商品分类:汽车)', '1509960107', '127.0.0.1');
INSERT INTO `user_log` VALUES ('349', '1', '商品管理-新增商品 >> FFF(商品分类:汽车)', '1509960113', '127.0.0.1');
INSERT INTO `user_log` VALUES ('350', '1', '商品管理-新增商品 >> GGG(商品分类:汽车)', '1509960120', '127.0.0.1');
INSERT INTO `user_log` VALUES ('351', '1', '商品管理-分类开启首页显示 >> 酒类', '1509960131', '127.0.0.1');
INSERT INTO `user_log` VALUES ('352', '1', '商品管理-分类开启首页显示 >> 汽车', '1509960132', '127.0.0.1');
INSERT INTO `user_log` VALUES ('353', '1', '商品管理-分类取消首页显示 >> 汽车', '1510013875', '127.0.0.1');
INSERT INTO `user_log` VALUES ('354', '1', '商品管理-分类取消首页显示 >> 酒类', '1510014022', '127.0.0.1');
INSERT INTO `user_log` VALUES ('355', '1', '商品管理-分类取消首页显示 >> 休闲零食', '1510014022', '127.0.0.1');
INSERT INTO `user_log` VALUES ('356', '1', '商品管理-分类开启首页显示 >> 汽车', '1510014040', '127.0.0.1');
INSERT INTO `user_log` VALUES ('357', '1', '商品管理-分类开启首页显示 >> 酒类', '1510014040', '127.0.0.1');
INSERT INTO `user_log` VALUES ('358', '1', '商品管理-分类开启首页显示 >> 休闲零食', '1510014041', '127.0.0.1');
INSERT INTO `user_log` VALUES ('359', '1', '商品管理-修改商品 >> 卡宴(商品分类:汽车)', '1510014064', '127.0.0.1');
INSERT INTO `user_log` VALUES ('360', '1', '商品管理-修改商品 >> 上海来伊份 菲律宾香蕉片 500克 果干休闲零食 江浙沪皖满百包邮(商品分类:休闲零食)', '1510017229', '127.0.0.1');
INSERT INTO `user_log` VALUES ('361', '1', '商品管理-修改商品 >> 上海来伊份 菲律宾香蕉片 500克 果干休闲零食 江浙沪皖满百包邮(商品分类:休闲零食)', '1510017272', '127.0.0.1');
INSERT INTO `user_log` VALUES ('362', '44', '用户管理-禁用用户 >> 供货商: 供货商_005(supplier_005)', '1510017812', '127.0.0.1');
INSERT INTO `user_log` VALUES ('363', '44', '用户管理-禁用用户 >> 供货商: 供货商_004(supplier_004)', '1510017838', '127.0.0.1');
INSERT INTO `user_log` VALUES ('364', '44', '用户管理-禁用用户 >> 供货商: 供货商_003(supplier_003)', '1510017839', '127.0.0.1');
INSERT INTO `user_log` VALUES ('365', '44', '用户管理-禁用用户 >> 供货商: 供货商_002(supplier_002)', '1510017842', '127.0.0.1');
INSERT INTO `user_log` VALUES ('366', '44', '用户管理-启用用户 >> 供货商: 供货商_005(supplier_005)', '1510017844', '127.0.0.1');
INSERT INTO `user_log` VALUES ('367', '44', '用户管理-启用用户 >> 供货商: 供货商_004(supplier_004)', '1510017845', '127.0.0.1');
INSERT INTO `user_log` VALUES ('368', '44', '用户管理-启用用户 >> 供货商: 供货商_003(supplier_003)', '1510017846', '127.0.0.1');
INSERT INTO `user_log` VALUES ('369', '44', '用户管理-启用用户 >> 供货商: 供货商_002(supplier_002)', '1510017846', '127.0.0.1');
INSERT INTO `user_log` VALUES ('370', '44', '用户管理-启用用户 >> 平台运营员: asdfsdf(156489)', '1510017849', '127.0.0.1');
INSERT INTO `user_log` VALUES ('371', '44', '用户管理-启用用户 >> 平台运营员: asdqq(qwe)', '1510017850', '127.0.0.1');
INSERT INTO `user_log` VALUES ('372', '44', '用户管理-禁用用户 >> 平台运营员: asdqq(qwe)', '1510017854', '127.0.0.1');
INSERT INTO `user_log` VALUES ('373', '44', '用户管理-启用用户 >> 平台运营员: asdqq(qwe)', '1510017858', '127.0.0.1');
