/*
Navicat MySQL Data Transfer

Source Server         : 192.168.0.254
Source Server Version : 50719
Source Host           : 192.168.0.254:3306
Source Database       : leacmf

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2017-10-31 14:17:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ad
-- ----------------------------
DROP TABLE IF EXISTS `ad`;
CREATE TABLE `ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL,
  `picture` varchar(128) DEFAULT NULL,
  `sort` smallint(5) DEFAULT NULL,
  `action_type` tinyint(1) DEFAULT NULL,
  `action_param` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-禁用 1-启用 2-删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ad
-- ----------------------------
INSERT INTO `ad` VALUES ('4', '1', 'banner', 'Fka58TZrkGuuNXnbQKSAoume1SP1', '0', '0', '', '', '1508125727', '1508138666', '1');
INSERT INTO `ad` VALUES ('5', '1', 'banner', 'Fka58TZrkGuuNXnbQKSAoume1SP1', '0', '0', '', '', '1508132308', '1508138491', '1');

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `nickname` varchar(20) NOT NULL COMMENT '昵称',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `face` int(10) NOT NULL DEFAULT '0',
  `token` varchar(32) DEFAULT NULL COMMENT 'token',
  `login_times` int(10) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `last_login_time` int(10) DEFAULT NULL COMMENT '上次登陆时间',
  `last_login_ip` char(16) DEFAULT NULL COMMENT '上次登陆ip',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-禁用 1-正常',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', 'admin', 'd3e3aad7256f373a52a9cfb99bb54c98', '6', '8bd4e9e2e09621edf2b60ffef4b4c2d6', '685', '1508981170', '222.128.15.91', '1', '1');
INSERT INTO `admin` VALUES ('2', 'manage', '管理员', 'd3e3aad7256f373a52a9cfb99bb54c98', '0', '9c1f396f25668e2b2322936c7e02e603', '47', '1504764750', '1.25.227.160', '1', '1496306374');

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cate` smallint(1) unsigned NOT NULL COMMENT '1-系统单页 2-文章',
  `title` varchar(64) NOT NULL,
  `cover_id` int(11) unsigned DEFAULT NULL,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `create_aid` smallint(5) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1-正常 0-下架 2-删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of article
-- ----------------------------
INSERT INTO `article` VALUES ('1', '1', '冠军助力怡宝，优惠活动来袭~！', '44', '<p><span>华润怡宝，中国饮用水市场的领先品牌，在华南地区市场占有率连续多年稳居首位，2007年销量达到108万吨。1989年，华润怡宝在国内率先推出</span><a target=\"_blank\" href=\"http://baike.baidu.com/item/%E7%BA%AF%E5%87%80%E6%B0%B4\">纯净水</a><span>，是国内最早专业化生产包装水的企业之一。华润怡宝也是国家质监和卫生</span><a target=\"_blank\" href=\"http://baike.baidu.com/item/%E9%A5%AE%E7%94%A8%E7%BA%AF%E5%87%80%E6%B0%B4\">饮用纯净水</a><span>国家标准的主要发起和起草单位之一。华润怡宝始终以优于“国标”的生产标准为消费者提供健康满意的优质产品，并通过良好的服务，赢得消费者的认同。华润怡宝多年来得到了各级政府部门的肯定与嘉奖，获得</span><a target=\"_blank\" href=\"http://baike.baidu.com/item/%E4%B8%AD%E5%9B%BD%E5%90%8D%E7%89%8C\">中国名牌</a><span>产品、中国最具竞争力品牌等荣誉。2008年，华润怡宝水业务的管理纳入</span><a target=\"_blank\" href=\"http://baike.baidu.com/item/%E5%8D%8E%E6%B6%A6%E9%9B%86%E5%9B%A2\">华润集团</a><span>6S管理体系，华润怡宝食品饮料（深圳）有限公司列入</span><a target=\"_blank\" href=\"http://baike.baidu.com/item/%E5%8D%8E%E6%B6%A6\">华润</a><span>（集团）有限公司一级利润中心序列。</span></p><p><span><img src=\"/image/lJxzmzzqg\" alt=\"undefined\"><br></span></p><p><span><img src=\"/image/lQSNiXmqe\" alt=\"undefined\"><br></span></p><p><span>华润怡宝，继2009年“怡宝VBA广东省大学生三人篮球赛”后，2011年5月24日，华润怡宝携手中国乒乓球队在深圳举行了盛大的战略合作签约酒会，怡宝同时也获得了“中国国家乒乓球队唯一指定饮料”称号。分析人士认为，作为华南地区饮用水品牌的领头羊，此次携手中国兵乓球队，是华润怡宝对多年来体育营销策略的深化，将助其在竞争白热化的饮用水市场处于更稳固的地位。</span></p>', '1498713850', '1', '1498713850', '2');
INSERT INTO `article` VALUES ('2', '1', 'test', '0', 'sdf', '1503571073', '1', '1503571073', '2');
INSERT INTO `article` VALUES ('3', '1', 'asdf', '0', 'asdf', '1503571256', '1', '1503571256', '2');
INSERT INTO `article` VALUES ('4', '1', '全民美APP强势来袭！！！', '276', '<p>华润怡宝，中国饮用水市场的领先品牌，在华南地区市场占有率连续多年稳居首位，2007年销量达到108万吨。1989年，华润怡宝在国内率先推出<a target=\"_blank\" href=\"http://baike.baidu.com/item/%E7%BA%AF%E5%87%80%E6%B0%B4\">纯净水</a>，是国内最早专业化生产包装水的企业之一。华润怡宝也是国家质监和卫生<a target=\"_blank\" href=\"http://baike.baidu.com/item/%E9%A5%AE%E7%94%A8%E7%BA%AF%E5%87%80%E6%B0%B4\">饮用纯净水</a>国家标准的主要发起和起草单位之一。华润怡宝始终以优于“国标”的生产标准为消费者提供健康满意的优质产品，并通过良好的服务，赢得消费者的认同。华润怡宝多年来得到了各级政府部门的肯定与嘉奖，获得<a target=\"_blank\" href=\"http://baike.baidu.com/item/%E4%B8%AD%E5%9B%BD%E5%90%8D%E7%89%8C\">中国名牌</a>产品、中国最具竞争力品牌等荣誉。2008年，华润怡宝水业务的管理纳入<a target=\"_blank\" href=\"http://baike.baidu.com/item/%E5%8D%8E%E6%B6%A6%E9%9B%86%E5%9B%A2\">华润集团</a>6S管理体系，华润怡宝食品饮料（深圳）有限公司列入<a target=\"_blank\" href=\"http://baike.baidu.com/item/%E5%8D%8E%E6%B6%A6\">华润</a>（集团）有限公司一级利润中心序列。</p><p><img src=\"/image/lJxzmzzqg\" alt=\"undefined\"><br></p><p><img src=\"/image/lQSNiXmqe\" alt=\"undefined\"><br></p><p>华润怡宝，继2009年“怡宝VBA广东省大学生三人篮球赛”后，2011年5月24日，华润怡宝携手中国乒乓球队在深圳举行了盛大的战略合作签约酒会，怡宝同时也获得了“中国国家乒乓球队唯一指定饮料”称号。分析人士认为，作为华南地区饮用水品牌的领头羊，此次携手中国兵乓球队，是华润怡宝对多年来体育营销策略的深化，将助其在竞争白热化的饮用水市场处于更稳固的地位。</p>', '1503651207', '1', '1503651207', '2');
INSERT INTO `article` VALUES ('5', '1', 'test', '0', 'test', '1503654629', '1', '1503654629', '2');
INSERT INTO `article` VALUES ('6', '1', '测试活动推送', '0', '<p>测试活动推送</p>', '1503654827', '1', '1503654827', '2');
INSERT INTO `article` VALUES ('7', '1', '全民美强势来袭', '0', '<p>全民美强势来袭全民美强势来袭全民美强势来袭全民美强势来袭</p>', '1503655438', '1', '1503655438', '2');
INSERT INTO `article` VALUES ('8', '1', '优惠活动来袭~！', '0', '<p><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span></p>', '1503655625', '1', '1503655625', '2');
INSERT INTO `article` VALUES ('9', '1', '优惠活动来袭~！', '0', '<p><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span></p>', '1503655808', '1', '1503655808', '2');
INSERT INTO `article` VALUES ('10', '2', '优惠活动来袭~！', '0', '<p><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span><span>优惠活动来袭~！</span></p>', '1503657105', '1', '1503657105', '2');
INSERT INTO `article` VALUES ('11', '1', '系统重磅升级中...', '384', '升级中。。', '1504322695', '1', '1504322695', '1');
INSERT INTO `article` VALUES ('12', '1', '系统重磅升级', '0', '<p><img src=\"/uploads/image/20170904/4d2d6aa3fb40b434490b0e8eb259b6a0.jpg\" alt=\"undefined\"></p><p>微信文字编辑器样式不识别 &nbsp;<a target=\"_self\" href=\"https://mp.weixin.qq.com/s?__biz=MzAxMjcyMjE1Ng==&amp;tempkey=OTIwX0VoL0JJYmI5dGNiZFM5WkYwd0JlWVltRDVya1BiRVR1UlFUcDVKc2NmZ1ZIaXVqZnd3cm5kMkJ1cmdWaS1WQ0puN1RjTVdWOGUtSnRsNTFkVUNpN0pXNFhLUHlCZ1plc1hBNVhicDl6M1U3eGxadnRlODdiWWVUZHRQZW9KRFJtWFI3WEk0MUdQMmZndi1INFJ2SXM1VDZ3cUtJc2l1X0M5MzlvR1F%2Bfg%3D%3D&amp;chksm=004a34bf373dbda9c5749348491baa714508d81f0a752c4b3f80634b91a43067cf43e5d8e373#rd\">引用链接</a></p><section class=\"editor\"><section class=\"96wx-bdc\"><span class=\"96wx-bdc\"><section class=\"96wx-bgc\" style=\"text-align: center;\">今日话题</section><img class=\"96wx-bdc \" src=\"https://mmbiz.qlogo.cn/mmbiz/SlzGSgJicOCyyFCCia7KrgN9uruqH8dB461o9ZgmIVbOdRSicIpLRPBuciaGl0YKedcIfpXGI9TEia3a14TFWdLNrgQ/0?wx_fmt=gif\"></span><section>一天一个鸡蛋，不仅能提高记忆力，还能保护视力，帮助减肥。但有些人对鸡蛋心有疑虑，怕每天吃升高血脂。殊不知，早餐吃个鸡蛋，可是有诸多好处。在营养学界，鸡蛋一直有“全营养食品”的美称，杂志甚至为鸡蛋戴上了“世界上最营养早餐”的殊荣。</section></section></section>', '1504506132', '1', '1504506513', '1');
INSERT INTO `article` VALUES ('13', '2', '11111111111111111111', '424', '11111111', '1504711084', '1', '1504711084', '1');
INSERT INTO `article` VALUES ('14', '1', '全民美APP带你走进不一样的电商', '432', '<p><img src=\"/uploads/image/20170907/f99791079aceb6c1aa751f6dbb807939.jpg\" alt=\"undefined\"></p><p>呜啦啦呜啦啦<br></p>', '1504756154', '2', '1504756154', '1');
INSERT INTO `article` VALUES ('16', '1', 'asdf', '9', '<p>sdf</p><p><img src=\"/uploads/image/20171010/0baa9176b0a42a34b08e39f915a93e28.png\" alt=\"undefined\"><br></p>', '1507621712', '1', '1507622250', '1');

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text COMMENT '规则ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` smallint(5) NOT NULL DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='分组表';

-- ----------------------------
-- Records of auth_group
-- ----------------------------
INSERT INTO `auth_group` VALUES ('1', '超级管理员', '*', '1', '0', '');
INSERT INTO `auth_group` VALUES ('2', '管理员', '92,93,58,61,59,56,57,135,60,13,15,14,11,12,34,36,35,32,33,116,114,115,96,97,94,95,129,70,72,71,68,69,113,142,118,122,119,117,120,121,88,125,90,91,89,1,83,84,103,124,101,102,123,104,126,127,128,131,141,85,86,87,98,99,100,138,139,130,136,137,105,108,110,106,109,107,4,81,82,132,140,133,65,66,67,134,76,78,77,74,75,80,79', '1', '0', '');

-- ----------------------------
-- Table structure for auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `auth_group_access`;
CREATE TABLE `auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '组ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限分组表';

-- ----------------------------
-- Records of auth_group_access
-- ----------------------------
INSERT INTO `auth_group_access` VALUES ('1', '1');
INSERT INTO `auth_group_access` VALUES ('2', '2');

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则标题',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `is_menu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(10) NOT NULL COMMENT '权重',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 COMMENT='节点表';

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
INSERT INTO `auth_rule` VALUES ('1', '0', '/admin/index/index', '控制台', 'fa-dashboard', '', '用于展示当前系统中的统计数据、统计报表及重要实时数据', '1', '1494569696', '1498534871', '-9', '1');
INSERT INTO `auth_rule` VALUES ('2', '0', '/admin/system/config', '系统管理', 'fa-cogs', '', '', '1', '1494570009', '1498534886', '-8', '1');
INSERT INTO `auth_rule` VALUES ('3', '0', '/admin/auth/admin', '权限管理', 'fa-sitemap', '', '', '1', '1494570136', '1498534901', '-7', '1');
INSERT INTO `auth_rule` VALUES ('4', '2', '/admin/system/config/group', '系统设置', 'fa-cog', '', '', '1', '1494570435', '1494570435', '0', '1');
INSERT INTO `auth_rule` VALUES ('5', '2', '/admin/system/config/index', '配置管理', 'fa-magic', '', '用于管理一些字典数据,通常以键值格式进行录入,程序将解析成相应的类型', '1', '1494570482', '1494570490', '0', '1');
INSERT INTO `auth_rule` VALUES ('6', '5', '/admin/system/config/add', '添加配置', '', '', '', '0', '1494570541', '1494570541', '0', '1');
INSERT INTO `auth_rule` VALUES ('7', '5', '/admin/system/config/edit', '修改配置', '', '', '', '0', '1494570557', '1494570565', '0', '1');
INSERT INTO `auth_rule` VALUES ('8', '5', '/admin/system/config/lists', '配置列表', '', '', '', '0', '1494570600', '1494570600', '0', '1');
INSERT INTO `auth_rule` VALUES ('9', '5', '/admin/system/config/sort', '快速排序', '', '', '', '0', '1494570721', '1494570753', '0', '1');
INSERT INTO `auth_rule` VALUES ('10', '5', '/admin/system/config/lock', '状态锁定', '', '', '', '0', '1494570739', '1494570761', '0', '1');
INSERT INTO `auth_rule` VALUES ('11', '3', '/admin/auth/admin/index', '后台用户管理', ' fa-user', '', '', '1', '1494570920', '1495850074', '0', '1');
INSERT INTO `auth_rule` VALUES ('12', '11', '/admin/auth/admin/lists', '用户列表', '', '', '', '0', '1494570935', '1494570952', '0', '1');
INSERT INTO `auth_rule` VALUES ('13', '11', '/admin/auth/admin/add', '添加用户', '', '', '该权限包含分配用户组', '0', '1494570998', '1494571178', '0', '1');
INSERT INTO `auth_rule` VALUES ('14', '11', '/admin/auth/admin/edit', '修改用户', '', '', '该权限包含分配用户组', '0', '1494571005', '1494571005', '0', '1');
INSERT INTO `auth_rule` VALUES ('15', '11', '/admin/auth/admin/delete', '删除用户', '', '', '', '0', '1494571013', '1494571013', '0', '1');
INSERT INTO `auth_rule` VALUES ('16', '5', '/admin/system/config/delete', '删除配置', '', '', '', '0', '1494571037', '1496307872', '0', '1');
INSERT INTO `auth_rule` VALUES ('17', '3', '/admin/auth/group/index', '角色管理', 'fa-users', '', '', '1', '1494571157', '1494571157', '0', '1');
INSERT INTO `auth_rule` VALUES ('18', '17', '/admin/auth/group/lists', '角色列表', '', '', '', '0', '1494571297', '1495697646', '0', '1');
INSERT INTO `auth_rule` VALUES ('19', '17', '/admin/auth/group/add', '添加角色', '', '', '', '0', '1494571308', '1494571308', '0', '1');
INSERT INTO `auth_rule` VALUES ('20', '17', '/admin/auth/group/edit', '修改角色', '', '', '', '0', '1494571327', '1494571327', '0', '1');
INSERT INTO `auth_rule` VALUES ('21', '17', '/admin/auth/group/sort', '排序', '', '', '', '0', '1494571345', '1494571345', '0', '1');
INSERT INTO `auth_rule` VALUES ('22', '17', '/admin/auth/group/delete', '删除', '', '', '', '0', '1494571353', '1494571353', '0', '1');
INSERT INTO `auth_rule` VALUES ('23', '17', '/admin/auth/group/assigned', '分配权限', '', '', '', '0', '1494571376', '1494571376', '0', '1');
INSERT INTO `auth_rule` VALUES ('24', '3', '/admin/auth/rule/index', '规则&菜单', 'fa-child', '', '', '1', '1494571444', '1494571444', '0', '1');
INSERT INTO `auth_rule` VALUES ('25', '24', '/admin/auth/rule/lists', '规则列表', '', '', '', '0', '1494571479', '1494571479', '0', '1');
INSERT INTO `auth_rule` VALUES ('26', '24', '/admin/auth/rule/add', '添加规则', '', '', '', '0', '1494571490', '1494571490', '0', '1');
INSERT INTO `auth_rule` VALUES ('27', '24', '/admin/auth/rule/edit', '修改规则', '', '', '', '0', '1494571497', '1494571497', '0', '1');
INSERT INTO `auth_rule` VALUES ('28', '24', '/admin/auth/rule/sort', '快速排序', '', '', '', '0', '1494571516', '1494571516', '0', '1');
INSERT INTO `auth_rule` VALUES ('29', '24', '/admin/auth/rule/delete', '删除', '', '', '', '0', '1494571525', '1494571525', '0', '1');
INSERT INTO `auth_rule` VALUES ('30', '24', '/admin/auth/rule/menu', '设置菜单', '', '', '', '0', '1494571554', '1494571554', '0', '1');
INSERT INTO `auth_rule` VALUES ('31', '0', '/admin/article/index', '文章管理', 'fa fa fa-feed', '', '', '1', '1507616189', '1507616189', '0', '1');
INSERT INTO `auth_rule` VALUES ('32', '31', '/admin/article/add', '添加', '', '', '', '0', '1507616311', '1507616311', '0', '1');
INSERT INTO `auth_rule` VALUES ('33', '31', '/admin/article/edit', '修改', '', '', '', '0', '1507616329', '1507616329', '0', '1');
INSERT INTO `auth_rule` VALUES ('34', '31', '/admin/article/delete', '删除', '', '', '', '0', '1507616374', '1507616374', '0', '1');
INSERT INTO `auth_rule` VALUES ('35', '31', '/admin/article/setStatus', '设置状态', '', '', '', '0', '1507624177', '1507624177', '0', '1');
INSERT INTO `auth_rule` VALUES ('36', '0', '/admin/ad/index', '广告管理', 'fa fa-buysellads', '', '', '1', '1507689641', '1507689832', '0', '1');
INSERT INTO `auth_rule` VALUES ('37', '36', '/admin/ad/add', '添加广告', '', '', '', '0', '1507689661', '1507689661', '0', '1');
INSERT INTO `auth_rule` VALUES ('38', '36', '/admin/ad/edit', '编辑广告', '', '', '', '0', '1507689679', '1507689679', '0', '1');
INSERT INTO `auth_rule` VALUES ('39', '36', '/admin/ad/delete', '删除广告', '', '', '', '0', '1507689694', '1507689694', '0', '1');
INSERT INTO `auth_rule` VALUES ('40', '36', '/admin/ad/setStatus', '状态设置', '', '', '', '0', '1507689710', '1507689710', '0', '1');
INSERT INTO `auth_rule` VALUES ('41', '36', '/admin/ad/sort', '快速排序', '', '', '', '0', '1507691376', '1507691376', '0', '1');
INSERT INTO `auth_rule` VALUES ('42', '0', '/admin/push/index', '推送管理', 'fa fa fa-bell-o', '', '', '1', '1507692655', '1507692655', '0', '1');
INSERT INTO `auth_rule` VALUES ('43', '42', '/admin/push/lists', '推送信息', '', '', '', '0', '1507692670', '1507692670', '0', '1');
INSERT INTO `auth_rule` VALUES ('44', '42', '/admin/push/publish', '推送', '', '', '', '0', '1507692688', '1507692688', '0', '1');
INSERT INTO `auth_rule` VALUES ('83', '0', '/admin/user/index', '用户管理', 'fa fa-users', '', '', '1', '1508296838', '1508296838', '0', '1');
INSERT INTO `auth_rule` VALUES ('84', '83', '/admin/user/lists', '用户列表', '', '', '', '0', '1508296856', '1508296856', '0', '1');
INSERT INTO `auth_rule` VALUES ('85', '83', '/admin/user/setStatus', '状态设置', '', '', '', '0', '1508297900', '1508297900', '0', '1');

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(522) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `lock` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `value` text COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `group` (`group`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `config` VALUES ('1', 'web_site_title', '1', '网站标题', '1', '', '网站标题前台显示标题', '1378898976', '1494505103', '0', '家长培优', '0');
INSERT INTO `config` VALUES ('5', 'color_style', '4', '后台色系', '1', 'skin-yellow:skin-yellow\r\nskin-yellow-light:skin-yellow-light\r\nskin-red:skin-red\r\nskin-red-light:skin-red-light\r\nskin-purple:skin-purple\r\nskin-purple-light:skin-purple-light\r\nskin-green:skin-green\r\nskin-green-light:skin-green-light\r\nskin-blue:skin-blue\r\nskin-blue-light:skin-blue-light\r\nskin-black:skin-black\r\nskin-black-light:skin-black-light', '后台颜色风格', '1379122533', '1379235904', '0', 'skin-blue', '0');
INSERT INTO `config` VALUES ('6', 'web_site_close', '4', '关闭站点', '1', '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', '1378898976', '1379235296', '0', '1', '0');
INSERT INTO `config` VALUES ('7', 'admin_allow_ip', '2', '后台允许访问IP', '0', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '1387165454', '1387165553', '0', '', '0');
INSERT INTO `config` VALUES ('8', 'config_group_list', '3', '配置分组', '0', '', '配置分组', '1379228036', '1477766924', '1', '1:基本\r\n3:业务\r\n5:App\r\n6:企业', '0');
INSERT INTO `config` VALUES ('9', 'allow_visit', '3', '不受限控制器方法', '0', '', '', '1386644047', '1386644741', '1', '0:/admin/index/index', '0');
INSERT INTO `config` VALUES ('10', 'config_type_list', '3', '配置类型列表', '4', '', '主要用于数据解析和页面表单的生成', '1378898976', '1379235348', '1', '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', '0');
INSERT INTO `config` VALUES ('13', 'token_expiration_date', '0', 'token过期时间', '5', '', '单位：秒，0表示用不过期', '0', '0', '0', '604800', '0');
INSERT INTO `config` VALUES ('15', 'sms_template', '1', '短信模板', '5', '', '用于配置注册和找回密码时发送短信的模板，{code}为验证码。', '0', '0', '0', '【家长思维】您的验证码是{code}，请在5分钟内填写。', '0');
INSERT INTO `config` VALUES ('16', 'pay_time_limit', '0', '支付限时', '5', '', '单位：分钟', '0', '0', '0', '30', '0');
INSERT INTO `config` VALUES ('17', 'web_copyright', '1', '版权设置', '1', '', '', '0', '0', '0', '北京睿亿网络科技有限公司', '0');

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for msg
-- ----------------------------
DROP TABLE IF EXISTS `msg`;
CREATE TABLE `msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '0-系统信息 其他推送信息相关id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `msg` varchar(155) NOT NULL,
  `data` varchar(522) NOT NULL DEFAULT '',
  `at_time` int(11) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未读 1-已读',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of msg
-- ----------------------------
INSERT INTO `msg` VALUES ('1', '0', 'test', '', '1507692726', '0');

-- ----------------------------
-- Table structure for setting
-- ----------------------------
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of setting
-- ----------------------------
INSERT INTO `setting` VALUES ('app_start_bg', '{\"src\":{\"1080_1920\":\"373\",\"960_1600\":\"390\",\"640_1136\":\"391\",\"750_1334\":\"392\",\"1242_2208\":\"393\"},\"url\":\"\"}', '启动图');
INSERT INTO `setting` VALUES ('business', '{\"3\":\"30\",\"4\":\"40\",\"5\":\"50\",\"6\":\"60\"}', '招商佣金比例');
INSERT INTO `setting` VALUES ('index_cate_picture', '{\"1\":\"434\",\"2\":\"435\"}', '首页分类图片');
INSERT INTO `setting` VALUES ('nobility', '{\"3\":\"2.04\",\"4\":\"3.06\",\"5\":\"7.14\",\"6\":\"8.16\"}', '爵位佣金比例');
INSERT INTO `setting` VALUES ('nobility_update', '{\"2\":{\"buy_amount\":\"98\",\"buy_goods_num\":\"1\"},\"3\":{\"buy_amount\":\"98\",\"sales_amount\":\"98\",\"nobility_num\":\"2\"},\"4\":{\"buy_amount\":\"98\",\"sales_amount\":\"98\",\"nobility_num\":\"2\"},\"5\":{\"buy_amount\":\"98\",\"sales_amount\":\"98\",\"nobility_num\":\"2\"},\"6\":{\"buy_amount\":\"980000\",\"sales_amount\":\"9800000\",\"nobility_num\":\"10000000\"},\"dp\":{\"4\":\"2\",\"5\":\"2\",\"6\":\"3\"}}', '爵位升级条件配置');
INSERT INTO `setting` VALUES ('nobility_update_money', '{\"3\":\"5000\",\"4\":\"10000\",\"5\":\"30000\",\"6\":\"60000\"}', '爵位升级价格配置');
INSERT INTO `setting` VALUES ('promotion_background', '408', '推广背景');
INSERT INTO `setting` VALUES ('shareholders_bonus', '2.04', '股东分红佣金比例');
INSERT INTO `setting` VALUES ('spread_1', '359', '谁是大咖头图');
INSERT INTO `setting` VALUES ('spread_2', '360', '谁是直播王头图');
INSERT INTO `setting` VALUES ('spread_3', '361', '财富排行榜头图');
INSERT INTO `setting` VALUES ('video', '{\"status\":1,\"cover\":\"426\"}', null);

-- ----------------------------
-- Table structure for sms
-- ----------------------------
DROP TABLE IF EXISTS `sms`;
CREATE TABLE `sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '接收信息的手机号',
  `code` varchar(32) NOT NULL COMMENT '验证码',
  `type` varchar(15) NOT NULL COMMENT '短信类型： 1:注册  2:修改密码',
  `status` tinyint(1) NOT NULL COMMENT '发送状态  1:成功  2:失败',
  `content` varchar(255) NOT NULL COMMENT '发送内容',
  `send_time` int(11) DEFAULT NULL COMMENT '发送的时间',
  `sms_ret_msg` varchar(100) DEFAULT NULL COMMENT '发送短信的返回',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='记录短信的发送历史';

-- ----------------------------
-- Records of sms
-- ----------------------------
INSERT INTO `sms` VALUES ('1', '18210881586', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507700441', 'success');
INSERT INTO `sms` VALUES ('2', '18210881586', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507700704', 'success');
INSERT INTO `sms` VALUES ('3', '18210881586', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507700735', 'success');
INSERT INTO `sms` VALUES ('4', '18210881586', '81dc9bdb52d04dc20036dbd8313ed055', 'forgetPassword', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507701220', 'success');
INSERT INTO `sms` VALUES ('5', '18622991098', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507703574', 'success');
INSERT INTO `sms` VALUES ('6', '18622991098', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507704242', 'success');
INSERT INTO `sms` VALUES ('7', '18622991098', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507704457', 'success');
INSERT INTO `sms` VALUES ('8', '18622991098', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507704541', 'success');
INSERT INTO `sms` VALUES ('9', '18622991098', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507704606', 'success');
INSERT INTO `sms` VALUES ('10', '18622991098', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1507704629', 'success');
INSERT INTO `sms` VALUES ('11', '18210905679', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508142819', 'success');
INSERT INTO `sms` VALUES ('12', '18210905679', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508146391', 'success');
INSERT INTO `sms` VALUES ('13', '18622991099', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508148283', 'success');
INSERT INTO `sms` VALUES ('14', '18210905679', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508150141', 'success');
INSERT INTO `sms` VALUES ('15', '18501993540', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508167229', 'success');
INSERT INTO `sms` VALUES ('16', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508169828', 'success');
INSERT INTO `sms` VALUES ('17', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508170547', 'success');
INSERT INTO `sms` VALUES ('18', '18501993540', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508209488', 'success');
INSERT INTO `sms` VALUES ('19', '18210905678', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508211252', 'success');
INSERT INTO `sms` VALUES ('20', '18201430000', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508290454', 'success');
INSERT INTO `sms` VALUES ('21', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508555488', 'success');
INSERT INTO `sms` VALUES ('22', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508556737', 'success');
INSERT INTO `sms` VALUES ('23', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508557680', 'success');
INSERT INTO `sms` VALUES ('24', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508558019', 'success');
INSERT INTO `sms` VALUES ('25', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508567003', 'success');
INSERT INTO `sms` VALUES ('26', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508567304', 'success');
INSERT INTO `sms` VALUES ('27', '17600115070', '81dc9bdb52d04dc20036dbd8313ed055', 'forgetPassword', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508571336', 'success');
INSERT INTO `sms` VALUES ('28', '18010485070', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508581710', 'success');
INSERT INTO `sms` VALUES ('29', '18010485070', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508581785', 'success');
INSERT INTO `sms` VALUES ('30', '18010485070', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508581849', 'success');
INSERT INTO `sms` VALUES ('31', '18201430001', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508926168', 'success');
INSERT INTO `sms` VALUES ('32', '18201430002', '81dc9bdb52d04dc20036dbd8313ed055', 'signUp', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508926469', 'success');
INSERT INTO `sms` VALUES ('33', '18210905679', '81dc9bdb52d04dc20036dbd8313ed055', 'forgetPassword', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508927447', 'success');
INSERT INTO `sms` VALUES ('34', '18210905679', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508928252', 'success');
INSERT INTO `sms` VALUES ('35', '18201430001', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508979170', 'success');
INSERT INTO `sms` VALUES ('36', '18201430000', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508979202', 'success');
INSERT INTO `sms` VALUES ('37', '18201430002', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508979297', 'success');
INSERT INTO `sms` VALUES ('38', '18201430002', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508979312', 'success');
INSERT INTO `sms` VALUES ('39', '18201430001', '81dc9bdb52d04dc20036dbd8313ed055', 'relationUser', '1', '【家长思维】您的验证码是1234，请在5分钟内填写。', '1508979436', 'success');

-- ----------------------------
-- Table structure for uploads
-- ----------------------------
DROP TABLE IF EXISTS `uploads`;
CREATE TABLE `uploads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(10) NOT NULL COMMENT '文件类型',
  `ext` varchar(20) DEFAULT NULL COMMENT '文件扩展名',
  `size` int(10) unsigned NOT NULL,
  `path` varchar(64) DEFAULT NULL,
  `filename` varchar(64) NOT NULL,
  `sha1` varchar(64) NOT NULL,
  `width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mime` varchar(32) DEFAULT NULL,
  `at_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uploads
-- ----------------------------
INSERT INTO `uploads` VALUES ('1', 'image', 'png', '2193', '20171010/944739d1573bf553f9c442c0c411868a.png', '944739d1573bf553f9c442c0c411868a.png', '6d65d00419aee23581f6dc029e70a3843863e425', '128', '128', 'image/png', '1507605540');
INSERT INTO `uploads` VALUES ('2', 'image', 'png', '1584', '20171010/86a37e9c7b6122609f3a399acb3ae4a1.png', '86a37e9c7b6122609f3a399acb3ae4a1.png', '7aa5158e3ae8515449e6715b081966a959de1e7b', '128', '128', 'image/png', '1507605560');
INSERT INTO `uploads` VALUES ('3', 'image', 'png', '2275', '20171010/94f4451791d244a5f38689e13eb5c613.png', '94f4451791d244a5f38689e13eb5c613.png', '573020a9cc75e3682bff0d9d551ade7728e4fb59', '128', '128', 'image/png', '1507605584');
INSERT INTO `uploads` VALUES ('4', 'image', 'png', '2269', '20171010/17eb4eb9f0450a6be4ae63dc99f741f8.png', '17eb4eb9f0450a6be4ae63dc99f741f8.png', '193057cf3016fca5d3b176b7d957c9c54ad50ebb', '128', '128', 'image/png', '1507606260');
INSERT INTO `uploads` VALUES ('5', 'image', 'png', '1989', '20171010/8bb78f056ee2c36c22f59b7d70c3699a.png', '8bb78f056ee2c36c22f59b7d70c3699a.png', '90d8778c998b5a1de645957bd89b473a6144ccde', '128', '128', 'image/png', '1507606268');
INSERT INTO `uploads` VALUES ('6', 'image', 'png', '7578', '20171010/ea11f01241a0eb3f20f1a04bb6adff16.png', 'ea11f01241a0eb3f20f1a04bb6adff16.png', '3af84861aaacebcddef13ef2783cb1088faf236b', '215', '215', 'image/png', '1507606501');
INSERT INTO `uploads` VALUES ('7', 'image', 'png', '7578', '20171010/3297e497b4f0d3bf01011b65af716573.png', '3297e497b4f0d3bf01011b65af716573.png', '3af84861aaacebcddef13ef2783cb1088faf236b', '215', '215', 'image/png', '1507619504');
INSERT INTO `uploads` VALUES ('8', 'image', 'png', '7578', '20171010/f6a7a2f4da7b401ceb0049e5055f6202.png', 'f6a7a2f4da7b401ceb0049e5055f6202.png', '3af84861aaacebcddef13ef2783cb1088faf236b', '215', '215', 'image/png', '1507622100');
INSERT INTO `uploads` VALUES ('9', 'image', 'png', '13539', '20171010/50a2ee64aa0d52c9057257766278250c.png', '50a2ee64aa0d52c9057257766278250c.png', 'c295afd4311682807535e1a02a4c90007da00a28', '215', '215', 'image/png', '1507622214');
INSERT INTO `uploads` VALUES ('10', 'image', 'png', '8262', '20171010/0baa9176b0a42a34b08e39f915a93e28.png', '0baa9176b0a42a34b08e39f915a93e28.png', '221bffc125e97decada22541605793c6e57c6466', '215', '215', 'image/png', '1507622245');
INSERT INTO `uploads` VALUES ('11', 'image', 'png', '9241', '20171011/c88ec54570fdab8d01254b7130ffb4d2.png', 'c88ec54570fdab8d01254b7130ffb4d2.png', '98764e968dd1f5b7a92a15494d5cc377e113e2fe', '215', '215', 'image/png', '1507690798');
INSERT INTO `uploads` VALUES ('12', 'image', 'png', '7578', '20171011/30d385e003ce236ef80ac633e2d7e112.png', '30d385e003ce236ef80ac633e2d7e112.png', '3af84861aaacebcddef13ef2783cb1088faf236b', '215', '215', 'image/png', '1507692074');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(13) DEFAULT NULL,
  `mobile` varchar(20) NOT NULL COMMENT '手机号',
  `nickname` varchar(64) NOT NULL DEFAULT '',
  `openid` varchar(64) DEFAULT NULL COMMENT '微信id',
  `face` varchar(128) DEFAULT NULL COMMENT '头像',
  `sex` enum('男','女') DEFAULT NULL,
  `occupation` varchar(64) DEFAULT NULL,
  `balance` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额',
  `password` varchar(40) NOT NULL,
  `register_time` datetime NOT NULL COMMENT '注册时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '2D885888', '18622991098', '你好', null, 'Fq3QXJG0TzPhINcKy7HNtt7iiJrJ', '男', '工程师1', '19094.00', 'd3e3aad7256f373a52a9cfb99bb54c98', '2017-10-11 14:50:30', '1507704630', '1');
INSERT INTO `user` VALUES ('4', '27395D92', '18622991099', '家长vbmOe', null, null, null, null, '10000.00', 'd3e3aad7256f373a52a9cfb99bb54c98', '2017-10-16 18:04:48', '1508148288', '1');
INSERT INTO `user` VALUES ('5', '23563592', '18210905679', '家长penRe', null, null, null, null, '10000.00', 'd3e3aad7256f373a52a9cfb99bb54c98', '2017-10-16 18:35:50', '1508150150', '1');
INSERT INTO `user` VALUES ('6', '2BE9BD6D', '18210905678', '家长xboja', null, null, null, null, '10000.00', 'd3e3aad7256f373a52a9cfb99bb54c98', '2017-10-17 11:34:28', '1508211268', '1');
INSERT INTO `user` VALUES ('7', '2E8546AB', '18201430000', '家长mep2b', null, null, null, null, '10000.00', 'd3e3aad7256f373a52a9cfb99bb54c98', '2017-10-18 09:34:29', '1508290469', '1');
INSERT INTO `user` VALUES ('8', '28D48859', '17600115070', '家长zbq2d', null, null, null, null, '10000.00', '7caab0a8b3b888cafc9febed7d08f2d0', '2017-10-21 14:41:25', '1508568085', '1');
INSERT INTO `user` VALUES ('9', '2959DA35', '18201430001', '家长YerEd', null, null, null, null, '0.00', 'd3e3aad7256f373a52a9cfb99bb54c98', '2017-10-25 18:09:34', '1508926174', '1');

-- ----------------------------
-- Table structure for user_token
-- ----------------------------
DROP TABLE IF EXISTS `user_token`;
CREATE TABLE `user_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `token` varchar(32) NOT NULL COMMENT 'token',
  `ip` char(16) NOT NULL,
  `time` datetime NOT NULL COMMENT '登陆时间',
  `agent` varchar(255) DEFAULT NULL COMMENT 'user_agent',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='记录用户登陆情况';

-- ----------------------------
-- Records of user_token
-- ----------------------------
INSERT INTO `user_token` VALUES ('1', '17196', '90e8346cff29185b96ec5aad9e43b310', '192.168.33.1', '2017-10-11 13:45:50', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('2', '1', 'fcb29f21c840c5e0574f16da1a12d504', '192.168.33.1', '2017-10-11 13:48:09', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('3', '1', 'b6efb657d28026aaf0f1f6304a07d5b5', '192.168.33.1', '2017-10-11 13:48:59', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('4', '1', '1d0af882b2ab01349ad195db0fd5e328', '192.168.33.1', '2017-10-11 13:53:46', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('5', '1', '43ccb83b73be6e466778d4a925310c1f', '192.168.33.1', '2017-10-11 13:54:01', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('6', '2', '764d8065d26195db09d96915b0e8a122', '192.168.33.1', '2017-10-11 14:33:05', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('7', '1', 'e957ec03af4c46b5976dd512ef732a06', '192.168.33.1', '2017-10-11 14:44:06', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('8', '1', '8e4ea971887752d22bac961ff0716d8d', '192.168.33.1', '2017-10-11 14:47:39', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('9', '1', '72ba1887b90adb83d6d49c260f5308fe', '192.168.33.1', '2017-10-11 14:49:02', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('10', '1', '3d505df6b30eb7a884d116e067dde93c', '192.168.33.1', '2017-10-11 14:50:07', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('11', '1', '92960a3208f36e55774c31bd9489e2cc', '192.168.33.1', '2017-10-11 14:50:30', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('12', '4', 'e16826357006d8a1fc238537d821728a', '192.168.33.1', '2017-10-16 18:04:48', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('13', '5', 'd43a12d12714374c3e33900c4612b854', '222.128.15.91', '2017-10-16 18:35:50', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36');
INSERT INTO `user_token` VALUES ('14', '5', '34e03716c124a1b65673b80de0350bbb', '61.148.243.252', '2017-10-17 09:22:20', 'Mozilla/5.0 (Linux; U; Android 6.0.1; zh-cn; MI 5 Build/MXB48T) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
INSERT INTO `user_token` VALUES ('15', '5', '704e9ea9b3ce96694d65a37592b165f6', '61.148.243.252', '2017-10-17 09:22:42', 'Mozilla/5.0 (Linux; U; Android 6.0.1; zh-cn; MI 5 Build/MXB48T) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
INSERT INTO `user_token` VALUES ('16', '5', 'd8014f47382f4cd5aaac14da488e70a1', '61.148.243.252', '2017-10-17 09:22:57', 'Mozilla/5.0 (Linux; U; Android 6.0.1; zh-cn; MI 5 Build/MXB48T) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
INSERT INTO `user_token` VALUES ('17', '5', '5c1142797eb840d1841b19e50098f544', '61.148.243.252', '2017-10-17 10:53:15', 'Mozilla/5.0 (Linux; U; Android 6.0.1; zh-cn; MI 5 Build/MXB48T) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
INSERT INTO `user_token` VALUES ('18', '6', '13cfa9798e7d147366ac216675a056b3', '61.148.243.252', '2017-10-17 11:34:28', 'Mozilla/5.0 (Linux; U; Android 6.0.1; zh-cn; MI 5 Build/MXB48T) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
INSERT INTO `user_token` VALUES ('19', '7', '3eda7325daea5a904e58769a7579e476', '222.128.15.91', '2017-10-18 09:34:29', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36');
INSERT INTO `user_token` VALUES ('20', '1', '24765b97f82fa4ea58350d120a6999b3', '192.168.33.1', '2017-10-18 15:32:36', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('21', '1', '4e40c57e3991ff3002de62420e1fae5c', '222.128.15.91', '2017-10-19 10:21:03', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('22', '1', 'c3138e503d0369d37879a932c382aa86', '192.168.33.1', '2017-10-19 10:21:25', 'PostmanRuntime/6.4.0');
INSERT INTO `user_token` VALUES ('23', '8', '9909957cc61b2ceeb419cdb3899a5d51', '222.128.15.91', '2017-10-21 14:41:25', 'dedao/1.0 (iPhone; iOS 11.0.3; Scale/2.00)');
INSERT INTO `user_token` VALUES ('24', '8', 'f324069a2dd571fbde61de2f5b5d2e64', '222.128.15.91', '2017-10-21 15:19:24', 'dedao/1.0 (iPhone; iOS 11.0.3; Scale/2.00)');
INSERT INTO `user_token` VALUES ('25', '8', '987a46a4593450ba60098ff3a6c19cb0', '222.128.15.91', '2017-10-21 15:21:01', 'dedao/1.0 (iPhone; iOS 11.0.3; Scale/2.00)');
INSERT INTO `user_token` VALUES ('26', '8', 'ae5b8029863e5ca936e56e0b6c518922', '222.128.15.91', '2017-10-21 15:36:21', 'dedao/1.0 (iPhone; iOS 11.0.3; Scale/2.00)');
INSERT INTO `user_token` VALUES ('27', '8', '68c48fa103e092a22034c4e2a897ea92', '222.128.15.91', '2017-10-21 17:54:28', 'dedao/1.0 (iPhone; iOS 11.0.3; Scale/2.00)');
INSERT INTO `user_token` VALUES ('28', '1', '854ed267b0cbdbc8697243b0d2dbf2c0', '222.128.15.91', '2017-10-25 14:03:52', 'PostmanRuntime/6.4.1');
INSERT INTO `user_token` VALUES ('29', '1', 'c0167e62b6087bc970605e4798399d50', '222.128.15.91', '2017-10-25 14:04:18', 'PostmanRuntime/6.4.1');
INSERT INTO `user_token` VALUES ('30', '1', '0d224e84e3734fd146e2769cd0e711c5', '192.168.33.1', '2017-10-25 14:04:29', 'PostmanRuntime/6.4.1');
INSERT INTO `user_token` VALUES ('31', '1', 'fc4cae932206f783500ea0aecd18dcc4', '192.168.33.1', '2017-10-25 14:04:52', 'PostmanRuntime/6.4.1');
INSERT INTO `user_token` VALUES ('32', '7', '2fed1c23467d08887d765e0eb4d4bb7e', '222.128.15.91', '2017-10-25 18:08:39', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
INSERT INTO `user_token` VALUES ('33', '7', '6f55ebb8ff5ad5df0ed3a853b2e04683', '222.128.15.91', '2017-10-25 18:08:42', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
INSERT INTO `user_token` VALUES ('34', '7', '2e06e6f8c7945855e6a3f7f01e6c9cdf', '222.128.15.91', '2017-10-25 18:08:50', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
INSERT INTO `user_token` VALUES ('35', '9', '6cdfbaa2172e9608ed873daa7463ed67', '222.128.15.91', '2017-10-25 18:09:34', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
INSERT INTO `user_token` VALUES ('36', '9', 'a377e7f586b40e1edd6431ad6ec431de', '222.128.15.91', '2017-10-25 18:09:46', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
INSERT INTO `user_token` VALUES ('37', '9', '260a836ba84cc5eac9afad70a916f132', '222.128.15.91', '2017-10-25 18:12:40', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
INSERT INTO `user_token` VALUES ('38', '9', 'ea36999f2d07f15151b676cb722dd1bd', '222.128.15.91', '2017-10-25 18:13:18', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
INSERT INTO `user_token` VALUES ('39', '9', '97e5c9ca59b72632c0f9b814f6447f47', '222.128.15.91', '2017-10-25 18:13:21', 'Mozilla/5.0 (Linux; U; Android 5.1; zh-cn; m3 note Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Mobile Safari/537.36');
