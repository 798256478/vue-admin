
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for api
-- ----------------------------
DROP TABLE IF EXISTS `api`;
CREATE TABLE `api` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `path` varchar(200) NOT NULL DEFAULT '' COMMENT '接口路径',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '接口名称',
  `group` varchar(20) NOT NULL DEFAULT '' COMMENT '分组',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='接口';

-- ----------------------------
-- Records of api
-- ----------------------------
BEGIN;
INSERT INTO `api` VALUES (1, 1660269885, 1660269885, 'admin/user/list', '用户列表', '用户管理');
INSERT INTO `api` VALUES (2, 1660269885, 1660269885, 'admin/group/list', '分组列表', '用户组管理');
INSERT INTO `api` VALUES (3, 1660269885, 1660269885, 'admin/group/add', '添加分组', '用户组管理');
INSERT INTO `api` VALUES (4, 1660269885, 1660269885, 'admin/group/edit', '编辑角色', '用户组管理');
INSERT INTO `api` VALUES (7, 1660269885, 1660269885, 'admin/user/add', '新增用户', '用户管理');
INSERT INTO `api` VALUES (8, 1660269885, 1660269885, 'admin/user/edit', '编辑用户', '用户管理');
INSERT INTO `api` VALUES (9, 1660269885, 1660269885, 'admin/user/delete', '删除用户', '用户管理');
INSERT INTO `api` VALUES (10, 1660269885, 1660269885, 'admin/group/delete', '删除分组', '用户组管理');
COMMIT;

-- ----------------------------
-- Table structure for group
-- ----------------------------
DROP TABLE IF EXISTS `group`;
CREATE TABLE `group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '角色名',
  `desc` varchar(100) DEFAULT '' COMMENT '角色描述',
  `api_ids` varchar(1000) DEFAULT NULL COMMENT '接口权限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='角色';

-- ----------------------------
-- Records of group
-- ----------------------------
BEGIN;
INSERT INTO `group` VALUES (2, NULL, NULL, '超级管理员', '超级管理', '');
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分组',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录IP',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 2, 'admin', 'cfda04aec968c2394b4cd83471940841', '935c1b', 1592566020, '172.23.0.1', 1492186163, 1660246567, 'd64c652c-c64c-43ec-a5d8-6f9d9de78c2b');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
